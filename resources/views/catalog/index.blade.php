@extends('layouts.app')

@section('title', 'Catalog — Skinbae.ID')

@section('content')
<div class="border-b border-brand-100 bg-gradient-to-b from-brand-50 to-white">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 sm:py-10 lg:px-8">
        <p class="mb-2 text-xs font-semibold uppercase tracking-[0.22em] text-brand-500">Skinbae.ID</p>
        <h1 class="text-3xl font-bold tracking-tight text-brand-900 sm:text-4xl">Katalog</h1>
        <p class="mt-2 max-w-2xl text-neutral-600">Produk kecantikan terkurasi — saring dengan filter di samping.</p>
    </div>
</div>

<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 lg:py-10">
    <div class="flex flex-col gap-8 lg:flex-row lg:gap-10">
        <aside class="w-full shrink-0 lg:w-72 xl:w-80">
            <div class="rounded-2xl border border-brand-100 bg-white/80 p-5 shadow-md shadow-brand-200/20 backdrop-blur-sm">
                <h2 class="mb-5 text-xs font-semibold uppercase tracking-widest text-brand-500">Saring</h2>
                <form action="{{ route('catalog') }}" method="GET" id="catalog-filter-form" class="space-y-5">
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-brand-600" for="catalog-search">Cari</label>
                        <div class="flex overflow-hidden rounded-xl border border-brand-200 bg-white focus-within:border-brand-400 focus-within:ring-2 focus-within:ring-brand-200">
                            <input type="text" name="search" id="catalog-search" class="min-w-0 flex-1 border-0 bg-transparent px-3 py-2.5 text-sm text-neutral-800 placeholder-neutral-400 focus:outline-none focus:ring-0" placeholder="Nama atau brand" value="{{ request('search') }}" autocomplete="off">
                            <button type="submit" class="border-l border-brand-100 px-3 text-brand-500 transition hover:bg-brand-50" aria-label="Cari">
                                <i class="fas fa-search text-sm" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-brand-600" for="catalog-category">Kategori</label>
                        <select name="category" id="catalog-category" class="input-brand rounded-xl py-2.5 text-sm">
                            <option value="">Semua</option>
                            @foreach($categories as $category)
                                <optgroup label="{{ $category->name }}">
                                    @foreach($category->children as $subcat)
                                        <option value="{{ $subcat->id }}" {{ request('category') == $subcat->id ? 'selected' : '' }}>{{ $subcat->name }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <span class="mb-2 block text-xs font-semibold uppercase tracking-wide text-brand-600">Brand</span>
                        <div class="max-h-44 space-y-2 overflow-y-auto pr-1">
                            @foreach($brands->take(8) as $brand)
                                <label class="flex cursor-pointer items-center gap-2 text-sm text-neutral-700">
                                    <input type="checkbox" name="brands[]" value="{{ $brand->id }}" class="h-4 w-4 rounded border-brand-300 text-brand-500 focus:ring-brand-400" id="brand{{ $brand->id }}" {{ in_array($brand->id, request('brands', [])) ? 'checked' : '' }}>
                                    <span>{{ $brand->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <span class="mb-2 block text-xs font-semibold uppercase tracking-wide text-brand-600">Tipe kulit</span>
                        <div class="max-h-44 space-y-2 overflow-y-auto pr-1">
                            @foreach($skinTypes as $skinType)
                                <label class="flex cursor-pointer items-center gap-2 text-sm text-neutral-700">
                                    <input type="checkbox" name="skin_types[]" value="{{ $skinType->id }}" class="h-4 w-4 rounded border-brand-300 text-brand-500 focus:ring-brand-400" id="skin{{ $skinType->id }}" {{ in_array($skinType->id, request('skin_types', [])) ? 'checked' : '' }}>
                                    <span>{{ $skinType->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <span class="mb-2 block text-xs font-semibold uppercase tracking-wide text-brand-600">Harga (Rp)</span>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="number" name="min_price" class="input-brand py-2 text-sm" placeholder="Min" value="{{ request('min_price') }}" min="0">
                            <input type="number" name="max_price" class="input-brand py-2 text-sm" placeholder="Max" value="{{ request('max_price') }}" min="0">
                        </div>
                    </div>

                    <label class="flex cursor-pointer items-center gap-2 text-sm text-neutral-700">
                        <input type="checkbox" name="in_stock_only" value="1" class="h-4 w-4 rounded border-brand-300 text-brand-500 focus:ring-brand-400" id="inStock" {{ request('in_stock_only') ? 'checked' : '' }}>
                        <span>Hanya tersedia</span>
                    </label>

                    <div class="flex flex-col gap-2 pt-2">
                        <button type="submit" class="btn-brand w-full py-3 text-sm">Terapkan</button>
                        <a href="{{ route('catalog') }}" class="text-center text-sm font-medium text-brand-600 underline decoration-brand-300 underline-offset-4 hover:text-brand-800">Reset semua</a>
                    </div>
                </form>
            </div>
        </aside>

        <div class="min-w-0 flex-1">
            <div class="mb-6 flex flex-col gap-4 border-b border-brand-100 pb-6 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    @if(request('search'))
                        <p class="text-lg font-semibold text-brand-900">Hasil untuk “{{ request('search') }}”</p>
                    @else
                        <p class="text-lg font-semibold text-brand-900">Semua produk</p>
                    @endif
                    <p class="mt-1 text-sm text-neutral-500">{{ $products->total() }} item</p>
                </div>
                <div class="w-full sm:w-auto">
                    <label class="mb-1 block text-[0.65rem] font-semibold uppercase tracking-wider text-brand-500" for="catalog-sort">Urutkan</label>
                    <select id="catalog-sort" name="sort_by" class="input-brand w-full min-w-[11rem] py-2.5 text-sm sm:w-auto"
                        onchange="window.location.href='{{ route('catalog') }}?' + new URLSearchParams({...Object.fromEntries(new URLSearchParams(window.location.search)), sort_by: this.value})">
                        <option value="newest" {{ request('sort_by', 'newest') == 'newest' ? 'selected' : '' }}>Terbaru</option>
                        <option value="price_low_high" {{ request('sort_by') == 'price_low_high' ? 'selected' : '' }}>Harga: terendah</option>
                        <option value="price_high_low" {{ request('sort_by') == 'price_high_low' ? 'selected' : '' }}>Harga: tertinggi</option>
                        <option value="rating" {{ request('sort_by') == 'rating' ? 'selected' : '' }}>Rating</option>
                        <option value="popularity" {{ request('sort_by') == 'popularity' ? 'selected' : '' }}>Ulasan terbanyak</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3">
                @forelse($products as $product)
                    <article class="group flex flex-col overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-md shadow-brand-200/15 transition hover:shadow-pink-soft">
                        <a href="{{ route('products.show', $product->slug) }}" class="relative block aspect-[4/5] overflow-hidden bg-brand-50">
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.03]" loading="lazy">
                            @if($product->has_discount)
                                <span class="absolute right-3 top-3 rounded-full bg-white/95 px-2 py-1 text-xs font-bold text-brand-800 shadow-sm ring-1 ring-brand-100">−{{ $product->discount_percentage }}%</span>
                            @endif
                            @if(!$product->is_in_stock)
                                <div class="absolute inset-0 flex items-center justify-center bg-white/85 text-xs font-semibold uppercase tracking-wider text-neutral-500 backdrop-blur-[2px]">Tidak tersedia</div>
                            @endif
                        </a>
                        <div class="flex flex-1 flex-col p-4 pt-3">
                            <p class="text-[0.65rem] font-semibold uppercase tracking-wider text-brand-500">{{ strtoupper($product->brand->name) }}</p>
                            <h2 class="mt-1 text-sm font-semibold leading-snug text-neutral-900">
                                <a href="{{ route('products.show', $product->slug) }}" class="hover:text-brand-600">{{ Str::limit($product->name, 52) }}</a>
                            </h2>
                            @if($product->review_count > 0)
                                <p class="mt-2 text-xs text-neutral-500"><span class="font-semibold text-neutral-800">{{ number_format((float) $product->average_rating, 1) }}</span> · {{ $product->review_count }} ulasan</p>
                            @else
                                <p class="mt-2 text-xs text-neutral-400">Belum ada ulasan</p>
                            @endif
                            <div class="mt-auto border-t border-brand-50 pt-3">
                                @if($product->has_discount)
                                    <p class="text-xs text-neutral-400 line-through">{{ $product->formatted_price }}</p>
                                @endif
                                <p class="text-base font-bold text-brand-800">{{ $product->formatted_final_price }}</p>
                            </div>
                            <div class="relative z-[2] mt-3 flex flex-col gap-2">
                                <a href="{{ route('products.show', $product->slug) }}" class="rounded-full border border-brand-200 py-2 text-center text-xs font-semibold uppercase tracking-wide text-brand-800 transition hover:border-brand-400 hover:bg-brand-50">Lihat detail</a>
                                @if($product->is_in_stock)
                                    @auth
                                        <form action="{{ route('cart.store') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="btn-brand w-full py-2.5 text-xs">Tambah ke keranjang</button>
                                        </form>
                                    @else
                                        <a href="{{ $loginReturnUrl ?? route('login') }}" class="btn-brand w-full py-2.5 text-center text-xs">Masuk untuk membeli</a>
                                    @endauth
                                @endif
                                @auth
                                    @if(in_array($product->id, $wishlistProductIds ?? []))
                                        <form action="{{ route('wishlist.destroy', $product) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full py-2 text-xs font-medium text-brand-500 underline decoration-brand-300 underline-offset-2 hover:text-brand-700" aria-label="Hapus dari wishlist">Hapus wishlist</button>
                                        </form>
                                    @else
                                        <form action="{{ route('wishlist.store') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <button type="submit" class="w-full py-2 text-xs font-medium text-brand-500 underline decoration-brand-300 underline-offset-2 hover:text-brand-700" aria-label="Tambah ke wishlist">Simpan ke wishlist</button>
                                        </form>
                                    @endif
                                @else
                                    <a href="{{ $loginReturnUrl ?? route('login') }}" class="w-full py-2 text-center text-xs font-medium text-brand-500 underline">Wishlist</a>
                                @endauth
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full rounded-2xl border border-dashed border-brand-200 bg-brand-50/50 px-6 py-14 text-center">
                        <p class="font-semibold text-brand-900">Tidak ada produk</p>
                        <p class="mt-2 text-sm text-neutral-500">Sesuaikan filter atau reset untuk melihat koleksi penuh.</p>
                        <a href="{{ route('catalog') }}" class="mt-6 inline-block text-sm font-semibold text-brand-600 underline">Reset filter</a>
                    </div>
                @endforelse
            </div>

            @if($products->hasPages())
                <nav class="mt-10 flex justify-center border-t border-brand-100 pt-8" aria-label="Navigasi halaman">
                    {{ $products->withQueryString()->links() }}
                </nav>
            @endif
        </div>
    </div>
</div>
@endsection
