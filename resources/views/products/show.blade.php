@extends('layouts.app')

@section('title', $product->name . ' - E-Beauty')

@section('content')
<div class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('catalog') }}" class="text-decoration-none text-muted">Catalog</a></li>
            <li class="breadcrumb-item"><a href="{{ route('catalog', ['category' => $product->category->slug]) }}" class="text-decoration-none text-muted">{{ $product->category->name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($product->name, 30) }}</li>
        </ol>
    </nav>

    <div class="row g-5">
        <!-- Product Image -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm overflow-hidden">
                <img src="{{ $product->image_url }}" class="card-img-top w-100" alt="{{ $product->name }}" style="max-height: 500px; object-fit: contain; background: #f8f9fa;">
            </div>
        </div>

        <!-- Product Details -->
        <div class="col-lg-6">
            <a href="{{ route('catalog', ['brand' => $product->brand->slug]) }}" class="badge bg-light text-dark text-decoration-none border mb-2">
                {{ $product->brand->name }}
            </a>
            <h1 class="fw-bold mb-3">{{ $product->name }}</h1>
            
            <div class="d-flex align-items-center mb-3">
                <div class="text-warning me-2">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="fas fa-star{{ $i <= $product->average_rating ? '' : '-half-alt' . ($i - 0.5 > $product->average_rating ? ' text-muted opacity-25' : '') }}"></i>
                    @endfor
                </div>
                <span class="text-muted small">({{ $product->reviews_count }} reviews)</span>
            </div>

            <div class="mb-4">
                @if($product->has_discount)
                    <div class="d-flex align-items-center gap-2">
                        <h2 class="fw-bold text-gradient mb-0">{{ $product->formatted_final_price }}</h2>
                        <span class="text-muted text-decoration-line-through">{{ $product->formatted_price }}</span>
                        <span class="badge bg-danger">{{ $product->discount_percentage }}% OFF</span>
                    </div>
                @else
                    <h2 class="fw-bold text-gradient mb-0">{{ $product->formatted_price }}</h2>
                @endif
            </div>

            <p class="text-muted mb-4" style="line-height: 1.8;">
                {{ $product->description }}
            </p>

            <div class="mb-4">
                <label class="form-label text-secondary fw-semibold">Availability</label>
                <div>
                    @if($product->stock_quantity > 0)
                        <span class="text-success"><i class="fas fa-check-circle me-1"></i> In Stock ({{ $product->stock_quantity }} items)</span>
                    @else
                        <span class="text-danger"><i class="fas fa-times-circle me-1"></i> Out of Stock</span>
                    @endif
                </div>
            </div>

            <div class="d-flex gap-3 mb-5">
                <button class="btn btn-primary px-5 py-2 rounded-pill shadow-sm" {{ $product->stock_quantity < 1 ? 'disabled' : '' }}>
                    <i class="fas fa-shopping-bag me-2"></i> Add to Cart
                </button>
                <button class="btn btn-outline-secondary px-3 py-2 rounded-pill">
                    <i class="far fa-heart"></i>
                </button>
            </div>

            <!-- Additional Info -->
            <div class="accordion" id="productInfoAccordion">
                <div class="accordion-item border-0 shadow-sm mb-2 rounded overflow-hidden">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapseShipping">
                            <i class="fas fa-truck me-2 text-secondary"></i> Shipping & Delivery
                        </button>
                    </h2>
                    <div id="collapseShipping" class="accordion-collapse collapse" data-bs-parent="#productInfoAccordion">
                        <div class="accordion-body text-muted small">
                            Gratis ongkir untuk pembelian di atas Rp 500.000. Pengiriman dilakukan setiap hari kerja (Senin-Jumat).
                        </div>
                    </div>
                </div>
                <div class="accordion-item border-0 shadow-sm rounded overflow-hidden">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapseReturn">
                            <i class="fas fa-undo me-2 text-secondary"></i> Returns & Exchanges
                        </button>
                    </h2>
                    <div id="collapseReturn" class="accordion-collapse collapse" data-bs-parent="#productInfoAccordion">
                        <div class="accordion-body text-muted small">
                            Jaminan uang kembali 100% jika produk tidak orisinal. Penukaran barang maksimal 7 hari setelah diterima.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
        <div class="mt-5 pt-5 border-top">
            <h3 class="fw-bold mb-4">Related Products</h3>
            <div class="row g-4">
                @foreach($relatedProducts as $related)
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="card h-100 product-card shadow-sm">
                            @if($related->has_discount)
                                <span class="position-absolute top-0 start-0 badge bg-danger m-3">-{{ $related->discount_percentage }}%</span>
                            @endif
                            <div class="position-relative overflow-hidden">
                                <img src="{{ $related->image_url }}" class="card-img-top product-image" alt="{{ $related->name }}" style="height: 250px; object-fit: cover;">
                            </div>
                            <div class="card-body">
                                <p class="text-muted small mb-1">{{ $related->brand->name }}</p>
                                <h6 class="card-title fw-bold mb-2">
                                    <a href="{{ route('products.show', $related->slug) }}" class="text-decoration-none text-dark stretched-link">
                                        {{ Str::limit($related->name, 40) }}
                                    </a>
                                </h6>
                                <div class="mb-2">
                                    @if($related->has_discount)
                                        <small class="text-decoration-line-through text-muted">{{ $related->formatted_price }}</small>
                                        <span class="d-block fw-bold text-gradient">{{ $related->formatted_final_price }}</span>
                                    @else
                                        <span class="fw-bold text-gradient">{{ $related->formatted_price }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

@push('styles')
<style>
    .text-gradient {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
</style>
@endpush
@endsection
