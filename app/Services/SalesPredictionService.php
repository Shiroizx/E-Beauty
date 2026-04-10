<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Review;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SalesPredictionService
{
    /**
     * Default estimated gross margin for skincare products.
     * Used when no cost data is available.
     */
    const DEFAULT_MARGIN_RATE = 0.45;

    /**
     * Only count orders with these payment statuses as "completed sales"
     */
    const VALID_PAYMENT_STATUSES = ['paid'];

    const EXCLUDED_ORDER_STATUSES = ['cancelled'];

    // ─── Aggregation ────────────────────────────────────────

    /**
     * Get daily aggregated sales data for a given date range.
     */
    public function getDailyAggregates(Carbon $from, Carbon $to): Collection
    {
        $rows = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(subtotal - COALESCE(discount_amount, 0)) as revenue'),
                DB::raw('SUM(total) as gross_total'),
                DB::raw('SUM(shipping_cost) as shipping_total')
            )
            ->whereIn('payment_status', self::VALID_PAYMENT_STATUSES)
            ->whereNotIn('status', self::EXCLUDED_ORDER_STATUSES)
            ->whereBetween('created_at', [$from->startOfDay(), $to->endOfDay()])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $itemsPerDay = OrderItem::select(
                DB::raw('DATE(orders.created_at) as date'),
                DB::raw('SUM(order_items.quantity) as items_sold')
            )
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereIn('orders.payment_status', self::VALID_PAYMENT_STATUSES)
            ->whereNotIn('orders.status', self::EXCLUDED_ORDER_STATUSES)
            ->whereBetween('orders.created_at', [$from->startOfDay(), $to->endOfDay()])
            ->groupBy(DB::raw('DATE(orders.created_at)'))
            ->get()
            ->keyBy('date');

        $period = CarbonPeriod::create($from->toDateString(), $to->toDateString());
        $result = collect();

        foreach ($period as $day) {
            $key = $day->toDateString();
            $row = $rows->get($key);
            $itemRow = $itemsPerDay->get($key);

            $result->push([
                'date'         => $key,
                'order_count'  => $row->order_count ?? 0,
                'revenue'      => (float) ($row->revenue ?? 0),
                'gross_total'  => (float) ($row->gross_total ?? 0),
                'shipping'     => (float) ($row->shipping_total ?? 0),
                'items_sold'   => (int) ($itemRow->items_sold ?? 0),
            ]);
        }

        return $result;
    }

    /**
     * Get monthly aggregated data.
     */
    public function getMonthlyAggregates(int $months = 12): Collection
    {
        $from = Carbon::now()->subMonths($months)->startOfMonth();
        $to   = Carbon::now()->endOfMonth();

        $rows = Order::select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(subtotal - COALESCE(discount_amount, 0)) as revenue'),
                DB::raw('SUM(total) as gross_total'),
                DB::raw('SUM(shipping_cost) as shipping_total')
            )
            ->whereIn('payment_status', self::VALID_PAYMENT_STATUSES)
            ->whereNotIn('status', self::EXCLUDED_ORDER_STATUSES)
            ->whereBetween('created_at', [$from, $to])
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"))
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $itemsPerMonth = OrderItem::select(
                DB::raw("DATE_FORMAT(orders.created_at, '%Y-%m') as month"),
                DB::raw('SUM(order_items.quantity) as items_sold')
            )
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereIn('orders.payment_status', self::VALID_PAYMENT_STATUSES)
            ->whereNotIn('orders.status', self::EXCLUDED_ORDER_STATUSES)
            ->whereBetween('orders.created_at', [$from, $to])
            ->groupBy(DB::raw("DATE_FORMAT(orders.created_at, '%Y-%m')"))
            ->get()
            ->keyBy('month');

        $result = collect();
        $cursor = $from->copy();

        while ($cursor->lte($to)) {
            $key = $cursor->format('Y-m');
            $row = $rows->get($key);
            $itemRow = $itemsPerMonth->get($key);

            $result->push([
                'month'        => $key,
                'label'        => $cursor->translatedFormat('M Y'),
                'order_count'  => $row->order_count ?? 0,
                'revenue'      => (float) ($row->revenue ?? 0),
                'gross_total'  => (float) ($row->gross_total ?? 0),
                'shipping'     => (float) ($row->shipping_total ?? 0),
                'items_sold'   => (int) ($itemRow->items_sold ?? 0),
            ]);

            $cursor->addMonth();
        }

        return $result;
    }

    /**
     * Agregasi ulasan disetujui per hari (untuk analitik tren rating).
     */
    public function getDailyReviewAggregates(Carbon $from, Carbon $to): Collection
    {
        $rows = Review::query()
            ->approved()
            ->whereBetween('created_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as review_count'),
                DB::raw('AVG(rating) as avg_rating')
            )
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $period = CarbonPeriod::create($from->toDateString(), $to->toDateString());
        $result = collect();

        foreach ($period as $day) {
            $key = $day->toDateString();
            $row = $rows->get($key);
            $count = $row ? (int) $row->review_count : 0;
            $result->push([
                'date' => $key,
                'review_count' => $count,
                'avg_rating' => $count > 0 ? round((float) $row->avg_rating, 3) : null,
            ]);
        }

        return $result;
    }

    /**
     * Rata-rata rating terbobot jumlah ulasan dalam jendela hari (inklusif hari ini).
     *
     * @return array<int, float|null>
     */
    public function weightedRatingMovingAverage(Collection $dailyReviewRows, int $window = 7): array
    {
        $arr = $dailyReviewRows->values()->all();
        $n = count($arr);
        $out = [];

        for ($i = 0; $i < $n; $i++) {
            $start = max(0, $i - $window + 1);
            $sumW = 0;
            $sum = 0.0;
            for ($j = $start; $j <= $i; $j++) {
                $c = (int) ($arr[$j]['review_count'] ?? 0);
                $a = $arr[$j]['avg_rating'] ?? null;
                if ($c > 0 && $a !== null) {
                    $sum += (float) $a * $c;
                    $sumW += $c;
                }
            }
            $out[] = $sumW > 0 ? round($sum / $sumW, 3) : null;
        }

        return $out;
    }

    /**
     * Ringkasan ulasan disetujui dalam periode: jumlah & rata-rata rating.
     *
     * @return array{count: int, avg_rating: float|null}
     */
    public function getReviewRatingSummary(Carbon $from, Carbon $to): array
    {
        $row = Review::query()
            ->approved()
            ->whereBetween('created_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->selectRaw('COUNT(*) as c, AVG(rating) as a')
            ->first();

        $count = (int) ($row->c ?? 0);
        if ($count === 0) {
            return ['count' => 0, 'avg_rating' => null];
        }

        return [
            'count' => $count,
            'avg_rating' => round((float) $row->a, 2),
        ];
    }

    /**
     * Distribusi jumlah ulasan per nilai bintang (1–5), ulasan disetujui.
     *
     * @return array<int, int>
     */
    public function getRatingDistribution(Carbon $from, Carbon $to): array
    {
        $rows = Review::query()
            ->approved()
            ->whereBetween('created_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->select('rating', DB::raw('COUNT(*) as cnt'))
            ->groupBy('rating')
            ->pluck('cnt', 'rating');

        $out = [];
        for ($star = 1; $star <= 5; $star++) {
            $out[$star] = (int) ($rows->get($star) ?? 0);
        }

        return $out;
    }

    // ─── KPI Summary ────────────────────────────────────────

    /**
     * Calculate KPIs for a period, with comparison to previous period.
     */
    public function getKpiSummary(Carbon $from, Carbon $to, float $marginRate = self::DEFAULT_MARGIN_RATE): array
    {
        $periodDays = $from->diffInDays($to) + 1;
        $prevFrom = $from->copy()->subDays($periodDays);
        $prevTo   = $from->copy()->subDay();

        $current  = $this->periodSummary($from, $to);
        $previous = $this->periodSummary($prevFrom, $prevTo);

        $currentProfit  = $current['revenue'] * $marginRate;
        $previousProfit = $previous['revenue'] * $marginRate;

        return [
            'current' => [
                'revenue'     => $current['revenue'],
                'order_count' => $current['order_count'],
                'items_sold'  => $current['items_sold'],
                'aov'         => $current['order_count'] > 0
                    ? $current['revenue'] / $current['order_count'] : 0,
                'profit'      => $currentProfit,
            ],
            'previous' => [
                'revenue'     => $previous['revenue'],
                'order_count' => $previous['order_count'],
                'items_sold'  => $previous['items_sold'],
                'aov'         => $previous['order_count'] > 0
                    ? $previous['revenue'] / $previous['order_count'] : 0,
                'profit'      => $previousProfit,
            ],
            'growth' => [
                'revenue'     => $this->growthRate($current['revenue'], $previous['revenue']),
                'order_count' => $this->growthRate($current['order_count'], $previous['order_count']),
                'items_sold'  => $this->growthRate($current['items_sold'], $previous['items_sold']),
                'profit'      => $this->growthRate($currentProfit, $previousProfit),
            ],
            'margin_rate' => $marginRate,
            'period_days' => $periodDays,
        ];
    }

    private function periodSummary(Carbon $from, Carbon $to): array
    {
        $order = Order::whereIn('payment_status', self::VALID_PAYMENT_STATUSES)
            ->whereNotIn('status', self::EXCLUDED_ORDER_STATUSES)
            ->whereBetween('created_at', [$from->startOfDay(), $to->endOfDay()])
            ->selectRaw('COUNT(*) as cnt, COALESCE(SUM(subtotal - COALESCE(discount_amount, 0)),0) as rev')
            ->first();

        $items = OrderItem::join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereIn('orders.payment_status', self::VALID_PAYMENT_STATUSES)
            ->whereNotIn('orders.status', self::EXCLUDED_ORDER_STATUSES)
            ->whereBetween('orders.created_at', [$from->startOfDay(), $to->endOfDay()])
            ->sum('order_items.quantity');

        return [
            'revenue'     => (float) $order->rev,
            'order_count' => (int) $order->cnt,
            'items_sold'  => (int) $items,
        ];
    }

    // ─── Prediction Algorithms ──────────────────────────────

    /**
     * Ordinary Least Squares linear regression.
     * Returns [slope, intercept, r_squared].
     */
    public function linearRegression(array $yValues): array
    {
        $n = count($yValues);
        if ($n < 2) {
            return ['slope' => 0, 'intercept' => $yValues[0] ?? 0, 'r_squared' => 0];
        }

        $sumX = 0; $sumY = 0; $sumXY = 0; $sumX2 = 0; $sumY2 = 0;

        for ($i = 0; $i < $n; $i++) {
            $x = $i;
            $y = $yValues[$i];
            $sumX  += $x;
            $sumY  += $y;
            $sumXY += $x * $y;
            $sumX2 += $x * $x;
            $sumY2 += $y * $y;
        }

        $denom = ($n * $sumX2 - $sumX * $sumX);
        if ($denom == 0) {
            return ['slope' => 0, 'intercept' => $sumY / $n, 'r_squared' => 0];
        }

        $slope     = ($n * $sumXY - $sumX * $sumY) / $denom;
        $intercept = ($sumY - $slope * $sumX) / $n;

        // R² (coefficient of determination)
        $ssRes = 0; $ssTot = 0;
        $yMean = $sumY / $n;
        for ($i = 0; $i < $n; $i++) {
            $yPred = $slope * $i + $intercept;
            $ssRes += ($yValues[$i] - $yPred) ** 2;
            $ssTot += ($yValues[$i] - $yMean) ** 2;
        }
        $rSquared = $ssTot > 0 ? 1 - ($ssRes / $ssTot) : 0;

        return [
            'slope'     => $slope,
            'intercept' => $intercept,
            'r_squared' => round($rSquared, 4),
        ];
    }

    /**
     * Simple Moving Average.
     */
    public function movingAverage(array $values, int $window = 7): array
    {
        $result = [];
        $n = count($values);

        for ($i = 0; $i < $n; $i++) {
            if ($i < $window - 1) {
                $result[] = null;
                continue;
            }
            $sum = 0;
            for ($j = $i - $window + 1; $j <= $i; $j++) {
                $sum += $values[$j];
            }
            $result[] = round($sum / $window, 2);
        }

        return $result;
    }

    /**
     * Exponential smoothing.
     */
    public function exponentialSmoothing(array $values, float $alpha = 0.3): array
    {
        if (empty($values)) return [];

        $result = [$values[0]];
        for ($i = 1; $i < count($values); $i++) {
            $result[] = round($alpha * $values[$i] + (1 - $alpha) * $result[$i - 1], 2);
        }

        return $result;
    }

    /**
     * Forecast future values using linear regression + seasonal decomposition.
     */
    public function forecast(array $historicalValues, int $periodsAhead = 3): array
    {
        $n = count($historicalValues);
        if ($n < 2) {
            return array_fill(0, $periodsAhead, $historicalValues[0] ?? 0);
        }

        $reg = $this->linearRegression($historicalValues);

        // Detrend to find seasonal residuals
        $residuals = [];
        for ($i = 0; $i < $n; $i++) {
            $trend = $reg['slope'] * $i + $reg['intercept'];
            $residuals[] = $trend > 0 ? $historicalValues[$i] / $trend : 1;
        }

        // Average seasonal factor (use last cycle or overall)
        $avgResidual = count($residuals) > 0 ? array_sum($residuals) / count($residuals) : 1;

        $predictions = [];
        for ($i = 0; $i < $periodsAhead; $i++) {
            $idx = $n + $i;
            $trend = $reg['slope'] * $idx + $reg['intercept'];
            $predicted = max(0, $trend * $avgResidual);
            $predictions[] = round($predicted, 2);
        }

        return $predictions;
    }

    /**
     * Full prediction package for a time series.
     */
    public function analyzeSeries(array $values, int $forecastPeriods = 3, int $maWindow = 7): array
    {
        $regression   = $this->linearRegression($values);
        $ma           = $this->movingAverage($values, $maWindow);
        $ema          = $this->exponentialSmoothing($values, 0.3);
        $predictions  = $this->forecast($values, $forecastPeriods);

        $trendLine = [];
        for ($i = 0; $i < count($values); $i++) {
            $trendLine[] = round($regression['slope'] * $i + $regression['intercept'], 2);
        }

        return [
            'regression'  => $regression,
            'trend_line'  => $trendLine,
            'moving_avg'  => $ma,
            'ema'         => $ema,
            'predictions' => $predictions,
        ];
    }

    // ─── Top Products ───────────────────────────────────────

    /**
     * Top selling products in a period.
     */
    public function getTopProducts(Carbon $from, Carbon $to, int $limit = 10): Collection
    {
        return OrderItem::select(
                'order_items.product_id',
                'order_items.product_name',
                DB::raw('SUM(order_items.quantity) as total_qty'),
                DB::raw('SUM(order_items.line_total) as total_revenue'),
                DB::raw('COUNT(DISTINCT order_items.order_id) as order_count')
            )
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereIn('orders.payment_status', self::VALID_PAYMENT_STATUSES)
            ->whereNotIn('orders.status', self::EXCLUDED_ORDER_STATUSES)
            ->whereBetween('orders.created_at', [$from->startOfDay(), $to->endOfDay()])
            ->groupBy('order_items.product_id', 'order_items.product_name')
            ->orderByDesc('total_revenue')
            ->limit($limit)
            ->get();
    }

    /**
     * Revenue by payment method.
     */
    public function getPaymentMethodBreakdown(Carbon $from, Carbon $to): Collection
    {
        return Order::select(
                'payment_method',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total) as total_amount')
            )
            ->whereIn('payment_status', self::VALID_PAYMENT_STATUSES)
            ->whereNotIn('status', self::EXCLUDED_ORDER_STATUSES)
            ->whereBetween('created_at', [$from->startOfDay(), $to->endOfDay()])
            ->groupBy('payment_method')
            ->orderByDesc('total_amount')
            ->get();
    }

    /**
     * Order status distribution.
     */
    public function getStatusDistribution(Carbon $from, Carbon $to): Collection
    {
        return Order::select(
                'status',
                DB::raw('COUNT(*) as count')
            )
            ->whereBetween('created_at', [$from->startOfDay(), $to->endOfDay()])
            ->groupBy('status')
            ->get();
    }

    // ─── Helpers ────────────────────────────────────────────

    public function growthRate(float $current, float $previous): ?float
    {
        if ($previous == 0) {
            return $current > 0 ? 100.0 : 0.0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }

    public function formatMoney(float $value): string
    {
        return 'Rp ' . number_format($value, 0, ',', '.');
    }
}
