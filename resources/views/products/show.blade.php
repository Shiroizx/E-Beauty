@extends('layouts.app')

@section('title', $product->name . ' — Skinbae.ID')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 lg:py-8">

    {{-- ═══════════ Breadcrumb ═══════════ --}}
    <nav aria-label="Breadcrumb" class="mb-8 pdp-entrance">
        <ol class="pdp-breadcrumb">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li><span class="pdp-breadcrumb__sep" aria-hidden="true"><i class="fas fa-chevron-right"></i></span></li>
            <li><a href="{{ route('catalog') }}">Catalog</a></li>
            <li><span class="pdp-breadcrumb__sep" aria-hidden="true"><i class="fas fa-chevron-right"></i></span></li>
            <li><a href="{{ route('catalog', ['category' => $product->category_id]) }}">{{ $product->category->name }}</a></li>
            <li><span class="pdp-breadcrumb__sep" aria-hidden="true"><i class="fas fa-chevron-right"></i></span></li>
            <li><span class="pdp-breadcrumb__current" title="{{ $product->name }}">{{ Str::limit($product->name, 35) }}</span></li>
        </ol>
    </nav>

    {{-- ═══════════ Hero: Gallery + Product Info ═══════════ --}}
    <div class="grid gap-8 lg:grid-cols-12 lg:gap-12" x-data="{
        images: [
            '{{ $product->image_url }}',
            @if($product->gallery_urls)
                @foreach($product->gallery_urls as $gUrl)
                    '{{ $gUrl }}',
                @endforeach
            @endif
        ],
        activeIdx: 0,
        get activeImage() { return this.images[this.activeIdx]; }
    }">

        {{-- ──── Gallery Column ──── --}}
        <div class="lg:col-span-7 pdp-entrance">
            <div class="pdp-gallery">
                {{-- Main Image --}}
                <div class="pdp-gallery__main">
                    @if($product->has_discount)
                        <span class="pdp-gallery__badge">
                            <i class="fas fa-bolt" aria-hidden="true"></i>
                            -{{ $product->discount_percentage }}%
                        </span>
                    @endif
                    <img :src="activeImage" alt="{{ $product->name }}" id="pdp-main-image">
                </div>

                {{-- Thumbnails --}}
                <div class="pdp-gallery__thumbs" x-show="images.length > 1" x-cloak>
                    <template x-for="(img, idx) in images" :key="idx">
                        <button
                            type="button"
                            class="pdp-gallery__thumb"
                            :class="{ 'pdp-gallery__thumb--active': activeIdx === idx }"
                            @click="activeIdx = idx"
                            :aria-label="'Gambar produk ' + (idx + 1)"
                        >
                            <img :src="img" :alt="'{{ $product->name }} - gambar ' + (idx + 1)">
                        </button>
                    </template>
                </div>
            </div>
        </div>

        {{-- ──── Product Info Column ──── --}}
        <div class="lg:col-span-5" id="pdp-info-panel">

            {{-- Brand Badge --}}
            <div class="pdp-entrance-delay-1">
                <a href="{{ route('catalog', ['brands' => [$product->brand_id]]) }}" class="pdp-info__brand">
                    <i class="fas fa-gem" aria-hidden="true" style="font-size: 0.6rem;"></i>
                    {{ $product->brand->name }}
                </a>
            </div>

            {{-- Title --}}
            <h1 class="pdp-info__title pdp-entrance-delay-1">{{ $product->name }}</h1>

            {{-- Rating --}}
            <div class="pdp-info__rating pdp-entrance-delay-2">
                <span class="pdp-info__stars">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="fa-star {{ $i <= round($product->average_rating) ? 'fas text-amber-400' : 'far text-neutral-200' }}" aria-hidden="true"></i>
                    @endfor
                </span>
                <span class="pdp-info__review-count">
                    {{ number_format((float) $product->average_rating, 1) }} · {{ $product->reviews_count ?? $product->review_count ?? 0 }} ulasan
                </span>
            </div>

            {{-- Price Block --}}
            <div class="pdp-price-block pdp-entrance-delay-2">
                <div class="flex flex-wrap items-baseline gap-2">
                    @if($product->has_discount)
                        <span class="pdp-price__current">{{ $product->formatted_final_price }}</span>
                        <span class="pdp-price__original">{{ $product->formatted_price }}</span>
                    @else
                        <span class="pdp-price__current">{{ $product->formatted_price }}</span>
                    @endif
                </div>
                @if($product->has_discount)
                    <div class="pdp-price__savings">
                        <i class="fas fa-tag" aria-hidden="true" style="font-size: 0.6rem;"></i>
                        Hemat {{ $product->discount_percentage }}%
                    </div>
                @endif
            </div>

            {{-- Stock --}}
            <div class="pdp-entrance-delay-3">
                @if($product->stock_quantity > 0)
                    <div class="pdp-stock pdp-stock--available">
                        <span class="pdp-stock__dot"></span>
                        Tersedia — {{ $product->stock_quantity }} item
                    </div>
                @else
                    <div class="pdp-stock pdp-stock--unavailable">
                        <span class="pdp-stock__dot"></span>
                        Stok habis
                    </div>
                @endif
            </div>

            {{-- Short Description --}}
            <p class="mt-4 text-sm leading-relaxed text-neutral-500 pdp-entrance-delay-3" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                {{ $product->description }}
            </p>

            {{-- CTA Group --}}
            <div class="pdp-cta-group pdp-entrance-delay-4" id="pdp-main-cta">
                @if($product->stock_quantity > 0)
                    @auth
                        <form action="{{ route('cart.store') }}" method="POST" class="contents">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <label class="sr-only" for="qty-add">Jumlah</label>
                            <input type="number" name="quantity" id="qty-add" class="pdp-qty-input" value="1" min="1" max="{{ $product->stock_quantity }}" aria-label="Jumlah">
                            <button type="submit" class="pdp-btn-cart">
                                <i class="fas fa-shopping-bag" aria-hidden="true"></i>
                                Tambah ke Keranjang
                            </button>
                        </form>
                    @else
                        <a href="{{ $loginReturnUrl ?? route('login') }}" class="pdp-btn-cart" style="text-decoration: none;">
                            <i class="fas fa-sign-in-alt" aria-hidden="true"></i>
                            Login untuk Belanja
                        </a>
                    @endauth
                @else
                    <button type="button" class="pdp-btn-cart pdp-btn-cart--disabled" disabled>
                        Stok Habis
                    </button>
                @endif

                {{-- Wishlist Button --}}
                @auth
                    @if($inWishlist ?? false)
                        <form action="{{ route('wishlist.destroy', $product) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="pdp-btn-wishlist pdp-btn-wishlist--active" title="Hapus dari wishlist" aria-label="Hapus dari wishlist">
                                <i class="fas fa-heart" aria-hidden="true"></i>
                            </button>
                        </form>
                    @else
                        <form action="{{ route('wishlist.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <button type="submit" class="pdp-btn-wishlist" title="Tambah ke wishlist" aria-label="Tambah ke wishlist">
                                <i class="far fa-heart" aria-hidden="true"></i>
                            </button>
                        </form>
                    @endif
                @else
                    <a href="{{ $loginReturnUrl ?? route('login') }}" class="pdp-btn-wishlist" title="Login untuk wishlist" aria-label="Login untuk wishlist">
                        <i class="far fa-heart" aria-hidden="true"></i>
                    </a>
                @endauth
            </div>

            {{-- Trust Strip --}}
            <div class="pdp-trust-strip pdp-entrance-delay-5">
                <div class="pdp-trust-item">
                    <span class="pdp-trust-item__icon">
                        <i class="fas fa-shield-alt" aria-hidden="true"></i>
                    </span>
                    <span class="pdp-trust-item__text">100% Produk Original</span>
                </div>
                <div class="pdp-trust-item">
                    <span class="pdp-trust-item__icon">
                        <i class="fas fa-truck" aria-hidden="true"></i>
                    </span>
                    <span class="pdp-trust-item__text">Gratis Ongkir ≥Rp500rb</span>
                </div>
                <div class="pdp-trust-item">
                    <span class="pdp-trust-item__icon">
                        <i class="fas fa-undo" aria-hidden="true"></i>
                    </span>
                    <span class="pdp-trust-item__text">Tukar dalam 7 Hari</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════ Decorative Divider ═══════════ --}}
    <div class="pdp-divider" aria-hidden="true">
        <i class="fas fa-spa"></i>
    </div>

    {{-- ═══════════ Product Detail Tabs ═══════════ --}}
    <div class="pdp-tabs pdp-entrance" x-data="{ tab: 'desc' }" style="animation-delay: 0.3s;">
        <nav class="pdp-tabs__nav" aria-label="Detail produk">
            <button type="button"
                    class="pdp-tabs__btn"
                    :class="{ 'pdp-tabs__btn--active': tab === 'desc' }"
                    @click="tab = 'desc'">
                <i class="fas fa-align-left me-1.5 text-xs" aria-hidden="true"></i>
                Deskripsi
            </button>
            <button type="button"
                    class="pdp-tabs__btn"
                    :class="{ 'pdp-tabs__btn--active': tab === 'ingredients' }"
                    @click="tab = 'ingredients'">
                <i class="fas fa-flask me-1.5 text-xs" aria-hidden="true"></i>
                Komposisi
            </button>
            <button type="button"
                    class="pdp-tabs__btn"
                    :class="{ 'pdp-tabs__btn--active': tab === 'howto' }"
                    @click="tab = 'howto'">
                <i class="fas fa-hand-sparkles me-1.5 text-xs" aria-hidden="true"></i>
                Cara Pakai
            </button>
            <button type="button"
                    class="pdp-tabs__btn"
                    :class="{ 'pdp-tabs__btn--active': tab === 'specs' }"
                    @click="tab = 'specs'">
                <i class="fas fa-list-ul me-1.5 text-xs" aria-hidden="true"></i>
                Spesifikasi
            </button>
        </nav>

        {{-- Tab: Deskripsi --}}
        <div class="pdp-tabs__panel" x-show="tab === 'desc'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
            @if($product->description)
                <div class="max-w-3xl space-y-4">
                    <p>{{ $product->description }}</p>
                </div>
            @else
                <div class="pdp-tabs__empty">
                    <i class="fas fa-info-circle me-1" aria-hidden="true"></i>
                    Deskripsi produk belum tersedia.
                </div>
            @endif
        </div>

        {{-- Tab: Komposisi --}}
        <div class="pdp-tabs__panel" x-show="tab === 'ingredients'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
            @if($product->ingredients)
                <div class="max-w-3xl">
                    <h3><i class="fas fa-leaf me-1.5 text-emerald-500" aria-hidden="true"></i> Daftar Komposisi</h3>
                    <p class="whitespace-pre-line">{{ $product->ingredients }}</p>
                </div>
            @else
                <div class="pdp-tabs__empty">
                    <i class="fas fa-flask me-1" aria-hidden="true"></i>
                    Informasi komposisi belum tersedia.
                </div>
            @endif
        </div>

        {{-- Tab: Cara Pakai --}}
        <div class="pdp-tabs__panel" x-show="tab === 'howto'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
            @if($product->how_to_use)
                <div class="max-w-3xl">
                    <h3><i class="fas fa-hand-sparkles me-1.5 text-brand-500" aria-hidden="true"></i> Petunjuk Pemakaian</h3>
                    <p class="whitespace-pre-line">{{ $product->how_to_use }}</p>
                </div>
            @else
                <div class="pdp-tabs__empty">
                    <i class="fas fa-hand-sparkles me-1" aria-hidden="true"></i>
                    Petunjuk pemakaian belum tersedia.
                </div>
            @endif
        </div>

        {{-- Tab: Spesifikasi --}}
        <div class="pdp-tabs__panel" x-show="tab === 'specs'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
            <div class="max-w-2xl overflow-hidden rounded-xl border border-brand-100">
                <table class="pdp-spec-table">
                    <tbody>
                        <tr>
                            <td>Brand</td>
                            <td>{{ $product->brand->name }}</td>
                        </tr>
                        <tr>
                            <td>Kategori</td>
                            <td>{{ $product->category->name }}</td>
                        </tr>
                        @if($product->weight)
                        <tr>
                            <td>Berat</td>
                            <td>{{ $product->weight }} g</td>
                        </tr>
                        @endif
                        @if($product->size)
                        <tr>
                            <td>Ukuran</td>
                            <td>{{ $product->size }}</td>
                        </tr>
                        @endif
                        @if($product->sku)
                        <tr>
                            <td>SKU</td>
                            <td><code class="text-xs font-mono text-neutral-500">{{ $product->sku }}</code></td>
                        </tr>
                        @endif
                        @if($product->skinTypes && $product->skinTypes->count() > 0)
                        <tr>
                            <td>Tipe Kulit</td>
                            <td>
                                @foreach($product->skinTypes as $skinType)
                                    <span class="pdp-spec-tag">{{ $skinType->name }}</span>
                                @endforeach
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ═══════════ Related Products ═══════════ --}}
    @if($relatedProducts->count() > 0)
        <div class="pdp-related">
            <div class="pdp-related__header">
                <div>
                    <h2 class="pdp-related__title">Produk Terkait</h2>
                    <p class="pdp-related__subtitle">Pilihan lain yang mungkin kamu suka</p>
                </div>
                <a href="{{ route('catalog', ['category' => $product->category_id]) }}" class="hidden items-center gap-1 text-sm font-semibold text-brand-600 transition hover:text-brand-800 sm:inline-flex">
                    Lihat semua
                    <i class="fas fa-arrow-right text-xs" aria-hidden="true"></i>
                </a>
            </div>

            <div class="pdp-related__scroll">
                @foreach($relatedProducts as $related)
                    <article class="pdp-related__card">
                        <a href="{{ route('products.show', $related->slug) }}" class="pdp-related__card-img block">
                            @if($related->has_discount)
                                <span class="absolute right-2.5 top-2.5 z-10 rounded-full bg-white/95 px-2 py-0.5 text-[0.65rem] font-bold text-brand-700 shadow-sm ring-1 ring-brand-100">
                                    −{{ $related->discount_percentage }}%
                                </span>
                            @endif
                            <img src="{{ $related->image_url }}" alt="{{ $related->name }}" loading="lazy">
                        </a>
                        <div class="pdp-related__card-body">
                            <p class="pdp-related__card-brand">{{ $related->brand->name }}</p>
                            <h3 class="pdp-related__card-name">
                                <a href="{{ route('products.show', $related->slug) }}">{{ Str::limit($related->name, 45) }}</a>
                            </h3>
                            <div class="pdp-related__card-price">
                                @if($related->has_discount)
                                    <p class="text-xs text-neutral-400 line-through">{{ $related->formatted_price }}</p>
                                    <p class="text-sm font-bold text-brand-700">{{ $related->formatted_final_price }}</p>
                                @else
                                    <p class="text-sm font-bold text-brand-700">{{ $related->formatted_price }}</p>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    @endif

