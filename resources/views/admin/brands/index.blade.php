@extends('layouts.admin')

@section('title', 'Manajemen Brand')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0"><i class="fas fa-tag me-2"></i> Manajemen Brand</h2>
        <a href="{{ route('admin.brands.create') }}" class="btn btn-primary rounded-pill">
            <i class="fas fa-plus me-2"></i> Tambah Brand
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Brand</th>
                            <th>Deskripsi</th>
                            <th>Total Produk</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($brands as $brand)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        @if($brand->logo)
                                            <img src="{{ Storage::url($brand->logo) }}" alt="" class="rounded-circle me-3" width="40" height="40" style="object-fit: cover;">
                                        @else
                                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                                <i class="fas fa-tag text-muted"></i>
                                            </div>
                                        @endif
                                        <span class="fw-bold">{{ $brand->name }}</span>
                                    </div>
                                </td>
                                <td>{{ Str::limit($brand->description, 50) }}</td>
                                <td><span class="badge bg-secondary">{{ $brand->products_count }} Produk</span></td>
                                <td>
                                    @if($brand->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-danger">Non-Aktif</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('admin.brands.edit', $brand->id) }}" class="btn btn-sm btn-outline-info me-1">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.brands.destroy', $brand->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus brand ini?');">
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
                                <td colspan="5" class="text-center py-5 text-muted">Belum ada brand</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="card-footer bg-white border-0 py-3">
                {{ $brands->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
