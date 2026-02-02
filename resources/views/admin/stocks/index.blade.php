@extends('layouts.admin')

@section('title', 'Manajemen Stok')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0"><i class="fas fa-warehouse me-2"></i> Manajemen Stok</h2>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.stocks.index', ['filter' => 'low_stock']) }}" class="btn btn-outline-warning">
                <i class="fas fa-exclamation-triangle me-2"></i> Low Stock
            </a>
            <a href="{{ route('admin.stocks.index', ['filter' => 'expiring']) }}" class="btn btn-outline-danger">
                <i class="fas fa-clock me-2"></i> Expiring Soon
            </a>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-white">
                <div class="card-body">
                    <p class="text-muted small mb-1">Total Items</p>
                    <h3 class="mb-0">{{ $statistics['total_items'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-white">
                <div class="card-body">
                    <p class="text-muted small mb-1">Total Quantity</p>
                    <h3 class="mb-0">{{ $statistics['total_quantity'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-white">
                <div class="card-body">
                    <p class="text-muted small mb-1">Low Stock Items</p>
                    <h3 class="mb-0 text-warning">{{ $statistics['low_stock_count'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-white">
                <div class="card-body">
                    <p class="text-muted small mb-1">Expiring Items</p>
                    <h3 class="mb-0 text-danger">{{ $statistics['expiring_soon_count'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Search -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('admin.stocks.index') }}" method="GET">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama produk atau SKU..." value="{{ request('search') }}">
                    <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i> Cari</button>
                    @if(request()->has('filter') || request()->has('search'))
                        <a href="{{ route('admin.stocks.index') }}" class="btn btn-outline-secondary">Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Stock Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Produk</th>
                            <th>SKU</th>
                            <th>Warehouse</th>
                            <th>Batch</th>
                            <th>Expiry</th>
                            <th>Stok</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stocks as $stock)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold">{{ $stock->product->name }}</div>
                                </td>
                                <td>{{ $stock->product->sku ?? '-' }}</td>
                                <td>{{ $stock->warehouse_location ?? '-' }}</td>
                                <td>{{ $stock->batch_number ?? '-' }}</td>
                                <td>
                                    @if($stock->expiry_date)
                                        <span class="{{ $stock->is_expiring_soon ? 'text-danger fw-bold' : '' }}">
                                            {{ $stock->expiry_date->format('d M Y') }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($stock->is_low_stock)
                                        <span class="badge bg-danger">Low: {{ $stock->quantity }} / {{ $stock->min_quantity }}</span>
                                    @else
                                        <span class="badge bg-success">{{ $stock->quantity }}</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#updateStockModal{{ $stock->product_id }}">
                                        <i class="fas fa-edit"></i> Update
                                    </button>
                                </td>
                            </tr>
                            
                            <!-- Update Modal -->
                            <div class="modal fade" id="updateStockModal{{ $stock->product_id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('admin.stocks.update', $stock->product_id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <div class="modal-header">
                                                <h5 class="modal-title">Update Stok: {{ $stock->product->name }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-start">
                                                <div class="mb-3">
                                                    <label class="form-label">Jumlah Stok</label>
                                                    <input type="number" name="quantity" class="form-control" value="{{ $stock->quantity }}" required min="0">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Minimum Stok (Alert)</label>
                                                    <input type="number" name="min_quantity" class="form-control" value="{{ $stock->min_quantity }}" min="0">
                                                </div>
                                                <div class="row">
                                                    <div class="col-6 mb-3">
                                                        <label class="form-label">Lokasi Gudang</label>
                                                        <input type="text" name="warehouse_location" class="form-control" value="{{ $stock->warehouse_location }}">
                                                    </div>
                                                    <div class="col-6 mb-3">
                                                        <label class="form-label">Batch No</label>
                                                        <input type="text" name="batch_number" class="form-control" value="{{ $stock->batch_number }}">
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Expiry Date</label>
                                                    <input type="date" name="expiry_date" class="form-control" value="{{ $stock->expiry_date ? $stock->expiry_date->format('Y-m-d') : '' }}">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">Belum ada data stok</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="card-footer bg-white border-0 py-3">
                {{ $stocks->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