</div>

{{-- ═══════════ Sticky Mobile CTA ═══════════ --}}
@if($product->stock_quantity > 0)
    <div class="pdp-sticky-cta"
         x-data="{ visible: false }"
         x-init="
            const target = document.getElementById('pdp-main-cta');
            if (target) {
                const observer = new IntersectionObserver(([e]) => { visible = !e.isIntersecting; }, { threshold: 0 });
                observer.observe(target);
            }
         "
         :class="{ 'pdp-sticky-cta--visible': visible }"
    >
        <div class="pdp-sticky-cta__inner">
            <div>
                <p class="text-[0.65rem] font-semibold uppercase tracking-wide text-neutral-400">Total</p>
                <p class="pdp-sticky-cta__price">{{ $product->has_discount ? $product->formatted_final_price : $product->formatted_price }}</p>
            </div>
            <div class="pdp-sticky-cta__btn">
                @auth
                    <form action="{{ route('cart.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="pdp-btn-cart w-full" style="height: 2.75rem; font-size: 0.8rem;">
                            <i class="fas fa-shopping-bag" aria-hidden="true"></i>
                            Tambah ke Keranjang
                        </button>
                    </form>
                @else
                    <a href="{{ $loginReturnUrl ?? route('login') }}" class="pdp-btn-cart w-full" style="height: 2.75rem; font-size: 0.8rem; text-decoration: none;">
                        <i class="fas fa-sign-in-alt" aria-hidden="true"></i>
                        Login
                    </a>
                @endauth
            </div>
        </div>
    </div>
@endif
@endsection
