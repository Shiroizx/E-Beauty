@extends('layouts.admin')

@section('title', 'Edit Brand')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h4 mb-0"><i class="fas fa-edit me-2"></i> Edit Brand: {{ $brand->name }}</h2>
                <a href="{{ route('admin.brands.index') }}" class="btn btn-outline-secondary rounded-pill">
                    <i class="fas fa-arrow-left me-2"></i> Kembali
                </a>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('admin.brands.update', $brand->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label">Nama Brand <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $brand->name) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Logo</label>
                            @if($brand->logo)
                                <div class="mb-2">
                                    <img src="{{ Storage::url($brand->logo) }}" alt="Preview" class="rounded" height="50">
                                </div>
                            @endif
                            <input type="file" name="logo" class="form-control" accept="image/*">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="description" class="form-control" rows="4">{{ old('description', $brand->description) }}</textarea>
                        </div>
                        
                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" {{ $brand->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="isActive">Aktif / Tampilkan</label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                            <i class="fas fa-save me-2"></i> Update Brand
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
