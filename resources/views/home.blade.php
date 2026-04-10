@extends('layouts.app')

@section('title', 'Skinbae.ID — Premium Beauty & Skincare')

@push('styles')
<style>
    :root {
        --pink-deep: #b82d5c;
        --pink-brand: #f4518c;
        --pink-soft: #ffb6cc;
        --cream: #fff5f9;
    }

    @keyframes float { 0%,100% { transform: translateY(0px); } 50% { transform: translateY(-12px); } }
    @keyframes float-slow { 0%,100% { transform: translateY(0px) rotate(0deg); } 50% { transform: translateY(-8px) rotate(3deg); } }
    @keyframes marquee { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }
    @keyframes pulse-glow { 0%,100% { opacity: 0.4; } 50% { opacity: 0.8; } }
    @keyframes slide-up { from { opacity: 0; transform: translateY(40px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes scale-in { from { opacity: 0; transform: scale(0.9); } to { opacity: 1; transform: scale(1); } }
    @keyframes gradient-shift { 0% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } 100% { background-position: 0% 50%; } }
    @keyframes shimmer { 0% { background-position: -200% center; } 100% { background-position: 200% center; } }
    @keyframes blob { 0%,100% { border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%; } 50% { border-radius: 30% 60% 70% 40% / 50% 60% 30% 60%; } }
    
    .animate-float { animation: float 6s ease-in-out infinite; }
    .animate-float-slow { animation: float-slow 8s ease-in-out infinite; }
    .animate-marquee { animation: marquee 30s linear infinite; }
    .animate-blob { animation: blob 8s ease-in-out infinite; }
    .animate-pulse-glow { animation: pulse-glow 3s ease-in-out infinite; }
    
    .animate-slide-up { animation: slide-up 0.8s cubic-bezier(0.22, 1, 0.36, 1) both; }
    .animate-scale-in { animation: scale-in 0.6s cubic-bezier(0.22, 1, 0.36, 1) both; }

    .hero-gradient {
        background: linear-gradient(135deg, #fff5f9 0%, #ffe8f2 40%, #ffd0e5 100%);
    }
    .hero-gradient-deep {
        background: linear-gradient(135deg, #b82d5c 0%, #f4518c 50%, #ff6b9d 100%);
        background-size: 200% 200%;
        animation: gradient-shift 8s ease infinite;
    }

    .glass-card {
        background: rgba(255,255,255,0.85);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255,255,255,0.5);
    }
    .glass-dark {
        background: rgba(30,20,20,0.6);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
    }

    .card-float {
        transition: transform 0.4s cubic-bezier(0.22, 1, 0.36, 1), box-shadow 0.4s ease;
    }
    .card-float:hover {
        transform: translateY(-8px);
        box-shadow: 0 24px 48px -12px rgba(244, 81, 140, 0.25);
    }

    .product-img-zoom img { transition: transform 0.6s cubic-bezier(0.22, 1, 0.36, 1); }
    .product-img-zoom:hover img { transform: scale(1.08); }

    .category-card::before {
        content: '';
        position: absolute; inset: 0;
        background: linear-gradient(180deg, transparent 40%, rgba(0,0,0,0.6) 100%);
        border-radius: inherit;
        z-index: 1;
    }
    .category-card:hover .cat-img { transform: scale(1.1); }
    .cat-img { transition: transform 0.7s cubic-bezier(0.22, 1, 0.36, 1); }

    .btn-primary-gradient {
        background: linear-gradient(135deg, #f4518c 0%, #d6386f 100%);
        transition: all 0.3s ease;
    }
    .btn-primary-gradient:hover {
        background: linear-gradient(135deg, #d6386f 0%, #b82d5c 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 24px -4px rgba(244,81,140,0.4);
    }

    .counter { font-variant-numeric: tabular-nums; }

    .scroll-hidden::-webkit-scrollbar { display: none; }
    .scroll-hidden { -ms-overflow-style: none; scrollbar-width: none; }

    .text-gradient {
        background: linear-gradient(135deg, #f4518c, #b82d5c);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .parallax-slow { will-change: transform; }

    .hero-shape {
        position: absolute;
        border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%;
        animation: blob 10s ease-in-out infinite;
    }

    .cta-input:focus { box-shadow: 0 0 0 3px rgba(244,81,140,0.3); }

    .testimonial-card { transition: all 0.4s cubic-bezier(0.22,1,0.36,1); }
    .testimonial-card.active { transform: scale(1.05); border-color: #f4518c; }

    .stagger-1 { animation-delay: 0.1s; }
    .stagger-2 { animation-delay: 0.2s; }
    .stagger-3 { animation-delay: 0.3s; }
    .stagger-4 { animation-delay: 0.4s; }
    .stagger-5 { animation-delay: 0.5s; }

    @media (prefers-reduced-motion: reduce) {
        *, *::before, *::after { animation-duration: 0.01ms !important; transition-duration: 0.01ms !important; }
    }
</style>
@endpush

@section('content')

<div x-data="homepage()" x-init="init()">

    <!-- ======================== HERO SECTION ======================== -->
    <section class="relative overflow-hidden hero-gradient min-h-[92vh] flex items-center" aria-label="Hero utama">
        <!-- Decorative blobs -->
        <div class="hero-shape bg-pink-200/40 w-96 h-96 -top-20 -right-20 animate-blob" aria-hidden="true"></div>
        <div class="hero-shape bg-pink-300/30 w-72 h-72 top-1/2 -left-10 animate-blob" style="animation-delay: -3s;" aria-hidden="true"></div>
        <div class="hero-shape bg-rose-100/50 w-64 h-64 bottom-10 right-1/4 animate-blob" style="animation-delay: -6s;" aria-hidden="true"></div>

        <!-- Floating product images -->
        <div class="absolute right-[5%] top-1/2 -translate-y-1/2 hidden xl:block animate-float" aria-hidden="true" style="animation-delay: 0s;">
            <img src="{{ $featuredProducts->first()?->image_url ?? 'https://picsum.photos/seed/skinbae1/300/400' }}" alt="" class="w-48 h-64 object-cover rounded-3xl shadow-2xl shadow-brand-300/30 rotate-3" loading="eager">
        </div>
        <div class="absolute right-[2%] top-[15%] hidden xl:block animate-float-slow" style="animation-delay: -2s;" aria-hidden="true">
            <img src="{{ $featuredProducts->skip(1)->first()?->image_url ?? 'https://picsum.photos/seed/skinbae2/200/280' }}" alt="" class="w-36 h-48 object-cover rounded-2xl shadow-xl shadow-brand-300/20 -rotate-6" loading="eager">
        </div>
        <div class="absolute right-[12%] bottom-[15%] hidden 2xl:block animate-float" style="animation-delay: -4s;" aria-hidden="true">
            <img src="{{ $featuredProducts->skip(2)->first()?->image_url ?? 'https://picsum.photos/seed/skinbae3/220/300' }}" alt="" class="w-40 h-52 object-cover rounded-2xl shadow-xl shadow-brand-300/20 rotate-2" loading="eager">
        </div>

        <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 w-full">
            <div class="max-w-2xl">
                <div class="inline-flex items-center gap-2 rounded-full border border-pink-200 bg-white/70 backdrop-blur px-4 py-1.5 text-xs font-semibold text-pink-700 shadow-sm mb-6 animate-slide-up">
                    <span class="flex h-2 w-2 rounded-full bg-pink-500 animate-pulse"></span>
                    Gratis Ongkir untuk Pembelian di Atas Rp200rb
                </div>

                <h1 class="mb-6 text-5xl font-black leading-[1.1] tracking-tight text-neutral-900 sm:text-6xl lg:text-7xl animate-slide-up stagger-1">
                    Kulit sehat,<br>
                    <span class="text-gradient">cantik </span><br>
                    <span class="relative inline-block">
                        alami
                        <svg class="absolute -bottom-2 left-0 w-full" viewBox="0 0 200 12" fill="none" aria-hidden="true">
                            <path d="M2 10C50 2 150 2 198 8" stroke="#f4518c" stroke-width="3" stroke-linecap="round"/>
                        </svg>
                    </span>
                </h1>

                <p class="mb-8 max-w-lg text-lg leading-relaxed text-neutral-600 animate-slide-up stagger-2">
                    Jelajahi koleksi skincare & beauty premium dari brand-brand terpercaya. Formulasi canggih, bahan alami pilihan, hasil nyata.
                </p>

                <div class="flex flex-wrap items-center gap-4 animate-slide-up stagger-3">
                    <a href="{{ route('catalog') }}" class="btn-primary-gradient inline-flex items-center gap-2.5 rounded-full px-8 py-4 text-sm font-bold text-white shadow-lg shadow-pink-500/30">
                        <i class="fas fa-shopping-bag" aria-hidden="true"></i>
                        Belanja Sekarang
                    </a>
                    <a href="{{ route('track.index') }}" class="group inline-flex items-center gap-2 rounded-full border-2 border-pink-200 bg-white/80 backdrop-blur px-6 py-4 text-sm font-semibold text-pink-700 transition hover:border-pink-400 hover:bg-white">
                        <i class="fas fa-truck text-brand-400 transition group-hover:translate-x-1" aria-hidden="true"></i>
                        Lacak Pesanan
                    </a>
                </div>

                <div class="mt-12 flex items-center gap-8 animate-slide-up stagger-4">
                    <div>
                        <div class="counter text-3xl font-black text-pink-700">15K+</div>
                        <div class="text-xs text-neutral-500 font-medium">Pelanggan Puas</div>
                    </div>
                    <div class="h-10 w-px bg-pink-200" aria-hidden="true"></div>
                    <div>
                        <div class="counter text-3xl font-black text-pink-700">50+</div>
                        <div class="text-xs text-neutral-500 font-medium">Brand Pilihan</div>
                    </div>
                    <div class="h-10 w-px bg-pink-200" aria-hidden="true"></div>
                    <div>
                        <div class="counter text-3xl font-black text-pink-700">4.9</div>
                        <div class="flex items-center gap-1 text-xs text-neutral-500 font-medium">
                            <i class="fas fa-star text-amber-400" aria-hidden="true"></i> Rating Toko
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ======================== CATEGORY MARQUEE ======================== -->
    <section class="overflow-hidden border-y border-pink-100 bg-white py-5" aria-label="Kategori produk" aria-roledescription="carousel">
        <div class="flex animate-marquee whitespace-nowrap">
            @foreach(['Skincare', 'Serum', 'Moisturizer', 'Sunscreen', 'Cleanser', 'Toner', 'Masker', 'Body Care', 'Makeup', 'Hair Care', 'Skincare', 'Serum', 'Moisturizer', 'Sunscreen', 'Cleanser', 'Toner', 'Masker', 'Body Care', 'Makeup', 'Hair Care'] as $cat)
                <span class="mx-8 flex items-center gap-3 text-sm font-semibold text-neutral-500">
                    <span class="flex h-2 w-2 rounded-full bg-pink-400" aria-hidden="true"></span>
                    {{ $cat }}
                </span>
            @endforeach
        </div>
    </section>

    @auth
        <x-promo.home-strip :promos="$homePromos" />
    @endauth

    <!-- ======================== FEATURED PRODUCTS ======================== -->
    <section class="py-20 lg:py-28" aria-labelledby="featured-heading">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-14 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="mb-2 text-xs font-bold uppercase tracking-[0.2em] text-pink-500">Pilihan terbaik</p>
                    <h2 id="featured-heading" class="text-3xl font-black text-neutral-900 sm:text-4xl">Produk Unggulan</h2>
                    <p class="mt-2 max-w-sm text-neutral-500">Rekomendasi produk terlaris yang disukai pelanggan kami</p>
                </div>
                <a href="{{ route('catalog') }}" class="group inline-flex items-center gap-2 text-sm font-semibold text-pink-600 transition hover:gap-3">
                    Lihat semua koleksi
                    <i class="fas fa-arrow-right transition group-hover:translate-x-1" aria-hidden="true"></i>
                </a>
            </div>

            @if($featuredProducts->count() > 0)
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach($featuredProducts->take(8) as $index => $product)
                <article class="group card-float animate-slide-up stagger-{{ ($index % 5) + 1 }}">
                    <div class="relative overflow-hidden rounded-3xl border border-brand-100/50 bg-white shadow-sm">
                        @if($product->has_discount)
                            <span class="absolute left-3 top-3 z-10 rounded-full bg-gradient-to-r from-pink-500 to-pink-600 px-3 py-1 text-[11px] font-bold text-white shadow-md">
                                -{{ $product->discount_percentage }}%
                            </span>
                        @endif
                        @if($product->is_featured)
                            <span class="absolute right-3 top-3 z-10 rounded-full bg-white/90 backdrop-blur px-2.5 py-1 text-[10px] font-bold text-pink-600 shadow-sm border border-pink-100">
                                <i class="fas fa-heart text-pink-400 mr-1" aria-hidden="true"></i> Favorit
                            </span>
                        @endif

                        <a href="{{ route('products.show', $product->slug) }}" class="product-img-zoom relative flex aspect-[4/5] items-center justify-center overflow-hidden bg-gradient-to-br from-brand-50 to-pink-50">
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-full w-full object-cover" loading="lazy" decoding="async">
                            <div class="absolute inset-x-0 bottom-0 flex gap-2 bg-gradient-to-t from-black/40 to-transparent p-4 opacity-0 transition-opacity duration-300 group-hover:opacity-100" aria-hidden="true">
                                <button class="flex-1 rounded-xl bg-white/90 py-2.5 text-xs font-bold text-pink-700 backdrop-blur hover:bg-white transition">Lihat</button>
                                <button class="rounded-xl bg-pink-500/90 py-2.5 px-4 text-xs font-bold text-white backdrop-blur hover:bg-pink-600 transition">
                                    <i class="fas fa-cart-plus" aria-hidden="true"></i>
                                </button>
                            </div>
                        </a>

                        <div class="p-4">
                            <p class="mb-1 text-[11px] font-semibold uppercase tracking-wider text-pink-400">{{ $product->brand->name ?? '' }}</p>
                            <h3 class="mb-2 line-clamp-2 text-sm font-bold leading-snug text-neutral-900 transition group-hover:text-pink-700">
                                <a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a>
                            </h3>
                            <div class="mb-3 flex items-center gap-1.5">
                                <div class="flex text-amber-400 text-xs">
                                    @for($i=1;$i<=5;$i++)
                                        <i class="{{ $i <= round($product->average_rating ?? 0) ? 'fas' : 'far text-brand-200' }} fa-star" aria-hidden="true"></i>
                                    @endfor
                                </div>
                                <span class="text-[11px] text-neutral-400">({{ $product->review_count ?? 0 }})</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <div>
                                    @if($product->has_discount)
                                        <span class="text-lg font-black text-pink-600">{{ $product->formatted_discount_price }}</span>
                                        <span class="ml-1.5 text-xs text-neutral-400 line-through">{{ $product->formatted_price }}</span>
                                    @else
                                        <span class="text-lg font-black text-neutral-900">{{ $product->formatted_price }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
                @endforeach
            </div>
            @else
            <div class="flex flex-col items-center justify-center rounded-3xl border-2 border-dashed border-pink-200 bg-pink-50/50 py-20 text-center">
                <i class="fas fa-box-open text-5xl text-pink-200 mb-4" aria-hidden="true"></i>
                <p class="text-neutral-500">Belum ada produk untuk ditampilkan.</p>
                <a href="{{ route('catalog') }}" class="btn-primary-gradient mt-4 inline-flex items-center gap-2 rounded-full px-6 py-3 text-sm font-bold text-white">Jelajahi Katalog</a>
            </div>
            @endif
        </div>
    </section>

    <!-- ======================== CATEGORIES GRID ======================== -->
    <section class="py-20 lg:py-28 bg-gradient-to-b from-white to-brand-50/30" aria-labelledby="categories-heading">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-14 text-center">
                <p class="mb-2 text-xs font-bold uppercase tracking-[0.2em] text-pink-500">Jelajahi</p>
                <h2 id="categories-heading" class="text-3xl font-black text-neutral-900 sm:text-4xl">Kategori Produk</h2>
                <p class="mt-2 text-neutral-500">Temukan produk yang tepat untuk kebutuhan kulit Anda</p>
            </div>

            <div class="grid grid-cols-2 gap-4 lg:grid-cols-4 lg:gap-6">
                @foreach([
                    ['name' => 'Cleanser', 'icon' => 'fa-water', 'color' => 'from-blue-400/20 to-cyan-400/20', 'text' => 'text-blue-600'],
                    ['name' => 'Serum', 'icon' => 'fa-vial', 'color' => 'from-purple-400/20 to-pink-400/20', 'text' => 'text-purple-600'],
                    ['name' => 'Moisturizer', 'icon' => 'fa-pump-medical', 'color' => 'from-pink-400/20 to-rose-400/20', 'text' => 'text-pink-600'],
                    ['name' => 'Sunscreen', 'icon' => 'fa-sun', 'color' => 'from-amber-400/20 to-orange-400/20', 'text' => 'text-amber-600'],
                    ['name' => 'Toner', 'icon' => 'fa-leaf', 'color' => 'from-emerald-400/20 to-teal-400/20', 'text' => 'text-emerald-600'],
                    ['name' => 'Masker', 'icon' => 'fa-mask-face', 'color' => 'from-rose-400/20 to-pink-400/20', 'text' => 'text-rose-600'],
                    ['name' => 'Body Care', 'icon' => 'fa-hand-holding-heart', 'color' => 'from-orange-400/20 to-red-400/20', 'text' => 'text-orange-600'],
                    ['name' => 'Hair Care', 'icon' => 'fa-scissors', 'color' => 'from-neutral-400/20 to-neutral-500/20', 'text' => 'text-neutral-600'],
                ] as $cat)
                    <a href="{{ route('catalog') }}?category={{ urlencode($cat['name']) }}"
                       class="category-card card-float group relative flex flex-col items-center justify-center overflow-hidden rounded-3xl border border-brand-100/50 bg-white p-6 text-center shadow-sm lg:p-8">
                        <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br {{ $cat['color'] }} transition group-hover:scale-110">
                            <i class="fas {{ $cat['icon'] }} text-xl {{ $cat['text'] }}" aria-hidden="true"></i>
                        </div>
                        <h3 class="text-sm font-bold text-neutral-900 transition group-hover:text-pink-600">{{ $cat['name'] }}</h3>
                        <p class="mt-1 text-xs text-neutral-400">Lihat koleksi</p>
                        <img src="" alt="" class="cat-img absolute inset-0 -z-10 object-cover opacity-0 transition group-hover:opacity-20" aria-hidden="true">
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ======================== BRAND STORY / PROMISE ======================== -->
    <section class="relative overflow-hidden py-20 lg:py-28" aria-labelledby="promise-heading">
        <div class="absolute inset-0 hero-gradient-deep opacity-[0.03]" aria-hidden="true"></div>
        <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-12 lg:grid-cols-2 lg:gap-20 items-center">
                <div>
                    <p class="mb-3 text-xs font-bold uppercase tracking-[0.2em] text-pink-500">Mengapa Skinbae.ID</p>
                    <h2 id="promise-heading" class="mb-6 text-3xl font-black leading-tight text-neutral-900 sm:text-4xl lg:text-5xl">
                        Kulit Anda layak<br>dirawat dengan yang terbaik
                    </h2>
                    <p class="mb-8 leading-relaxed text-neutral-600">
                        Kami secara cermat memilih setiap produk dari brand-brand yang telah terbukti kualitasnya. Dengan dukungan tim ahli dan pelayanan personal, Skinbae.ID memastikan setiap pelanggan mendapatkan pengalaman belanja yang menyenangkan dan hasil yang nyata.
                    </p>
                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4 lg:grid-cols-2">
                        @foreach([
                            ['icon' => 'fa-certificate', 'val' => '100%', 'label' => 'Produk Original'],
                            ['icon' => 'fa-truck-fast', 'val' => 'Same Day', 'label' => 'Pengiriman Cepat'],
                            ['icon' => 'fa-shield-heart', 'val' => 'Expert', 'label' => 'Konsultasi Gratis'],
                            ['icon' => 'fa-rotate-left', 'val' => 'Easy', 'label' => '30 Hari Return'],
                        ] as $badge)
                            <div class="rounded-2xl border border-pink-100 bg-white/80 p-4 text-center backdrop-blur">
                                <i class="fas {{ $badge['icon'] }} mb-2 text-xl text-pink-500" aria-hidden="true"></i>
                                <div class="text-lg font-black text-neutral-900">{{ $badge['val'] }}</div>
                                <div class="text-xs text-neutral-500">{{ $badge['label'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="relative">
                    <div class="relative z-10 overflow-hidden rounded-3xl shadow-2xl shadow-pink-500/20">
                        <img src="https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=600&h=700&fit=crop" alt="Ilustrasi produk skincare premium" class="w-full object-cover" loading="lazy">
                    </div>
                    <div class="absolute -bottom-6 -right-6 z-20 rounded-2xl border border-pink-100 bg-white p-4 shadow-xl" aria-label="Promo badge">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-pink-100">
                                <i class="fas fa-gift text-pink-600" aria-hidden="true"></i>
                            </div>
                            <div>
                                <div class="text-sm font-bold text-neutral-900">Gratis Gift</div>
                                <div class="text-xs text-neutral-500">Min. pembelian Rp300rb</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ======================== TESTIMONIALS ======================== -->
    <section class="py-20 lg:py-28 bg-white" aria-labelledby="testimonials-heading">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-14 text-center">
                <p class="mb-2 text-xs font-bold uppercase tracking-[0.2em] text-pink-500">Kata mereka</p>
                <h2 id="testimonials-heading" class="text-3xl font-black text-neutral-900 sm:text-4xl">Testimoni Pelanggan</h2>
            </div>

            <div class="relative" x-data="{ active: 0 }">
                <div class="overflow-hidden">
                    <div class="flex transition-transform duration-500 ease-out" :style="`transform: translateX(-${active * 100}%)`">
                        @foreach([
                            ['q' => 'Produk skincare-nya original banget! Kulit saya sudah bersih dan cerah setelah rutin pakai serum dari Skinbae.ID. Recommend banget!', 'name' => 'Rina Wulandari', 'city' => 'Jakarta', 'rating' => 5, 'avatar' => 'RW'],
                            ['q' => 'Pengiriman super cepat, packaging juga rapi banget. CS-nya ramah dan helpfull. Pasti repeat order di sini!', 'name' => 'Anisa Putri', 'city' => 'Bandung', 'rating' => 5, 'avatar' => 'AP'],
                            ['q' => ' variety of products and fast delivery. My skin has never looked better. Highly recommended for anyone looking for genuine beauty products!', 'name' => 'Sarah Chen', 'city' => 'Surabaya', 'rating' => 5, 'avatar' => 'SC'],
                        ] as $i => $t)
                            <div class="w-full flex-shrink-0 px-4">
                                <div class="mx-auto max-w-2xl rounded-3xl border border-pink-100 bg-gradient-to-br from-white to-pink-50/50 p-8 text-center shadow-sm">
                                    <div class="mb-4 flex justify-center gap-1 text-amber-400" aria-label="Rating {{ $t['rating'] }} dari 5">
                                        @for($s=1;$s<=5;$s++)
                                            <i class="{{ $s <= $t['rating'] ? 'fas' : 'far' }} fa-star" aria-hidden="true"></i>
                                        @endfor
                                    </div>
                                    <blockquote class="mb-6 text-lg leading-relaxed text-neutral-700 italic">"{{ $t['q'] }}"</blockquote>
                                    <div class="flex items-center justify-center gap-3">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-pink-400 to-pink-600 text-xs font-bold text-white" aria-hidden="true">{{ $t['avatar'] }}</div>
                                        <div class="text-left">
                                            <div class="font-bold text-neutral-900">{{ $t['name'] }}</div>
                                            <div class="text-xs text-neutral-500">{{ $t['city'] }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-8 flex justify-center gap-2" role="tablist" aria-label="Navigasi testimoni">
                    @foreach(range(0, 2) as $i)
                        <button @click="active = {{ $i }}" role="tab" :aria-selected="active === {{ $i }}" :aria-label="`Testimoni {{ $i + 1 }}`"
                            class="h-2 rounded-full transition-all duration-300"
                            :class="active === {{ $i }} ? 'w-8 bg-pink-500' : 'w-2 bg-pink-200'"
                            x-bind:aria-current="active === {{ $i }} ? 'true' : 'false'">
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- ======================== NEWSLETTER / CTA ======================== -->
    <section class="py-20 lg:py-28" aria-labelledby="newsletter-heading">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="relative overflow-hidden rounded-3xl hero-gradient-deep p-8 sm:p-14 text-center text-white">
                <div class="absolute inset-0 opacity-10" aria-hidden="true">
                    <div class="absolute -top-20 -left-20 h-60 w-60 rounded-full bg-white blur-3xl"></div>
                    <div class="absolute -bottom-20 -right-20 h-60 w-60 rounded-full bg-white blur-3xl"></div>
                </div>
                <div class="relative z-10">
                    <i class="fas fa-envelope-open-text mb-4 text-4xl text-white/60" aria-hidden="true"></i>
                    <h2 id="newsletter-heading" class="mb-3 text-2xl font-black sm:text-3xl">Dapatkan Update Eksklusif</h2>
                    <p class="mb-8 max-w-md mx-auto text-white/80">Berlangganan newsletter kami untuk informasi produk terbaru, promo spesial, dan tips perawatan kulit.</p>
                    <form @submit.prevent="subscribe()" class="mx-auto flex max-w-md flex-col gap-3 sm:flex-row" aria-label="Form langganan newsletter">
                        <label for="email-subscribe" class="sr-only">Alamat email Anda</label>
                        <input type="email" id="email-subscribe" x-model="email" required placeholder="Ketik email Anda di sini..." class="cta-input flex-1 rounded-full bg-white/20 backdrop-blur border border-white/30 px-6 py-3.5 text-sm text-white placeholder-white/60 outline-none transition focus:bg-white/30 focus:border-white/60" aria-label="Email">
                        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-full bg-white px-8 py-3.5 text-sm font-bold text-pink-600 transition hover:bg-pink-50">
                            <span x-show="!subscribed">Berlangganan</span>
                            <span x-show="subscribed"><i class="fas fa-check" aria-hidden="true"></i> Terdaftar!</span>
                        </button>
                    </form>
                    <p x-show="subscribed" x-transition class="mt-3 text-sm text-white/80">✓ Anda berhasil terdaftar. Cek inbox untuk konfirmasi.</p>
                    <p class="mt-4 text-xs text-white/50">Dengan berlangganan, Anda menyetujui kebijakan privasi kami. Unsubscribe kapan saja.</p>
                </div>
            </div>
        </div>
    </section>

</div>

@endsection

@push('scripts')
<script>
function homepage() {
    return {
        email: '',
        subscribed: false,
        init() {
            // Intersection Observer for scroll animations
            if ('IntersectionObserver' in window) {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('animate-slide-up');
                            observer.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

                document.querySelectorAll('.observe-animate').forEach(el => observer.observe(el));
            }
        },
        subscribe() {
            if (this.email) {
                this.subscribed = true;
                this.email = '';
                setTimeout(() => { this.subscribed = false; }, 5000);
            }
        }
    }
}
</script>
@endpush
