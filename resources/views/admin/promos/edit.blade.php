@extends('layouts.admin')

@section('title', 'Edit Promo — Admin Skinbae.ID')
@section('page_title', 'Edit Promo')

@section('content')
<div class="mx-auto max-w-2xl space-y-5">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold tracking-tight text-gray-900">Edit Promo</h2>
            <p class="mt-0.5 text-sm text-gray-400">{{ $promo->name }}</p>
        </div>
        <a href="{{ route('admin.promos.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-stone-200 bg-white px-4 py-2 text-sm font-semibold text-gray-600 shadow-sm transition hover:bg-stone-50">
            <i class="fas fa-arrow-left text-[10px]"></i> Kembali
        </a>
    </div>

    <div class="rounded-2xl bg-white p-5 shadow-card">
        <form action="{{ route('admin.promos.update', $promo->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-gray-600">Nama Promo <span class="text-red-400">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $promo->name) }}" required
                           class="w-full rounded-xl border border-stone-200 px-3.5 py-2.5 text-sm text-gray-700 transition focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-100">
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-gray-600">Kode Promo <span class="text-red-400">*</span></label>
                    <input type="text" name="code" value="{{ old('code', $promo->code) }}" required style="text-transform:uppercase"
                           class="w-full rounded-xl border border-stone-200 px-3.5 py-2.5 text-sm font-mono text-gray-700 transition focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-100">
                </div>
            </div>

            <div>
                <label class="mb-1.5 block text-xs font-semibold text-gray-600">Deskripsi</label>
                <textarea name="description" rows="3" class="w-full rounded-xl border border-stone-200 px-3.5 py-2.5 text-sm text-gray-700 transition focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-100">{{ old('description', $promo->description) }}</textarea>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-gray-600">Tipe Diskon <span class="text-red-400">*</span></label>
                    <select name="discount_type" required class="w-full rounded-xl border border-stone-200 px-3.5 py-2.5 text-sm text-gray-700 transition focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-100">
                        <option value="percentage" {{ old('discount_type', $promo->discount_type) == 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
                        <option value="fixed" {{ old('discount_type', $promo->discount_type) == 'fixed' ? 'selected' : '' }}>Nominal Tetap (Rp)</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-gray-600">Nilai Diskon <span class="text-red-400">*</span></label>
                    <input type="number" name="discount_value" value="{{ old('discount_value', $promo->discount_value) }}" required min="0"
                           class="w-full rounded-xl border border-stone-200 px-3.5 py-2.5 text-sm text-gray-700 transition focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-100">
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-gray-600">Tanggal Mulai <span class="text-red-400">*</span></label>
                    <input type="datetime-local" name="start_date" value="{{ old('start_date', $promo->start_date->format('Y-m-d\TH:i')) }}" required
                           class="w-full rounded-xl border border-stone-200 px-3.5 py-2.5 text-sm text-gray-700 transition focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-100">
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-gray-600">Tanggal Berakhir <span class="text-red-400">*</span></label>
                    <input type="datetime-local" name="end_date" value="{{ old('end_date', $promo->end_date->format('Y-m-d\TH:i')) }}" required
                           class="w-full rounded-xl border border-stone-200 px-3.5 py-2.5 text-sm text-gray-700 transition focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-100">
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-gray-600">Batas Penggunaan</label>
                    <input type="number" name="usage_limit" value="{{ old('usage_limit', $promo->usage_limit) }}" min="0"
                           class="w-full rounded-xl border border-stone-200 px-3.5 py-2.5 text-sm text-gray-700 transition focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-100">
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-gray-600">Minimum Pembelian</label>
                    <input type="number" name="min_purchase" value="{{ old('min_purchase', $promo->min_purchase) }}" min="0" step="0.01"
                           class="w-full rounded-xl border border-stone-200 px-3.5 py-2.5 text-sm text-gray-700 transition focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-100">
                </div>
            </div>

            <div>
                <label class="mb-1.5 block text-xs font-semibold text-gray-600">Produk Spesifik (Opsional)</label>
                <div class="max-h-40 space-y-1.5 overflow-y-auto rounded-xl border border-stone-200 p-3">
                    @foreach($products as $product)
                        <label class="flex cursor-pointer items-center gap-2.5 rounded-lg px-2 py-1 transition hover:bg-stone-50">
                            <input type="checkbox" name="product_ids[]" value="{{ $product->id }}"
                                   {{ in_array($product->id, old('product_ids', $promo->products->pluck('id')->toArray())) ? 'checked' : '' }}
                                   class="h-3.5 w-3.5 rounded border-stone-300 text-brand-500 focus:ring-brand-200">
                            <span class="text-sm text-gray-600">{{ $product->name }} <span class="text-xs text-gray-400">({{ $product->sku }})</span></span>
                        </label>
                    @endforeach
                </div>
            </div>

            <label class="flex cursor-pointer items-center justify-between rounded-xl border border-stone-100 p-3 transition hover:border-stone-200 hover:bg-stone-50/50">
                <span class="text-sm font-medium text-gray-700">Aktifkan Promo</span>
                <input type="checkbox" name="is_active" value="1" {{ $promo->is_active ? 'checked' : '' }}
                       class="relative h-5 w-9 cursor-pointer appearance-none rounded-full bg-gray-200 transition checked:bg-emerald-500 after:absolute after:left-0.5 after:top-0.5 after:h-4 after:w-4 after:rounded-full after:bg-white after:shadow after:transition checked:after:translate-x-4">
            </label>

            <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-brand-500 to-brand-600 py-2.5 text-sm font-bold text-white shadow-md shadow-brand-500/20 transition hover:shadow-lg hover:shadow-brand-500/25">
                <i class="fas fa-save mr-2 text-xs"></i> Update Promo
            </button>
        </form>
    </div>
</div>
@endsection
