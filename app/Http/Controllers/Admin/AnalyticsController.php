<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SalesPredictionService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    protected SalesPredictionService $prediction;

    public function __construct(SalesPredictionService $prediction)
    {
        $this->prediction = $prediction;
    }

    public function index(Request $request)
    {
        return $this->renderAnalytics($request, false);
    }

    /**
     * Halaman analitik yang sama; rute berbeda agar nav "Analitik Tren" tetap aktif (tanpa redirect ke admin.analytics).
     */
    public function trend(Request $request)
    {
        return $this->renderAnalytics($request, true);
    }

    private function renderAnalytics(Request $request, bool $isTrendRoute)
    {
        $defaultMode = $isTrendRoute ? 'tren' : 'biasa';
        $analyticsMode = in_array($request->query('mode'), ['biasa', 'tren'], true)
            ? $request->query('mode')
            : $defaultMode;
        $scrollToTrendSection = $isTrendRoute && $analyticsMode === 'tren';

        $range = $request->get('range', '30');
        $marginRate = (float) $request->get('margin', 45) / 100;
        $marginRate = max(0.01, min(0.99, $marginRate));

        [$from, $to] = $this->resolveDateRange($range, $request);

        $kpi = $this->prediction->getKpiSummary($from, $to, $marginRate);

        $periodDays = $from->diffInDays($to) + 1;
        $prevFrom = $from->copy()->subDays($periodDays);
        $prevTo = $from->copy()->subDay();
        $reviewCur = $this->prediction->getReviewRatingSummary($from, $to);
        $reviewPrev = $this->prediction->getReviewRatingSummary($prevFrom, $prevTo);
        $reviewKpi = [
            'count' => $reviewCur['count'],
            'count_growth' => $this->prediction->growthRate((float) $reviewCur['count'], (float) $reviewPrev['count']),
            'avg_rating' => $reviewCur['avg_rating'],
            'prev_avg_rating' => $reviewPrev['avg_rating'],
        ];
        $ratingDistribution = $this->prediction->getRatingDistribution($from, $to);

        $dailyData = $this->prediction->getDailyAggregates($from, $to);

        $revenueValues  = $dailyData->pluck('revenue')->toArray();
        $orderValues    = $dailyData->pluck('order_count')->toArray();
        $itemsValues    = $dailyData->pluck('items_sold')->toArray();
        $profitValues   = array_map(fn($v) => round($v * $marginRate, 2), $revenueValues);

        $forecastDays = max(3, min(14, (int) ceil(count($revenueValues) * 0.2)));

        $revenueAnalysis = $this->prediction->analyzeSeries($revenueValues, $forecastDays);
        $orderAnalysis   = $this->prediction->analyzeSeries($orderValues, $forecastDays);
        $itemsAnalysis   = $this->prediction->analyzeSeries($itemsValues, $forecastDays);
        $profitAnalysis  = $this->prediction->analyzeSeries($profitValues, $forecastDays);

        $reviewDaily = $this->prediction->getDailyReviewAggregates($from, $to);
        $reviewCountValues = $reviewDaily->pluck('review_count')->map(fn ($v) => (int) $v)->toArray();
        $reviewCountAnalysis = $this->prediction->analyzeSeries($reviewCountValues, $forecastDays);
        $ratingDailyPoints = $reviewDaily->map(function ($row) {
            return ((int) $row['review_count']) > 0 ? round((float) $row['avg_rating'], 2) : null;
        })->values()->toArray();
        $ratingWeightedMa = $this->prediction->weightedRatingMovingAverage($reviewDaily, 7);

        $labels = $dailyData->pluck('date')->toArray();
        $forecastLabels = [];
        $lastDate = Carbon::parse(end($labels) ?: now());
        for ($i = 1; $i <= $forecastDays; $i++) {
            $forecastLabels[] = $lastDate->copy()->addDays($i)->toDateString();
        }

        $topProducts = $this->prediction->getTopProducts($from, $to, 10);
        $paymentMethods = $this->prediction->getPaymentMethodBreakdown($from, $to);
        $statusDist = $this->prediction->getStatusDistribution($from, $to);

        $monthlyData = $this->prediction->getMonthlyAggregates(12);
        $monthlyRevenue = $monthlyData->pluck('revenue')->toArray();
        $monthlyLabels  = $monthlyData->pluck('label')->toArray();
        $monthlyForecast = $this->prediction->forecast($monthlyRevenue, 3);
        $monthlyRegression = $this->prediction->linearRegression($monthlyRevenue);

        $monthlyForecastLabels = [];
        $lastMonth = Carbon::now()->endOfMonth();
        for ($i = 1; $i <= 3; $i++) {
            $monthlyForecastLabels[] = $lastMonth->copy()->addMonths($i)->translatedFormat('M Y');
        }

        return view('admin.analytics.index', compact(
            'range', 'from', 'to', 'marginRate',
            'kpi',
            'labels', 'forecastLabels',
            'dailyData',
            'revenueValues', 'revenueAnalysis',
            'orderValues', 'orderAnalysis',
            'itemsValues', 'itemsAnalysis',
            'profitValues', 'profitAnalysis',
            'reviewCountValues', 'reviewCountAnalysis',
            'ratingDailyPoints', 'ratingWeightedMa',
            'reviewKpi', 'ratingDistribution',
            'topProducts', 'paymentMethods', 'statusDist',
            'monthlyLabels', 'monthlyRevenue', 'monthlyForecast',
            'monthlyForecastLabels', 'monthlyRegression',
            'forecastDays',
            'analyticsMode',
            'scrollToTrendSection'
        ));
    }

    private function resolveDateRange(string $range, Request $request): array
    {
        return match ($range) {
            '7'     => [Carbon::now()->subDays(6)->startOfDay(), Carbon::now()->endOfDay()],
            '30'    => [Carbon::now()->subDays(29)->startOfDay(), Carbon::now()->endOfDay()],
            '90'    => [Carbon::now()->subDays(89)->startOfDay(), Carbon::now()->endOfDay()],
            '365'   => [Carbon::now()->subDays(364)->startOfDay(), Carbon::now()->endOfDay()],
            'custom' => [
                Carbon::parse($request->get('date_from', now()->subDays(29)))->startOfDay(),
                Carbon::parse($request->get('date_to', now()))->endOfDay(),
            ],
            default => [Carbon::now()->subDays(29)->startOfDay(), Carbon::now()->endOfDay()],
        };
    }
}
