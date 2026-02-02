@extends('layouts.app')

@section('title', 'Home - E-Beauty')

@section('content')
<!-- Hero Section -->
<section class="hero-section" style="background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%); padding: 4rem 0; color: white;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-3">Temukan Produk Kecantikan Terbaik</h1>
                <p class="lead mb-4">Koleksi lengkap produk skincare, makeup, dan perawatan kecantikan dari brand terpercaya</p>
                <a href="{{ route('catalog') }}" class="btn btn-light btn-lg px-5 rounded-pill">
                    <i class="fas fa-shopping-bag"></i> Belanja Sekarang
                </a>
            </div>
            <div class="col-lg-6 text-center d-none d-lg-block">
                <i class="fas fa-spa fa-10x" style="opacity: 0.2;"></i>
            </div>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">✨ Produk Pilihan</h2>
            <p class="text-muted">Produk terbaik yang kami rekomendasikan untuk Anda</p>
        </div>
        
        <div class="row g-4">
            @forelse($featuredProducts as $product)
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="card product-card h-100 shadow-sm">
                        @if($product->has_discount)
                            <span class="badge badge-discount">-{{ $product->discount_percentage }}%</span>
                        @endif
                        
                        <img src="{{ $product->image_url }}" class="product-image card-img-top" alt="{{ $product->name }}">
                        
                        <div class="card-body">
                            <p class="text-muted small mb-1">{{ $product->brand->name }}</p>
                            <h6 class="card-title mb-2">{{ Str::limit($product->name, 40) }}</h6>
                            
                            <div class="mb-2">
                                @if($product->review_count > 0)
                                    <span class="rating-stars">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star{{ $i <= $product->average_rating ? '' : '-o' }}"></i>
                                        @endfor
                                    </span>
                                    <small class="text-muted">({{ $product->review_count }})</small>
                                @else
                                    <small class="text-muted">Belum ada review</small>
                                @endif
                            </div>
                            
                            <div class="mb-3">
                                @if($product->has_discount)
                                    <div class="price-original">{{ $product->formatted_price }}</div>
                                @endif
                                <div class="price-final">{{ $product->formatted_final_price }}</div>
                            </div>
                            
                            <a href="{{ route('products.show', $product->slug) }}" class="btn btn-primary btn-sm w-100">
                                <i class="fas fa-eye"></i> Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center">
                    <p class="text-muted">Belum ada produk featured</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Categories -->
<section class="py-5" style="background: #f8f9fa;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">🌸 Kategori Produk</h2>
            <p class="text-muted">Pilih kategori sesuai kebutuhan Anda</p>
        </div>
        
        <div class="row g-4">
            @foreach($categories as $category)
                <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                    <div class="card text-center h-100 shadow-sm">
                        <div class="card-body">
                            <div class="mb-3" style="font-size: 2.5rem;">
                                <i class="fas fa-box-open" style="color: var(--primary-color);"></i>
                            </div>
                            <h6 class="card-title mb-1">{{ $category->name }}</h6>
                            <small class="text-muted">{{ $category->products_count }} produk</small>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- New Arrivals -->
@if($newArrivals->count() > 0)
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">🆕 Produk Terbaru</h2>
            <p class="text-muted">Produk terbaru yang baru saja ditambahkan</p>
        </div>
        
        <div class="row g-4">
            @foreach($newArrivals as $product)
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="card product-card h-100 shadow-sm">
                        <img src="{{ $product->image_url }}" class="product-image card-img-top" alt="{{ $product->name }}">
                        
                        <div class="card-body">
                            <p class="text-muted small mb-1">{{ $product->brand->name }}</p>
                            <h6 class="card-title mb-2">{{ Str::limit($product->name, 40) }}</h6>
                            <div class="price-final mb-3">{{ $product->formatted_final_price }}</div>
                            
                            <a href="{{ route('products.show', $product->slug) }}" class="btn btn-outline-primary btn-sm w-100">
                                <i class="fas fa-eye"></i> Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Brands -->
<section class="py-5" style="background: #f8f9fa;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">💎 Brand Terpercaya</h2>
            <p class="text-muted">Kami bekerja sama dengan brand-brand ternama</p>
        </div>
        
        <div class="row g-3">
            @foreach($brands as $brand)
                <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                    <div class="card text-center p-3 shadow-sm">
                        <h6 class="mb-1">{{ $brand->name }}</h6>
                        <small class="text-muted">{{ $brand->products_count }} produk</small>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="py-5">
    <div class="container">
        <div class="card border-0 shadow-lg" style="background: var(--gradient-peach);">
            <div class="card-body text-center text-white py-5">
                <h2 class="fw-bold mb-3">Siap Menemukan Produk Favorit Anda?</h2>
                <p class="lead mb-4">Jelajahi katalog lengkap kami dan temukan produk yang sempurna untuk Anda</p>
                <a href="{{ route('catalog') }}" class="btn btn-light btn-lg px-5 rounded-pill">
                    <i class="fas fa-search"></i> Jelajahi Katalog
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
