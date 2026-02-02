@extends('layouts.admin')

@section('title', 'Edit Kategori')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h4 mb-0"><i class="fas fa-edit me-2"></i> Edit Kategori: {{ $category->name }}</h2>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary rounded-pill">
                    <i class="fas fa-arrow-left me-2"></i> Kembali
                </a>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $category->name) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Parent Kategori</label>
                            <select name="parent_id" class="form-select">
                                <option value="">Tidak Ada (Root Category)</option>
                                @foreach($parentCategories as $parent)
                                    <option value="{{ $parent->id }}" {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}>
                                        {{ $parent->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Icon</label>
                            @if($category->icon)
                                <div class="mb-2">
                                    <img src="{{ Storage::url($category->icon) }}" alt="Preview" class="rounded" width="40">
                                </div>
                            @endif
                            <input type="file" name="icon" class="form-control" accept="image/*">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="description" class="form-control" rows="4">{{ old('description', $category->description) }}</textarea>
                        </div>
                        
                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" {{ $category->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="isActive">Aktif / Tampilkan</label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                            <i class="fas fa-save me-2"></i> Update Kategori
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
