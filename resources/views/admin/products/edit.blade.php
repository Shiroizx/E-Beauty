@extends('layouts.admin')

@section('title', 'Edit Produk — Admin Skinbae.ID')
@section('page_title', 'Edit Produk')

@section('content')
<div class="mx-auto max-w-5xl space-y-5">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold tracking-tight text-gray-900">Edit Produk</h2>
            <p class="mt-0.5 text-sm text-gray-400">{{ $product->name }}</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-stone-200 bg-white px-4 py-2 text-sm font-semibold text-gray-600 shadow-sm transition hover:bg-stone-50 hover:border-stone-300">
            <i class="fas fa-arrow-left text-[10px]"></i> Kembali
        </a>
    </div>

    {{-- Form --}}
    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
            {{-- Left: Main Info --}}
            <div class="space-y-5 lg:col-span-2">
                <div class="rounded-2xl bg-white p-5 shadow-card">
                    <h3 class="mb-4 flex items-center gap-2 text-sm font-bold text-gray-800">
                        <span class="flex h-6 w-6 items-center justify-center rounded-md bg-blue-50 text-blue-500"><i class="fas fa-info text-[9px]"></i></span>
                        Informasi Dasar
                    </h3>

                    <div class="space-y-4">
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-gray-600">Nama Produk <span class="text-red-400">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                                   class="w-full rounded-xl border border-stone-200 px-3.5 py-2.5 text-sm text-gray-700 transition focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-100">
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-xs font-semibold text-gray-600">Brand <span class="text-red-400">*</span></label>
                                <select name="brand_id" required class="w-full rounded-xl border border-stone-200 px-3.5 py-2.5 text-sm text-gray-700 transition focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-100">
                                    <option value="">Pilih Brand</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-semibold text-gray-600">Kategori <span class="text-red-400">*</span></label>
                                <select name="category_id" required class="w-full rounded-xl border border-stone-200 px-3.5 py-2.5 text-sm text-gray-700 transition focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-100">
                                    <option value="">Pilih Kategori</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                        @foreach($category->children as $child)
                                            <option value="{{ $child->id }}" {{ old('category_id', $product->category_id) == $child->id ? 'selected' : '' }}>&nbsp;&nbsp;— {{ $child->name }}</option>
                                        @endforeach
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-gray-600">Deskripsi</label>
                            <textarea name="description" rows="4" class="w-full rounded-xl border border-stone-200 px-3.5 py-2.5 text-sm text-gray-700 transition focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-100">{{ old('description', $product->description) }}</textarea>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-gray-600">Cara Penggunaan</label>
                            <textarea name="how_to_use" rows="3" class="w-full rounded-xl border border-stone-200 px-3.5 py-2.5 text-sm text-gray-700 transition focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-100">{{ old('how_to_use', $product->how_to_use) }}</textarea>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-gray-600">Komposisi (Ingredients)</label>
                            <textarea name="ingredients" rows="3" class="w-full rounded-xl border border-stone-200 px-3.5 py-2.5 text-sm text-gray-700 transition focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-100">{{ old('ingredients', $product->ingredients) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right: Details --}}
            <div class="space-y-5">
                <div class="rounded-2xl bg-white p-5 shadow-card">
                    <h3 class="mb-4 flex items-center gap-2 text-sm font-bold text-gray-800">
                        <span class="flex h-6 w-6 items-center justify-center rounded-md bg-emerald-50 text-emerald-500"><i class="fas fa-dollar-sign text-[9px]"></i></span>
                        Detail & Harga
                    </h3>

                    <div class="space-y-4">
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-gray-600">Harga (IDR) <span class="text-red-400">*</span></label>
                            <input type="number" name="price" value="{{ old('price', $product->price) }}" required min="0"
                                   class="w-full rounded-xl border border-stone-200 px-3.5 py-2.5 text-sm text-gray-700 transition focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-100">
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-gray-600">Harga Diskon</label>
                            <input type="number" name="discount_price" value="{{ old('discount_price', $product->discount_price) }}" min="0"
                                   class="w-full rounded-xl border border-stone-200 px-3.5 py-2.5 text-sm text-gray-700 transition focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-100">
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-gray-600">SKU</label>
                            <input type="text" name="sku" value="{{ old('sku', $product->sku) }}"
                                   class="w-full rounded-xl border border-stone-200 px-3.5 py-2.5 text-sm text-gray-700 transition focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-100">
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl bg-white p-5 shadow-card">
                    <h3 class="mb-4 flex items-center gap-2 text-sm font-bold text-gray-800">
                        <span class="flex h-6 w-6 items-center justify-center rounded-md bg-violet-50 text-violet-500"><i class="fas fa-image text-[9px]"></i></span>
                        Media & Opsi
                    </h3>

                    <div class="space-y-4">
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-gray-600">Gambar Utama</label>
                            @if($product->image)
                                <div class="mb-2">
                                    <img src="{{ $product->image_url }}" alt="Preview" class="h-20 w-20 rounded-xl object-cover ring-1 ring-stone-200">
                                </div>
                            @endif
                            <input type="file" name="image" accept="image/*"
                                   class="w-full rounded-xl border border-stone-200 bg-stone-50/50 px-3 py-2 text-sm text-gray-500 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-1 file:text-xs file:font-semibold file:text-brand-600 hover:file:bg-brand-100">
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-gray-600">Tipe Kulit</label>
                            <div class="max-h-36 space-y-1.5 overflow-y-auto rounded-xl border border-stone-200 p-3">
                                @foreach($skinTypes as $skinType)
                                    <label class="flex cursor-pointer items-center gap-2.5 rounded-lg px-2 py-1 transition hover:bg-stone-50">
                                        <input type="checkbox" name="skin_type_ids[]" value="{{ $skinType->id }}"
                                               {{ in_array($skinType->id, old('skin_type_ids', $product->skinTypes->pluck('id')->toArray())) ? 'checked' : '' }}
                                               class="h-3.5 w-3.5 rounded border-stone-300 text-brand-500 focus:ring-brand-200">
                                        <span class="text-sm text-gray-600">{{ $skinType->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="space-y-3 pt-1">
                            <label class="flex cursor-pointer items-center justify-between rounded-xl border border-stone-100 p-3 transition hover:border-stone-200 hover:bg-stone-50/50">
                                <span class="text-sm font-medium text-gray-700">Aktif / Dijual</span>
                                <input type="checkbox" name="is_active" value="1" {{ $product->is_active ? 'checked' : '' }}
                                       class="relative h-5 w-9 cursor-pointer appearance-none rounded-full bg-gray-200 transition checked:bg-emerald-500 after:absolute after:left-0.5 after:top-0.5 after:h-4 after:w-4 after:rounded-full after:bg-white after:shadow after:transition checked:after:translate-x-4">
                            </label>
                            <label class="flex cursor-pointer items-center justify-between rounded-xl border border-stone-100 p-3 transition hover:border-stone-200 hover:bg-stone-50/50">
                                <span class="text-sm font-medium text-gray-700">Produk Unggulan</span>
                                <input type="checkbox" name="is_featured" value="1" {{ $product->is_featured ? 'checked' : '' }}
                                       class="relative h-5 w-9 cursor-pointer appearance-none rounded-full bg-gray-200 transition checked:bg-brand-500 after:absolute after:left-0.5 after:top-0.5 after:h-4 after:w-4 after:rounded-full after:bg-white after:shadow after:transition checked:after:translate-x-4">
                            </label>
                        </div>

                        <button type="submit" class="mt-2 w-full rounded-xl bg-gradient-to-r from-brand-500 to-brand-600 py-2.5 text-sm font-bold text-white shadow-md shadow-brand-500/20 transition hover:shadow-lg hover:shadow-brand-500/25">
                            <i class="fas fa-save mr-2 text-xs"></i> Update Produk
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
