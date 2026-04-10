@extends('layouts.admin')

@section('title', 'Dashboard — Admin Skinbae.ID')
@section('page_title', 'Dashboard')

@section('content')
<div class="space-y-6">

    {{-- Greeting --}}
    <div>
        <h2 class="text-xl font-bold text-gray-900 tracking-tight">Dashboard</h2>
        <p class="mt-0.5 text-sm text-gray-400">Ringkasan data toko Skinbae.ID hari ini</p>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

        {{-- Total Produk --}}
        <div class="group relative overflow-hidden rounded-2xl bg-white p-5 shadow-card transition-shadow hover:shadow-card-hover">
            <div class="absolute -right-3 -top-3 h-20 w-20 rounded-full bg-blue-50 transition-transform group-hover:scale-110"></div>
            <div class="relative flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Total Produk</p>
                    <p class="mt-2 text-3xl font-extrabold tracking-tight text-gray-900">{{ $stats['total_products'] }}</p>
                </div>
                <span class="relative flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-500">
                    <i class="fas fa-box text-lg"></i>
                </span>
            </div>
            <div class="mt-3 h-1 w-12 rounded-full bg-gradient-to-r from-blue-400 to-blue-200"></div>
        </div>

        {{-- Total Brand --}}
        <div class="group relative overflow-hidden rounded-2xl bg-white p-5 shadow-card transition-shadow hover:shadow-card-hover">
            <div class="absolute -right-3 -top-3 h-20 w-20 rounded-full bg-emerald-50 transition-transform group-hover:scale-110"></div>
            <div class="relative flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Total Brand</p>
                    <p class="mt-2 text-3xl font-extrabold tracking-tight text-gray-900">{{ $stats['total_brands'] }}</p>
                </div>
                <span class="relative flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-500">
                    <i class="fas fa-tag text-lg"></i>
                </span>
            </div>
            <div class="mt-3 h-1 w-12 rounded-full bg-gradient-to-r from-emerald-400 to-emerald-200"></div>
        </div>

        {{-- Stok Rendah --}}
        <div class="group relative overflow-hidden rounded-2xl bg-white p-5 shadow-card transition-shadow hover:shadow-card-hover">
            <div class="absolute -right-3 -top-3 h-20 w-20 rounded-full bg-amber-50 transition-transform group-hover:scale-110"></div>
            <div class="relative flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Stok Rendah</p>
                    <p class="mt-2 text-3xl font-extrabold tracking-tight text-amber-600">{{ $stats['stock_stats']['low_stock_count'] }}</p>
                </div>
                <span class="relative flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50 text-amber-500">
                    <i class="fas fa-exclamation-triangle text-lg"></i>
                </span>
            </div>
            <div class="mt-3 h-1 w-12 rounded-full bg-gradient-to-r from-amber-400 to-amber-200"></div>
        </div>

        {{-- Review Pending --}}
        <div class="group relative overflow-hidden rounded-2xl bg-white p-5 shadow-card transition-shadow hover:shadow-card-hover">
            <div class="absolute -right-3 -top-3 h-20 w-20 rounded-full bg-brand-50 transition-transform group-hover:scale-110"></div>
            <div class="relative flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Review Pending</p>
                    <p class="mt-2 text-3xl font-extrabold tracking-tight text-brand-600">{{ $stats['review_stats']['pending_reviews'] }}</p>
                </div>
                <span class="relative flex h-11 w-11 items-center justify-center rounded-xl bg-brand-50 text-brand-500">
                    <i class="fas fa-star text-lg"></i>
                </span>
            </div>
            <div class="mt-3 h-1 w-12 rounded-full bg-gradient-to-r from-brand-400 to-brand-200"></div>
        </div>
    </div>

    {{-- Data Panels --}}
    <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">

        {{-- Low Stock Products --}}
        <div class="overflow-hidden rounded-2xl bg-white shadow-card">
            <div class="flex items-center justify-between border-b border-stone-100 px-5 py-4">
                <div class="flex items-center gap-2.5">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-50 text-amber-500">
                        <i class="fas fa-box-open text-sm"></i>
                    </span>
                    <h3 class="text-sm font-bold text-gray-800">Produk Stok Rendah</h3>
                </div>
                @if($lowStockProducts->count() > 5)
                    <a href="{{ route('admin.stocks.index', ['filter' => 'low_stock']) }}" class="text-xs font-semibold text-brand-500 hover:text-brand-700 transition">
                        Lihat Semua <i class="fas fa-arrow-right ml-1 text-[9px]"></i>
                    </a>
                @endif
            </div>
            <div class="divide-y divide-stone-50">
                @forelse($lowStockProducts->take(5) as $item)
                    <div class="flex items-center justify-between px-5 py-3 transition hover:bg-stone-50/50">
                        <span class="text-sm text-gray-700 truncate max-w-[200px]">{{ $item['product']->name }}</span>
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-bold text-amber-700 ring-1 ring-inset ring-amber-200/60">
                                {{ $item['current_quantity'] }}
                            </span>
                            <span class="text-[11px] text-gray-400">min {{ $item['min_quantity'] }}</span>
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-10 text-center">
                        <div class="mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-full bg-emerald-50">
                            <i class="fas fa-check text-emerald-400 text-sm"></i>
                        </div>
                        <p class="text-xs text-gray-400">Semua stok dalam kondisi aman</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Pending Reviews --}}
        <div class="overflow-hidden rounded-2xl bg-white shadow-card">
            <div class="flex items-center justify-between border-b border-stone-100 px-5 py-4">
                <div class="flex items-center gap-2.5">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-50 text-brand-500">
                        <i class="fas fa-comments text-sm"></i>
                    </span>
                    <h3 class="text-sm font-bold text-gray-800">Review Menunggu Persetujuan</h3>
                </div>
                @if($pendingReviews->count() > 5)
                    <a href="{{ route('admin.reviews.index', ['status' => 'pending']) }}" class="text-xs font-semibold text-brand-500 hover:text-brand-700 transition">
                        Lihat Semua <i class="fas fa-arrow-right ml-1 text-[9px]"></i>
                    </a>
                @endif
            </div>
            <div class="divide-y divide-stone-50">
                @forelse($pendingReviews->take(5) as $review)
                    <div class="flex items-center justify-between gap-4 px-5 py-3 transition hover:bg-stone-50/50">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium text-gray-700">{{ $review->product->name }}</p>
                            <p class="text-[11px] text-gray-400">oleh {{ $review->user->name }}</p>
                        </div>
                        <div class="flex items-center gap-0.5 text-amber-400">
                            @for($i = 1; $i <= $review->rating; $i++)
                                <i class="fas fa-star text-[10px]"></i>
                            @endfor
                            @for($i = $review->rating + 1; $i <= 5; $i++)
                                <i class="far fa-star text-[10px] text-gray-200"></i>
                            @endfor
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-10 text-center">
                        <div class="mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-full bg-emerald-50">
                            <i class="fas fa-check text-emerald-400 text-sm"></i>
                        </div>
                        <p class="text-xs text-gray-400">Tidak ada review pending</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
