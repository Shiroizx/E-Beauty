@extends('layouts.admin')

@section('title', 'Manajemen Produk — Admin Skinbae.ID')
@section('page_title', 'Produk')

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-bold tracking-tight text-gray-900">Manajemen Produk</h2>
            <p class="mt-0.5 text-sm text-gray-400">Kelola semua produk di toko Anda</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-brand-500 to-brand-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-brand-500/20 transition hover:shadow-lg hover:shadow-brand-500/25 hover:from-brand-600 hover:to-brand-700">
            <i class="fas fa-plus text-xs"></i> Tambah Produk
        </a>
    </div>

    {{-- Filters --}}
    <div class="rounded-2xl bg-white p-4 shadow-card">
        <form action="{{ route('admin.products.index') }}" method="GET" class="flex flex-col gap-3 sm:flex-row sm:items-end">
            <div class="flex-1">
                <label class="mb-1 block text-[11px] font-semibold uppercase tracking-wider text-gray-400">Pencarian</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau SKU..."
                       class="w-full rounded-xl border border-stone-200 bg-stone-50/50 px-3.5 py-2 text-sm text-gray-700 placeholder-gray-300 transition focus:border-brand-300 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-100">
            </div>
            <div class="sm:w-44">
                <label class="mb-1 block text-[11px] font-semibold uppercase tracking-wider text-gray-400">Kategori</label>
                <select name="category_id" class="w-full rounded-xl border border-stone-200 bg-stone-50/50 px-3.5 py-2 text-sm text-gray-700 transition focus:border-brand-300 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-100">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sm:w-44">
                <label class="mb-1 block text-[11px] font-semibold uppercase tracking-wider text-gray-400">Brand</label>
                <select name="brand_id" class="w-full rounded-xl border border-stone-200 bg-stone-50/50 px-3.5 py-2 text-sm text-gray-700 transition focus:border-brand-300 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-100">
                    <option value="">Semua Brand</option>
                    @foreach($brands as $brand)
                        <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="rounded-xl bg-gray-800 px-5 py-2 text-sm font-semibold text-white transition hover:bg-gray-700">
                <i class="fas fa-search mr-1.5 text-[10px]"></i> Filter
            </button>
        </form>
    </div>

    {{-- Products Table --}}
    <div class="overflow-hidden rounded-2xl bg-white shadow-card">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-stone-100 text-left">
                        <th class="py-3 pl-5 pr-3 text-[11px] font-semibold uppercase tracking-wider text-gray-400">Produk</th>
                        <th class="px-3 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-400">Kategori</th>
                        <th class="px-3 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-400">Brand</th>
                        <th class="px-3 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-400">Harga</th>
                        <th class="px-3 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-400">Stok</th>
                        <th class="px-3 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-400">Status</th>
                        <th class="py-3 pl-3 pr-5 text-right text-[11px] font-semibold uppercase tracking-wider text-gray-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-50">
                    @forelse($products as $product)
                        <tr class="transition hover:bg-stone-50/60">
                            <td class="py-3 pl-5 pr-3">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $product->image_url }}" alt="" class="h-10 w-10 rounded-lg object-cover ring-1 ring-stone-100">
                                    <div class="min-w-0">
                                        <p class="truncate font-semibold text-gray-800 max-w-[180px]">{{ $product->name }}</p>
                                        <p class="text-[11px] text-gray-400">{{ $product->sku }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 py-3 text-gray-600">{{ $product->category->name }}</td>
                            <td class="px-3 py-3 text-gray-600">{{ $product->brand->name }}</td>
                            <td class="px-3 py-3 font-semibold text-gray-800">{{ $product->formatted_price }}</td>
                            <td class="px-3 py-3">
                                @if($product->stock && $product->stock->quantity <= $product->stock->min_quantity)
                                    <span class="inline-flex items-center rounded-full bg-red-50 px-2 py-0.5 text-xs font-bold text-red-600 ring-1 ring-inset ring-red-200/60">
                                        Low: {{ $product->stock->quantity }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-bold text-emerald-700 ring-1 ring-inset ring-emerald-200/60">
                                        {{ $product->stock->quantity ?? 0 }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-3 py-3">
                                <form action="{{ route('admin.products.toggle-status', $product->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="transition hover:opacity-70" title="{{ $product->is_active ? 'Aktif' : 'Non-Aktif' }}">
                                        @if($product->is_active)
                                            <span class="flex h-6 w-10 items-center rounded-full bg-emerald-500 p-0.5 shadow-inner">
                                                <span class="h-5 w-5 translate-x-4 rounded-full bg-white shadow transition"></span>
                                            </span>
                                        @else
                                            <span class="flex h-6 w-10 items-center rounded-full bg-gray-200 p-0.5 shadow-inner">
                                                <span class="h-5 w-5 rounded-full bg-white shadow transition"></span>
                                            </span>
                                        @endif
                                    </button>
                                </form>
                            </td>
                            <td class="py-3 pl-3 pr-5">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.products.edit', $product->id) }}" class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 transition hover:bg-blue-50 hover:text-blue-500" title="Edit">
                                        <i class="fas fa-pen text-xs"></i>
                                    </a>
                                    <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus produk ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 transition hover:bg-red-50 hover:text-red-500" title="Hapus">
                                            <i class="fas fa-trash-alt text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-16 text-center">
                                <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-stone-100">
                                    <i class="fas fa-box-open text-stone-300 text-lg"></i>
                                </div>
                                <p class="text-sm text-gray-400">Belum ada produk</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
            <div class="border-t border-stone-100 px-5 py-3">
                {{ $products->links('pagination::tailwind') }}
            </div>
        @endif
    </div>
</div>
@endsection
