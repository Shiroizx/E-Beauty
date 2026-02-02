@extends('layouts.admin')

@section('title', 'Manajemen Kategori')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0"><i class="fas fa-folder me-2"></i> Manajemen Kategori</h2>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary rounded-pill">
            <i class="fas fa-plus me-2"></i> Tambah Kategori
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Kategori</th>
                            <th>Parent Kategori</th>
                            <th>Total Produk</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        @if($category->icon)
                                            <img src="{{ Storage::url($category->icon) }}" alt="" class="rounded me-2" width="30">
                                        @endif
                                        <span class="fw-bold">{{ $category->name }}</span>
                                    </div>
                                </td>
                                <td>
                                    @if($category->parent)
                                        <span class="badge bg-light text-dark border">{{ $category->parent->name }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td><span class="badge bg-secondary">{{ $category->products_count }}</span></td>
                                <td>
                                    @if($category->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-danger">Non-Aktif</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-sm btn-outline-info me-1">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus kategori ini?');">
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
                                <td colspan="5" class="text-center py-5 text-muted">Belum ada kategori</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="card-footer bg-white border-0 py-3">
                {{ $categories->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
