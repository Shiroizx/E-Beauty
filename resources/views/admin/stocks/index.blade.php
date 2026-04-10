@extends('layouts.admin')

@section('title', 'Manajemen Stok — Admin Skinbae.ID')
@section('page_title', 'Stok')

@section('content')
<div class="space-y-5" x-data="stockManager()">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-bold tracking-tight text-gray-900">Manajemen Stok</h2>
            <p class="mt-0.5 text-sm text-gray-400">Pantau dan kelola persediaan produk</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.stocks.index', ['filter' => 'low_stock']) }}"
               class="inline-flex items-center gap-1.5 rounded-xl border border-amber-200 bg-amber-50 px-3.5 py-2 text-xs font-semibold text-amber-700 transition hover:bg-amber-100">
                <i class="fas fa-exclamation-triangle text-[10px]"></i> Low Stock
            </a>
            <a href="{{ route('admin.stocks.index', ['filter' => 'expiring']) }}"
               class="inline-flex items-center gap-1.5 rounded-xl border border-red-200 bg-red-50 px-3.5 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-100">
                <i class="fas fa-clock text-[10px]"></i> Expiring Soon
            </a>
        </div>
    </div>

    {{-- Statistics --}}
    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
        <div class="rounded-2xl bg-white p-4 shadow-card">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Total Items</p>
            <p class="mt-1 text-2xl font-extrabold tracking-tight text-gray-900">{{ $statistics['total_items'] }}</p>
        </div>
        <div class="rounded-2xl bg-white p-4 shadow-card">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Total Quantity</p>
            <p class="mt-1 text-2xl font-extrabold tracking-tight text-gray-900">{{ $statistics['total_quantity'] }}</p>
        </div>
        <div class="rounded-2xl bg-white p-4 shadow-card">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Low Stock</p>
            <p class="mt-1 text-2xl font-extrabold tracking-tight text-amber-600">{{ $statistics['low_stock_count'] }}</p>
        </div>
        <div class="rounded-2xl bg-white p-4 shadow-card">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Expiring</p>
            <p class="mt-1 text-2xl font-extrabold tracking-tight text-red-600">{{ $statistics['expiring_soon_count'] }}</p>
        </div>
    </div>

    {{-- Search --}}
    <div class="rounded-2xl bg-white p-4 shadow-card">
        <form action="{{ route('admin.stocks.index') }}" method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama produk atau SKU..."
                   class="flex-1 rounded-xl border border-stone-200 bg-stone-50/50 px-3.5 py-2 text-sm text-gray-700 placeholder-gray-300 transition focus:border-brand-300 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-100">
            <button type="submit" class="rounded-xl bg-gray-800 px-5 py-2 text-sm font-semibold text-white transition hover:bg-gray-700">
                <i class="fas fa-search mr-1.5 text-[10px]"></i> Cari
            </button>
            @if(request()->has('filter') || request()->has('search'))
                <a href="{{ route('admin.stocks.index') }}" class="rounded-xl border border-stone-200 bg-white px-4 py-2 text-sm font-semibold text-gray-600 transition hover:bg-stone-50">Reset</a>
            @endif
        </form>
    </div>

    {{-- Stock Table --}}
    <div class="overflow-hidden rounded-2xl bg-white shadow-card">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-stone-100 text-left">
                        <th class="py-3 pl-5 pr-3 text-[11px] font-semibold uppercase tracking-wider text-gray-400">Produk</th>
                        <th class="px-3 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-400">SKU</th>
                        <th class="px-3 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-400">Warehouse</th>
                        <th class="px-3 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-400">Batch</th>
                        <th class="px-3 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-400">Expiry</th>
                        <th class="px-3 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-400">Stok</th>
                        <th class="py-3 pl-3 pr-5 text-right text-[11px] font-semibold uppercase tracking-wider text-gray-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-50">
                    @forelse($stocks as $stock)
                        <tr class="transition hover:bg-stone-50/60">
                            <td class="py-3 pl-5 pr-3 font-semibold text-gray-800">{{ $stock->product->name }}</td>
                            <td class="px-3 py-3 font-mono text-xs text-gray-500">{{ $stock->product->sku ?? '—' }}</td>
                            <td class="px-3 py-3 text-gray-500">{{ $stock->warehouse_location ?? '—' }}</td>
                            <td class="px-3 py-3 text-gray-500">{{ $stock->batch_number ?? '—' }}</td>
                            <td class="px-3 py-3">
                                @if($stock->expiry_date)
                                    <span class="{{ $stock->is_expiring_soon ? 'font-bold text-red-600' : 'text-gray-600' }}">
                                        {{ $stock->expiry_date->format('d M Y') }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-300">—</span>
                                @endif
                            </td>
                            <td class="px-3 py-3">
                                @if($stock->is_low_stock)
                                    <span class="inline-flex items-center rounded-full bg-red-50 px-2 py-0.5 text-xs font-bold text-red-600 ring-1 ring-inset ring-red-200/60">
                                        Low: {{ $stock->quantity }} / {{ $stock->min_quantity }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-bold text-emerald-700 ring-1 ring-inset ring-emerald-200/60">
                                        {{ $stock->quantity }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 pl-3 pr-5 text-right">
                                <button @click="openModal({
                                    product_id: {{ $stock->product_id }},
                                    product_name: '{{ addslashes($stock->product->name) }}',
                                    quantity: {{ $stock->quantity }},
                                    min_quantity: {{ $stock->min_quantity }},
                                    warehouse_location: '{{ addslashes($stock->warehouse_location ?? '') }}',
                                    batch_number: '{{ addslashes($stock->batch_number ?? '') }}',
                                    expiry_date: '{{ $stock->expiry_date ? $stock->expiry_date->format('Y-m-d') : '' }}'
                                })" class="inline-flex items-center gap-1.5 rounded-lg border border-stone-200 bg-white px-3 py-1.5 text-xs font-semibold text-gray-600 transition hover:bg-stone-50 hover:border-stone-300">
                                    <i class="fas fa-pen text-[9px]"></i> Update
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-16 text-center">
                                <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-stone-100">
                                    <i class="fas fa-warehouse text-stone-300 text-lg"></i>
                                </div>
                                <p class="text-sm text-gray-400">Belum ada data stok</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($stocks->hasPages())
            <div class="border-t border-stone-100 px-5 py-3">
                {{ $stocks->links('pagination::tailwind') }}
            </div>
        @endif
    </div>

    {{-- Update Stock Modal --}}
    <div x-show="showModal" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center p-4">
        <div x-show="showModal" x-transition:enter="transition-opacity duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             @click="showModal = false" class="fixed inset-0 bg-black/40 backdrop-blur-sm"></div>

        <div x-show="showModal" x-transition:enter="transition duration-200" x-transition:enter-start="scale-95 opacity-0" x-transition:enter-end="scale-100 opacity-100"
             x-transition:leave="transition duration-150" x-transition:leave-start="scale-100 opacity-100" x-transition:leave-end="scale-95 opacity-0"
             class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">

            <div class="mb-5 flex items-center justify-between">
                <h3 class="text-base font-bold text-gray-900">Update Stok</h3>
                <button @click="showModal = false" class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 transition hover:bg-stone-100 hover:text-gray-600">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>

            <p class="mb-4 text-sm text-gray-500" x-text="current?.product_name"></p>

            <form :action="'/admin/stocks/' + current?.product_id" method="POST" class="space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-gray-600">Jumlah Stok</label>
                    <input type="number" name="quantity" :value="current?.quantity" required min="0"
                           class="w-full rounded-xl border border-stone-200 px-3.5 py-2.5 text-sm text-gray-700 transition focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-100">
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-gray-600">Minimum Stok (Alert)</label>
                    <input type="number" name="min_quantity" :value="current?.min_quantity" min="0"
                           class="w-full rounded-xl border border-stone-200 px-3.5 py-2.5 text-sm text-gray-700 transition focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-100">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-gray-600">Lokasi Gudang</label>
                        <input type="text" name="warehouse_location" :value="current?.warehouse_location"
                               class="w-full rounded-xl border border-stone-200 px-3.5 py-2.5 text-sm text-gray-700 transition focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-100">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-gray-600">Batch No</label>
                        <input type="text" name="batch_number" :value="current?.batch_number"
                               class="w-full rounded-xl border border-stone-200 px-3.5 py-2.5 text-sm text-gray-700 transition focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-100">
                    </div>
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-gray-600">Expiry Date</label>
                    <input type="date" name="expiry_date" :value="current?.expiry_date"
                           class="w-full rounded-xl border border-stone-200 px-3.5 py-2.5 text-sm text-gray-700 transition focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-100">
                </div>

                <div class="flex gap-2 pt-2">
                    <button type="button" @click="showModal = false" class="flex-1 rounded-xl border border-stone-200 py-2.5 text-sm font-semibold text-gray-600 transition hover:bg-stone-50">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 rounded-xl bg-gradient-to-r from-brand-500 to-brand-600 py-2.5 text-sm font-bold text-white shadow-md shadow-brand-500/20 transition hover:shadow-lg hover:shadow-brand-500/25">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function stockManager() {
        return {
            showModal: false,
            current: null,
            openModal(stock) {
                this.current = stock;
                this.showModal = true;
            }
        }
    }
</script>
@endpush
