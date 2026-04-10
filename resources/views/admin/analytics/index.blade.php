@extends('layouts.admin')

@section('title', 'Analitik & Prediksi — Admin Skinbae.ID')
@section('page_title')
    {{ $analyticsMode === 'tren' ? 'Analitik tren' : 'Analitik biasa' }}
@endsection

@push('styles')
<style>
    .chart-container { position: relative; width: 100%; }
    .kpi-up { color: #059669; }
    .kpi-down { color: #dc2626; }
    .kpi-neutral { color: #9ca3af; }
</style>
@endpush

@section('content')
<div class="space-y-5">

    {{-- Header + Filters --}}
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div class="min-w-0 flex-1 space-y-3">
            <div>
                <h2 class="text-xl font-bold tracking-tight text-gray-900">Analitik & Prediksi Penjualan</h2>
                <p class="mt-0.5 text-sm text-gray-400">Pilih mode tampilan — filter periode berlaku untuk keduanya</p>
            </div>
            <div class="inline-flex rounded-xl bg-white p-1 shadow-card ring-1 ring-stone-100">
                <a href="{{ request()->fullUrlWithQuery(['mode' => 'biasa']) }}"
                   class="rounded-lg px-3 py-2 text-xs font-semibold transition {{ $analyticsMode === 'biasa' ? 'bg-gray-800 text-white shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                    <i class="fas fa-table-columns mr-1 text-[10px]"></i> Analitik biasa
                </a>
                <a href="{{ request()->fullUrlWithQuery(['mode' => 'tren']) }}"
                   class="rounded-lg px-3 py-2 text-xs font-semibold transition {{ $analyticsMode === 'tren' ? 'bg-gray-800 text-white shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                    <i class="fas fa-chart-line mr-1 text-[10px]"></i> Analitik tren
                </a>
            </div>
        </div>

        <form method="GET" action="{{ url()->current() }}" class="flex flex-wrap items-end gap-2">
            <input type="hidden" name="mode" value="{{ $analyticsMode }}">
            {{-- Period Selector --}}
            <div class="flex items-center rounded-xl bg-white p-1 shadow-card ring-1 ring-stone-100">
                @foreach(['7' => '7H', '30' => '30H', '90' => '90H', '365' => '1Th'] as $val => $label)
                    <button type="submit" name="range" value="{{ $val }}"
                            class="rounded-lg px-3 py-1.5 text-xs font-semibold transition {{ $range == $val ? 'bg-gray-800 text-white shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            {{-- Custom Date --}}
            <div x-data="{ showCustom: {{ $range === 'custom' ? 'true' : 'false' }} }" class="flex items-end gap-2">
                <button type="button" @click="showCustom = !showCustom"
                        class="rounded-xl bg-white px-3 py-[7px] text-xs font-semibold text-gray-500 shadow-card ring-1 ring-stone-100 transition hover:text-gray-700 {{ $range === 'custom' ? 'ring-brand-300 text-brand-600' : '' }}">
                    <i class="fas fa-calendar-alt mr-1 text-[10px]"></i> Custom
                </button>
                <template x-if="showCustom">
                    <div class="flex items-end gap-1.5">
                        <input type="date" name="date_from" value="{{ $from->toDateString() }}"
                               class="rounded-xl border border-stone-200 px-2.5 py-[6px] text-xs text-gray-700 focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-100">
                        <input type="date" name="date_to" value="{{ $to->toDateString() }}"
                               class="rounded-xl border border-stone-200 px-2.5 py-[6px] text-xs text-gray-700 focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-100">
                        <button type="submit" name="range" value="custom"
                                class="rounded-xl bg-brand-500 px-3 py-[7px] text-xs font-bold text-white shadow-sm transition hover:bg-brand-600">
                            <i class="fas fa-search text-[9px]"></i>
                        </button>
                    </div>
                </template>
            </div>

            {{-- Margin Rate --}}
            <div class="flex items-center gap-1.5">
                <label class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Margin</label>
                <input type="number" name="margin" value="{{ round($marginRate * 100) }}" min="1" max="99"
                       class="w-14 rounded-xl border border-stone-200 px-2 py-[6px] text-center text-xs font-bold text-gray-700 focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-100">
                <span class="text-xs text-gray-400">%</span>
            </div>
        </form>
    </div>

    @if($analyticsMode === 'biasa')
    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @php
            $kpiCards = [
                ['label' => 'Total Pendapatan', 'key' => 'revenue', 'icon' => 'fa-coins', 'color' => 'blue', 'format' => 'money'],
                ['label' => 'Jumlah Pesanan', 'key' => 'order_count', 'icon' => 'fa-receipt', 'color' => 'violet', 'format' => 'number'],
                ['label' => 'Produk Terjual', 'key' => 'items_sold', 'icon' => 'fa-shopping-bag', 'color' => 'amber', 'format' => 'number'],
                ['label' => 'Estimasi Laba', 'key' => 'profit', 'icon' => 'fa-chart-line', 'color' => 'emerald', 'format' => 'money'],
            ];
        @endphp

        @foreach($kpiCards as $card)
            @php
                $value = $kpi['current'][$card['key']];
                $growth = $kpi['growth'][$card['key']];
                $isUp = $growth > 0;
                $isDown = $growth < 0;
                $colorMap = [
                    'blue' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-500', 'circle' => 'bg-blue-50'],
                    'violet' => ['bg' => 'bg-violet-50', 'text' => 'text-violet-500', 'circle' => 'bg-violet-50'],
                    'amber' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-500', 'circle' => 'bg-amber-50'],
                    'emerald' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-500', 'circle' => 'bg-emerald-50'],
                ];
                $c = $colorMap[$card['color']];
            @endphp
            <div class="group relative overflow-hidden rounded-2xl bg-white p-5 shadow-card transition-shadow hover:shadow-card-hover">
                <div class="absolute -right-3 -top-3 h-20 w-20 rounded-full {{ $c['circle'] }} transition-transform group-hover:scale-110"></div>
                <div class="relative flex items-start justify-between">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">{{ $card['label'] }}</p>
                        <p class="mt-2 text-2xl font-extrabold tracking-tight text-gray-900">
                            @if($card['format'] === 'money')
                                {{ 'Rp ' . number_format($value, 0, ',', '.') }}
                            @else
                                {{ number_format($value, 0, ',', '.') }}
                            @endif
                        </p>
                        <div class="mt-1.5 flex items-center gap-1">
                            @if($isUp)
                                <span class="kpi-up text-[11px] font-bold"><i class="fas fa-arrow-up text-[8px]"></i> {{ $growth }}%</span>
                            @elseif($isDown)
                                <span class="kpi-down text-[11px] font-bold"><i class="fas fa-arrow-down text-[8px]"></i> {{ abs($growth) }}%</span>
                            @else
                                <span class="kpi-neutral text-[11px] font-bold">— 0%</span>
                            @endif
                            <span class="text-[10px] text-gray-400">vs periode lalu</span>
                        </div>
                    </div>
                    <span class="relative flex h-10 w-10 items-center justify-center rounded-xl {{ $c['bg'] }} {{ $c['text'] }}">
                        <i class="fas {{ $card['icon'] }} text-base"></i>
                    </span>
                </div>
                <div class="mt-3 h-1 w-10 rounded-full bg-gradient-to-r from-{{ $card['color'] }}-400 to-{{ $card['color'] }}-200"></div>
            </div>
        @endforeach
    </div>

    @php
        $reviewCountGrowth = $reviewKpi['count_growth'];
        $avgDelta = null;
        if ($reviewKpi['avg_rating'] !== null && $reviewKpi['prev_avg_rating'] !== null) {
            $avgDelta = round($reviewKpi['avg_rating'] - $reviewKpi['prev_avg_rating'], 2);
        }
    @endphp

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div class="group relative overflow-hidden rounded-2xl bg-white p-5 shadow-card transition-shadow hover:shadow-card-hover">
            <div class="absolute -right-3 -top-3 h-20 w-20 rounded-full bg-amber-50 transition-transform group-hover:scale-110"></div>
            <div class="relative flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Rata-rata rating</p>
                    <p class="mt-2 text-2xl font-extrabold tracking-tight text-gray-900">
                        {{ $reviewKpi['avg_rating'] !== null ? number_format($reviewKpi['avg_rating'], 2).' / 5' : '—' }}
                    </p>
                    <div class="mt-1.5 flex items-center gap-1">
                        @if($avgDelta !== null)
                            @if($avgDelta > 0)
                                <span class="kpi-up text-[11px] font-bold"><i class="fas fa-arrow-up text-[8px]"></i> {{ $avgDelta }}</span>
                            @elseif($avgDelta < 0)
                                <span class="kpi-down text-[11px] font-bold"><i class="fas fa-arrow-down text-[8px]"></i> {{ abs($avgDelta) }}</span>
                            @else
                                <span class="kpi-neutral text-[11px] font-bold">±0</span>
                            @endif
                            <span class="text-[10px] text-gray-400">vs rata-rata periode lalu</span>
                        @else
                            <span class="text-[10px] text-gray-400">Ulasan disetujui dalam periode</span>
                        @endif
                    </div>
                </div>
                <span class="relative flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-500">
                    <i class="fas fa-star text-base"></i>
                </span>
            </div>
            <div class="mt-3 h-1 w-10 rounded-full bg-gradient-to-r from-amber-400 to-amber-200"></div>
        </div>

        <div class="group relative overflow-hidden rounded-2xl bg-white p-5 shadow-card transition-shadow hover:shadow-card-hover">
            <div class="absolute -right-3 -top-3 h-20 w-20 rounded-full bg-rose-50 transition-transform group-hover:scale-110"></div>
            <div class="relative flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Jumlah ulasan</p>
                    <p class="mt-2 text-2xl font-extrabold tracking-tight text-gray-900">{{ number_format($reviewKpi['count']) }}</p>
                    <div class="mt-1.5 flex items-center gap-1">
                        @if($reviewCountGrowth > 0)
                            <span class="kpi-up text-[11px] font-bold"><i class="fas fa-arrow-up text-[8px]"></i> {{ $reviewCountGrowth }}%</span>
                        @elseif($reviewCountGrowth < 0)
                            <span class="kpi-down text-[11px] font-bold"><i class="fas fa-arrow-down text-[8px]"></i> {{ abs($reviewCountGrowth) }}%</span>
                        @else
                            <span class="kpi-neutral text-[11px] font-bold">— 0%</span>
                        @endif
                        <span class="text-[10px] text-gray-400">vs periode lalu</span>
                    </div>
                </div>
                <span class="relative flex h-10 w-10 items-center justify-center rounded-xl bg-rose-50 text-rose-500">
                    <i class="fas fa-comments text-base"></i>
                </span>
            </div>
            <div class="mt-3 h-1 w-10 rounded-full bg-gradient-to-r from-rose-400 to-rose-200"></div>
        </div>
    </div>
    @endif

    @if($analyticsMode === 'tren')
    {{-- Main Charts (anchor untuk scroll dari menu) --}}
    <div id="analitik-tren" class="grid scroll-mt-24 grid-cols-1 gap-5 xl:grid-cols-2">

        {{-- Revenue Trend + Prediction --}}
        <div class="rounded-2xl bg-white p-5 shadow-card">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-gray-800">Tren Pendapatan</h3>
                    <p class="text-[11px] text-gray-400">
                        R² = {{ $revenueAnalysis['regression']['r_squared'] }}
                        &middot; Slope: {{ number_format($revenueAnalysis['regression']['slope'], 0) }}/hari
                    </p>
                </div>
                <span class="rounded-lg bg-blue-50 px-2 py-0.5 text-[10px] font-bold text-blue-600">+ {{ $forecastDays }} hari prediksi</span>
            </div>
            <div class="chart-container" style="height:280px">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        {{-- Order Count Trend --}}
        <div class="rounded-2xl bg-white p-5 shadow-card">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-gray-800">Jumlah Pesanan</h3>
                    <p class="text-[11px] text-gray-400">
                        R² = {{ $orderAnalysis['regression']['r_squared'] }}
                        &middot; Slope: {{ number_format($orderAnalysis['regression']['slope'], 2) }}/hari
                    </p>
                </div>
                <span class="rounded-lg bg-violet-50 px-2 py-0.5 text-[10px] font-bold text-violet-600">+ {{ $forecastDays }} hari prediksi</span>
            </div>
            <div class="chart-container" style="height:280px">
                <canvas id="orderChart"></canvas>
            </div>
        </div>

        {{-- Profit Estimation --}}
        <div class="rounded-2xl bg-white p-5 shadow-card">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-gray-800">Estimasi Laba</h3>
                    <p class="text-[11px] text-gray-400">
                        Margin {{ round($marginRate * 100) }}%
                        &middot; R² = {{ $profitAnalysis['regression']['r_squared'] }}
                    </p>
                </div>
                <span class="rounded-lg bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-600">+ {{ $forecastDays }} hari prediksi</span>
            </div>
            <div class="chart-container" style="height:280px">
                <canvas id="profitChart"></canvas>
            </div>
        </div>

        {{-- Items Sold --}}
        <div class="rounded-2xl bg-white p-5 shadow-card">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-gray-800">Produk Terjual</h3>
                    <p class="text-[11px] text-gray-400">
                        R² = {{ $itemsAnalysis['regression']['r_squared'] }}
                        &middot; Slope: {{ number_format($itemsAnalysis['regression']['slope'], 2) }}/hari
                    </p>
                </div>
                <span class="rounded-lg bg-amber-50 px-2 py-0.5 text-[10px] font-bold text-amber-600">+ {{ $forecastDays }} hari prediksi</span>
            </div>
            <div class="chart-container" style="height:280px">
                <canvas id="itemsChart"></canvas>
            </div>
        </div>

        <div class="col-span-full pt-1">
            <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-gray-400">Ulasan &amp; rating</p>
            <p class="mt-0.5 text-xs text-gray-500">Hanya ulasan yang sudah disetujui (moderasi).</p>
        </div>

        {{-- Review volume trend --}}
        <div class="rounded-2xl bg-white p-5 shadow-card">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-gray-800">Volume ulasan per hari</h3>
                    <p class="text-[11px] text-gray-400">
                        R² = {{ $reviewCountAnalysis['regression']['r_squared'] }}
                        &middot; Slope: {{ number_format($reviewCountAnalysis['regression']['slope'], 3) }}/hari
                    </p>
                </div>
                <span class="rounded-lg bg-rose-50 px-2 py-0.5 text-[10px] font-bold text-rose-600">+ {{ $forecastDays }} hari prediksi</span>
            </div>
            <div class="chart-container" style="height:280px">
                <canvas id="reviewCountChart"></canvas>
            </div>
        </div>

        {{-- Rating trend --}}
        <div class="rounded-2xl bg-white p-5 shadow-card">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-gray-800">Tren rating (1–5)</h3>
                    <p class="text-[11px] text-gray-400">
                        Rata-rata harian (jika ada ulasan) &middot; Garis ungu = rata-rata bergerak 7 hari terbobot jumlah ulasan
                    </p>
                </div>
            </div>
            <div class="chart-container" style="height:280px">
                <canvas id="ratingTrendChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Monthly Forecast --}}
    <div class="rounded-2xl bg-white p-5 shadow-card">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-gray-800">Prediksi Pendapatan Bulanan</h3>
                <p class="text-[11px] text-gray-400">
                    12 bulan terakhir + 3 bulan prediksi &middot; R² = {{ $monthlyRegression['r_squared'] }}
                </p>
            </div>
            <div class="flex items-center gap-4 text-[11px]">
                <span class="flex items-center gap-1.5"><span class="h-2 w-5 rounded-full bg-brand-400"></span> Aktual</span>
                <span class="flex items-center gap-1.5"><span class="h-2 w-5 rounded-full bg-brand-200 border border-dashed border-brand-400"></span> Prediksi</span>
            </div>
        </div>
        <div class="chart-container" style="height:300px">
            <canvas id="monthlyChart"></canvas>
        </div>
    </div>
    @endif

    @if($analyticsMode === 'biasa')
    {{-- Bottom Section --}}
    <div class="grid grid-cols-1 gap-5 xl:grid-cols-3">

        {{-- Top Products --}}
        <div class="overflow-hidden rounded-2xl bg-white shadow-card xl:col-span-2">
            <div class="flex items-center gap-2.5 border-b border-stone-100 px-5 py-4">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-50 text-brand-500">
                    <i class="fas fa-trophy text-sm"></i>
                </span>
                <h3 class="text-sm font-bold text-gray-800">Produk Terlaris</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-stone-50 text-left">
                            <th class="py-2.5 pl-5 pr-3 text-[10px] font-semibold uppercase tracking-wider text-gray-400">#</th>
                            <th class="px-3 py-2.5 text-[10px] font-semibold uppercase tracking-wider text-gray-400">Produk</th>
                            <th class="px-3 py-2.5 text-right text-[10px] font-semibold uppercase tracking-wider text-gray-400">Qty</th>
                            <th class="px-3 py-2.5 text-right text-[10px] font-semibold uppercase tracking-wider text-gray-400">Pendapatan</th>
                            <th class="py-2.5 pl-3 pr-5 text-right text-[10px] font-semibold uppercase tracking-wider text-gray-400">Order</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-50">
                        @forelse($topProducts as $i => $product)
                            <tr class="transition hover:bg-stone-50/60">
                                <td class="py-2.5 pl-5 pr-3">
                                    @if($i < 3)
                                        <span class="flex h-6 w-6 items-center justify-center rounded-full {{ $i === 0 ? 'bg-amber-100 text-amber-700' : ($i === 1 ? 'bg-gray-100 text-gray-600' : 'bg-orange-50 text-orange-600') }} text-[10px] font-bold">{{ $i + 1 }}</span>
                                    @else
                                        <span class="pl-2 text-xs text-gray-400">{{ $i + 1 }}</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5 font-medium text-gray-800 max-w-[250px] truncate">{{ $product->product_name }}</td>
                                <td class="px-3 py-2.5 text-right font-bold text-gray-700">{{ number_format($product->total_qty) }}</td>
                                <td class="px-3 py-2.5 text-right font-semibold text-gray-700">Rp {{ number_format($product->total_revenue, 0, ',', '.') }}</td>
                                <td class="py-2.5 pl-3 pr-5 text-right text-gray-500">{{ $product->order_count }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-10 text-center text-sm text-gray-400">Belum ada data penjualan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Payment & Status Charts --}}
        <div class="space-y-5">
            <div class="rounded-2xl bg-white p-5 shadow-card">
                <h3 class="mb-3 text-sm font-bold text-gray-800">Metode Pembayaran</h3>
                <div class="chart-container" style="height:180px">
                    <canvas id="paymentChart"></canvas>
                </div>
            </div>

            <div class="rounded-2xl bg-white p-5 shadow-card">
                <h3 class="mb-3 text-sm font-bold text-gray-800">Distribusi Status</h3>
                <div class="chart-container" style="height:180px">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>

            <div class="rounded-2xl bg-white p-5 shadow-card">
                <h3 class="mb-3 text-sm font-bold text-gray-800">Distribusi rating ulasan</h3>
                <p class="mb-2 text-[10px] text-gray-400">Ulasan disetujui · periode filter</p>
                <div class="chart-container" style="height:200px">
                    <canvas id="ratingDistChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Prediction Accuracy Info --}}
    <div class="rounded-2xl border border-stone-200 bg-gradient-to-r from-stone-50 to-white p-5">
        <div class="flex items-start gap-3">
            <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-500">
                <i class="fas fa-info-circle text-sm"></i>
            </span>
            <div>
                <h4 class="text-sm font-bold text-gray-800">Tentang Prediksi</h4>
                <p class="mt-1 text-xs leading-relaxed text-gray-500">
                    Prediksi menggunakan <strong>Ordinary Least Squares (OLS) Linear Regression</strong> dengan dekomposisi musiman.
                    Nilai <strong>R²</strong> menunjukkan seberapa baik model menjelaskan variasi data (0 = buruk, 1 = sempurna).
                    <strong>Moving Average</strong> (garis putus-putus) membantu memperhalus fluktuasi harian.
                    Estimasi laba menggunakan margin <strong>{{ round($marginRate * 100) }}%</strong> — sesuaikan dengan margin aktual bisnis Anda untuk akurasi lebih tinggi.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if($scrollToTrendSection ?? false)
<script>
document.addEventListener('DOMContentLoaded', function () {
    requestAnimationFrame(function () {
        var el = document.getElementById('analitik-tren');
        if (el) {
            el.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
});
</script>
@endif
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const brandPink = '#f4518c';

    Chart.defaults.font.family = 'Inter, system-ui, sans-serif';
    Chart.defaults.font.size = 11;
    Chart.defaults.color = '#9ca3af';
    Chart.defaults.plugins.legend.display = false;
    Chart.defaults.elements.point.radius = 0;
    Chart.defaults.elements.point.hoverRadius = 4;
    Chart.defaults.elements.line.tension = 0.35;

    function moneyTooltip(ctx) {
        const val = ctx.parsed.y;
        return 'Rp ' + val.toLocaleString('id-ID');
    }

    const baseOpts = (isMoney) => ({
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: {
            tooltip: {
                backgroundColor: '#1e1b2e',
                titleColor: '#fff',
                bodyColor: '#e5e5e5',
                cornerRadius: 10,
                padding: 10,
                callbacks: isMoney ? { label: moneyTooltip } : {}
            },
            legend: { display: false }
        },
        scales: {
            x: {
                grid: { display: false },
                ticks: { maxTicksLimit: 10, font: { size: 10 } },
            },
            y: {
                grid: { color: '#f3f4f6' },
                ticks: {
                    font: { size: 10 },
                    callback: isMoney
                        ? (v) => 'Rp ' + (v >= 1000000 ? (v/1000000).toFixed(1) + 'jt' : (v >= 1000 ? (v/1000).toFixed(0) + 'rb' : v))
                        : undefined
                },
                beginAtZero: true,
            }
        }
    });

    if (document.getElementById('revenueChart')) {
        function shortDate(d) {
            const dt = new Date(d);
            return dt.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
        }

        const allLabels = @json($labels);
        const forecastLabels = @json($forecastLabels);
        const combinedLabels = [...allLabels, ...forecastLabels];
        const displayLabels = combinedLabels.map(shortDate);

        function buildDatasets(actual, analysis, forecastVals, color, label) {
            const n = actual.length;
            const padForecast = new Array(n).fill(null);
            const bridgedForecast = [...padForecast.slice(0, -1), actual[actual.length - 1], ...forecastVals];

            return [
                {
                    label: label,
                    data: [...actual, ...new Array(forecastVals.length).fill(null)],
                    borderColor: color,
                    backgroundColor: color + '18',
                    fill: true,
                    borderWidth: 2,
                },
                {
                    label: 'Moving Avg',
                    data: [...analysis.moving_avg, ...new Array(forecastVals.length).fill(null)],
                    borderColor: color + '60',
                    borderDash: [4, 4],
                    borderWidth: 1.5,
                    fill: false,
                    pointRadius: 0,
                },
                {
                    label: 'Tren',
                    data: [...analysis.trend_line, ...new Array(forecastVals.length).fill(null)],
                    borderColor: '#94a3b8',
                    borderDash: [6, 3],
                    borderWidth: 1,
                    fill: false,
                    pointRadius: 0,
                },
                {
                    label: 'Prediksi',
                    data: bridgedForecast,
                    borderColor: color,
                    borderDash: [5, 5],
                    backgroundColor: color + '0a',
                    fill: true,
                    borderWidth: 2,
                    pointRadius: 3,
                    pointBackgroundColor: color,
                }
            ];
        }

        new Chart(document.getElementById('revenueChart'), {
            type: 'line',
            data: {
                labels: displayLabels,
                datasets: buildDatasets(
                    @json($revenueValues),
                    @json($revenueAnalysis),
                    @json($revenueAnalysis['predictions']),
                    '#3b82f6', 'Pendapatan'
                )
            },
            options: baseOpts(true)
        });

        new Chart(document.getElementById('orderChart'), {
            type: 'line',
            data: {
                labels: displayLabels,
                datasets: buildDatasets(
                    @json($orderValues),
                    @json($orderAnalysis),
                    @json($orderAnalysis['predictions']),
                    '#8b5cf6', 'Pesanan'
                )
            },
            options: baseOpts(false)
        });

        new Chart(document.getElementById('profitChart'), {
            type: 'line',
            data: {
                labels: displayLabels,
                datasets: buildDatasets(
                    @json($profitValues),
                    @json($profitAnalysis),
                    @json($profitAnalysis['predictions']),
                    '#059669', 'Laba'
                )
            },
            options: baseOpts(true)
        });

        new Chart(document.getElementById('itemsChart'), {
            type: 'line',
            data: {
                labels: displayLabels,
                datasets: buildDatasets(
                    @json($itemsValues),
                    @json($itemsAnalysis),
                    @json($itemsAnalysis['predictions']),
                    '#d97706', 'Terjual'
                )
            },
            options: baseOpts(false)
        });

        new Chart(document.getElementById('reviewCountChart'), {
            type: 'line',
            data: {
                labels: displayLabels,
                datasets: buildDatasets(
                    @json($reviewCountValues),
                    @json($reviewCountAnalysis),
                    @json($reviewCountAnalysis['predictions']),
                    '#e11d48', 'Ulasan'
                )
            },
            options: baseOpts(false)
        });

        const fcLen = forecastLabels.length;
        const dailyRatingHist = @json($ratingDailyPoints);
        const ratingMaHist = @json($ratingWeightedMa);
        const extRating = (arr) => [...arr, ...new Array(fcLen).fill(null)];

        new Chart(document.getElementById('ratingTrendChart'), {
            type: 'line',
            data: {
                labels: displayLabels,
                datasets: [
                    {
                        label: 'Rata-rata harian',
                        data: extRating(dailyRatingHist),
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245,158,11,0.08)',
                        fill: false,
                        borderWidth: 2,
                        spanGaps: false,
                        pointRadius: 3,
                        pointHoverRadius: 5,
                    },
                    {
                        label: 'MA 7h (terbobot)',
                        data: extRating(ratingMaHist),
                        borderColor: '#8b5cf6',
                        backgroundColor: 'transparent',
                        fill: false,
                        borderWidth: 2,
                        spanGaps: true,
                        pointRadius: 0,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    tooltip: {
                        backgroundColor: '#1e1b2e',
                        titleColor: '#fff',
                        bodyColor: '#e5e5e5',
                        cornerRadius: 10,
                        padding: 10,
                        callbacks: {
                            label: function (ctx) {
                                const v = ctx.parsed.y;
                                if (v === null || v === undefined) {
                                    return '';
                                }
                                return ctx.dataset.label + ': ' + Number(v).toFixed(2);
                            },
                        },
                    },
                    legend: { display: true, position: 'top', labels: { boxWidth: 10, padding: 8, font: { size: 10 } } },
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { maxTicksLimit: 10, font: { size: 10 } },
                    },
                    y: {
                        min: 1,
                        max: 5,
                        grid: { color: '#f3f4f6' },
                        ticks: { font: { size: 10 }, stepSize: 0.5 },
                    },
                },
            },
        });

        const mLabels = @json($monthlyLabels);
        const mForecastLabels = @json($monthlyForecastLabels);
        const mRevenue = @json($monthlyRevenue);
        const mForecast = @json($monthlyForecast);

        new Chart(document.getElementById('monthlyChart'), {
            type: 'bar',
            data: {
                labels: [...mLabels, ...mForecastLabels],
                datasets: [
                    {
                        label: 'Pendapatan',
                        data: [...mRevenue, ...new Array(mForecast.length).fill(null)],
                        backgroundColor: brandPink + 'cc',
                        borderRadius: 6,
                        barPercentage: 0.6,
                    },
                    {
                        label: 'Prediksi',
                        data: [...new Array(mRevenue.length).fill(null), ...mForecast],
                        backgroundColor: brandPink + '40',
                        borderColor: brandPink,
                        borderWidth: 1.5,
                        borderDash: [4, 4],
                        borderRadius: 6,
                        barPercentage: 0.6,
                    }
                ]
            },
            options: {
                ...baseOpts(true),
                plugins: {
                    ...baseOpts(true).plugins,
                    legend: { display: true, position: 'top', labels: { boxWidth: 12, font: { size: 11 } } }
                }
            }
        });
    }

    const payEl = document.getElementById('paymentChart');
    if (payEl) {
        const payData = @json($paymentMethods);
        const payColors = ['#f4518c', '#3b82f6', '#8b5cf6', '#059669', '#d97706', '#6b7280'];
        new Chart(payEl, {
            type: 'doughnut',
            data: {
                labels: payData.map(p => {
                    const labels = { bank_transfer: 'Transfer Bank', cod: 'COD', doku: 'DOKU', simulated_card: 'Kartu (demo)' };
                    return labels[p.payment_method] || p.payment_method;
                }),
                datasets: [{
                    data: payData.map(p => p.count),
                    backgroundColor: payColors.slice(0, payData.length),
                    borderWidth: 0,
                    spacing: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: { display: true, position: 'bottom', labels: { boxWidth: 10, padding: 8, font: { size: 10 } } }
                }
            }
        });
    }

    const statusEl = document.getElementById('statusChart');
    if (statusEl) {
        const statusData = @json($statusDist);
        const statusColors = {
            pending_payment: '#f59e0b', processing: '#06b6d4', confirmed: '#3b82f6',
            shipped: '#8b5cf6', completed: '#059669', cancelled: '#ef4444'
        };
        const statusLabels = {
            pending_payment: 'Menunggu', processing: 'Diproses', confirmed: 'Dikonfirmasi',
            shipped: 'Dikirim', completed: 'Selesai', cancelled: 'Dibatalkan'
        };
        new Chart(statusEl, {
            type: 'doughnut',
            data: {
                labels: statusData.map(s => statusLabels[s.status] || s.status),
                datasets: [{
                    data: statusData.map(s => s.count),
                    backgroundColor: statusData.map(s => statusColors[s.status] || '#9ca3af'),
                    borderWidth: 0,
                    spacing: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: { display: true, position: 'bottom', labels: { boxWidth: 10, padding: 8, font: { size: 10 } } }
                }
            }
        });
    }

    const ratingDistEl = document.getElementById('ratingDistChart');
    if (ratingDistEl) {
        const dist = @json($ratingDistribution);
        const starLabels = ['1★', '2★', '3★', '4★', '5★'];
        const starColors = ['#ef4444', '#f97316', '#eab308', '#84cc16', '#22c55e'];
        const counts = [1, 2, 3, 4, 5].map(function (s) {
            return dist[s] != null ? dist[s] : (dist[String(s)] != null ? dist[String(s)] : 0);
        });
        new Chart(ratingDistEl, {
            type: 'doughnut',
            data: {
                labels: starLabels,
                datasets: [{
                    data: counts,
                    backgroundColor: starColors,
                    borderWidth: 0,
                    spacing: 2,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '58%',
                plugins: {
                    legend: { display: true, position: 'bottom', labels: { boxWidth: 10, padding: 8, font: { size: 10 } } },
                },
            },
        });
    }
});
</script>
@endpush
