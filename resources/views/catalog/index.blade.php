@extends('layouts.app')

@section('title', 'Catalog - E-Beauty')

@section('content')
<div class="container py-5">
    <div class="row">
        <!-- Sidebar Filters -->
        <div class="col-lg-3 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="mb-4"><i class="fas fa-filter"></i> Filter Produk</h5>
                    
                    <form action="{{ route('catalog') }}" method="GET">
                        <!-- Search -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Search</label>
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="Cari produk..." value="{{ request('search') }}">
                                <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                            </div>
                        </div>

                        <!-- Category -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Kategori</label>
                            <select name="category" class="form-select">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $category)
                                    <optgroup label="{{ $category->name }}">
                                        @foreach($category->children as $subcat)
                                            <option value="{{ $subcat->id }}" {{ request('category') == $subcat->id ? 'selected' : '' }}>
                                                {{ $subcat->name }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>

                        <!-- Brand -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Brand</label>
                            @foreach($brands->take(8) as $brand)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="brands[]" value="{{ $brand->id }}" 
                                        id="brand{{ $brand->id }}" {{ in_array($brand->id, request('brands', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="brand{{ $brand->id }}">
                                        {{ $brand->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        <!-- Skin Type -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Tipe Kulit</label>
                            @foreach($skinTypes as $skinType)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="skin_types[]" value="{{ $skinType->id }}" 
                                        id="skin{{ $skinType->id }}" {{ in_array($skinType->id, request('skin_types', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="skin{{ $skinType->id }}">
                                        {{ $skinType->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        <!-- Price Range -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Harga</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="number" name="min_price" class="form-control" placeholder="Min" value="{{ request('min_price') }}">
                                </div>
                                <div class="col-6">
                                    <input type="number" name="max_price" class="form-control" placeholder="Max" value="{{ request('max_price') }}">
                                </div>
                            </div>
                        </div>

                        <!-- Stock -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="in_stock_only" value="1" 
                                    id="inStock" {{ request('in_stock_only') ? 'checked' : '' }}>
                                <label class="form-check-label" for="inStock">
                                    Hanya yang tersedia
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-filter"></i> Terapkan Filter
                        </button>
                        <a href="{{ route('catalog') }}" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-redo"></i> Reset Filter
                        </a>
                    </form>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="col-lg-9">
            <!-- Sort -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">
                    @if(request('search'))
                        Hasil Pencarian: "{{ request('search') }}"
                    @else
                        Semua Produk
                    @endif
                    <span class="badge bg-secondary">{{ $products->total() }}</span>
                </h4>
                
                <div class="d-flex gap-2">
                    <select name="sort_by" class="form-select form-select-sm" style="width: auto;" onchange="window.location.href='{{ route('catalog') }}?' + new URLSearchParams({...Object.fromEntries(new URLSearchParams(window.location.search)), sort_by: this.value})">
                        <option value="newest" {{ request('sort_by') == 'newest' ? 'selected' : '' }}>Terbaru</option>
                        <option value="price_low_high" {{ request('sort_by') == 'price_low_high' ? 'selected' : '' }}>Harga: Terendah</option>
                        <option value="price_high_low" {{ request('sort_by') == 'price_high_low' ? 'selected' : '' }}>Harga: Tertinggi</option>
                        <option value="rating" {{ request('sort_by') == 'rating' ? 'selected' : '' }}>Rating Tertinggi</option>
                        <option value="popularity" {{ request('sort_by') == 'popularity' ? 'selected' : '' }}>Terpopuler</option>
                    </select>
                </div>
            </div>

            <!-- Products -->
            <div class="row g-4">
                @forelse($products as $product)
                    <div class="col-lg-4 col-md-6">
                        <div class="card product-card h-100 shadow-sm">
                            @if($product->has_discount)
                                <span class="badge badge-discount">-{{ $product->discount_percentage }}%</span>
                            @endif
                            
                            @if(!$product->is_in_stock)
                                <span class="badge bg-secondary position-absolute top-0 start-0 m-2">Stok Habis</span>
                            @endif
                            
                            <img src="{{ $product->image_url }}" class="product-image card-img-top" alt="{{ $product->name }}">
                            
                            <div class="card-body">
                                <p class="text-muted small mb-1">{{ $product->brand->name }}</p>
                                <h6 class="card-title mb-2">{{ Str::limit($product->name, 50) }}</h6>
                                
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
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle"></i> Tidak ada produk yang ditemukan. Silakan coba filter lain.
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-5 d-flex justify-content-center">
                {{ $products->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
