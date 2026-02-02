@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
<div class="container-fluid py-4">
    <h2 class="mb-4"><i class="fas fa-tachometer-alt"></i> Dashboard</h2>
    
    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Produk</p>
                            <h3 class="mb-0">{{ $stats['total_products'] }}</h3>
                        </div>
                        <div class="fs-1 text-primary">
                            <i class="fas fa-box"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Brand</p>
                            <h3 class="mb-0">{{ $stats['total_brands'] }}</h3>
                        </div>
                        <div class="fs-1 text-success">
                            <i class="fas fa-tag"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Stok Rendah</p>
                            <h3 class="mb-0 text-warning">{{ $stats['stock_stats']['low_stock_count'] }}</h3>
                        </div>
                        <div class="fs-1 text-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Review Pending</p>
                            <h3 class="mb-0 text-info">{{ $stats['review_stats']['pending_reviews'] }}</h3>
                        </div>
                        <div class="fs-1 text-info">
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Low Stock Alert -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-box-open text-warning"></i> Produk Stok Rendah</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Stok</th>
                                    <th>Min</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lowStockProducts->take(5) as $item)
                                    <tr>
                                        <td>{{ Str::limit($item['product']->name, 30) }}</td>
                                        <td><span class="badge bg-warning">{{ $item['current_quantity'] }}</span></td>
                                        <td>{{ $item['min_quantity'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">Tidak ada produk dengan stok rendah</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($lowStockProducts->count() > 5)
                        <a href="{{ route('admin.stocks.low-stock') }}" class="btn btn-sm btn-outline-primary w-100">
                            Lihat Semua
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Pending Reviews -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-comments text-info"></i> Review Menunggu Persetujuan</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>User</th>
                                    <th>Rating</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingReviews->take(5) as $review)
                                    <tr>
                                        <td>{{ Str::limit($review->product->name, 25) }}</td>
                                        <td>{{ $review->user->name }}</td>
                                        <td>
                                            <span class="rating-stars">
                                                @for($i = 1; $i <= $review->rating; $i++)
                                                    <i class="fas fa-star"></i>
                                                @endfor
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">Tidak ada review pending</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($pendingReviews->count() > 5)
                        <a href="{{ route('admin.reviews.index', ['status' => 'pending']) }}" class="btn btn-sm btn-outline-primary w-100">
                            Lihat Semua
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
