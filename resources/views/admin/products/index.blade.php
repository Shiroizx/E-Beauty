@extends('layouts.admin')

@section('title', 'Manajemen Produk')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0"><i class="fas fa-box me-2"></i> Manajemen Produk</h2>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary rounded-pill">
            <i class="fas fa-plus me-2"></i> Tambah Produk
        </a>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('admin.products.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama atau SKU..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="category_id" class="form-select">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="brand_id" class="form-select">
                        <option value="">Semua Brand</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>
                                {{ $brand->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Products Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Produk</th>
                            <th>Kategori</th>
                            <th>Brand</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $product->image_url }}" alt="" class="rounded" width="40" height="40" style="object-fit: cover;">
                                        <div class="ms-3">
                                            <h6 class="mb-0">{{ Str::limit($product->name, 30) }}</h6>
                                            <small class="text-muted">{{ $product->sku }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $product->category->name }}</td>
                                <td>{{ $product->brand->name }}</td>
                                <td>{{ $product->formatted_price }}</td>
                                <td>
                                    @if($product->stock->quantity <= $product->stock->min_quantity)
                                        <span class="badge bg-danger">Low: {{ $product->stock->quantity }}</span>
                                    @else
                                        <span class="badge bg-success">{{ $product->stock->quantity }}</span>
                                    @endif
                                </td>
                                <td>
                                    <form action="{{ route('admin.products.toggle-status', $product->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-link text-decoration-none {{ $product->is_active ? 'text-success' : 'text-secondary' }}">
                                            <i class="fas fa-toggle-{{ $product->is_active ? 'on' : 'off' }} fa-lg"></i>
                                        </button>
                                    </form>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-outline-info me-1">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus produk ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">Belum ada produk</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($products->hasPages())
                <div class="card-footer bg-white border-0 py-3">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
