@extends('layouts.admin')

@section('title', 'Tambah Produk Baru')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h4 mb-0"><i class="fas fa-plus-circle me-2"></i> Tambah Produk Baru</h2>
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary rounded-pill">
                    <i class="fas fa-arrow-left me-2"></i> Kembali
                </a>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row g-4">
                            <!-- Basic Info -->
                            <div class="col-md-8">
                                <h5 class="mb-3 text-secondary">Informasi Dasar</h5>
                                
                                <div class="mb-3">
                                    <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Brand <span class="text-danger">*</span></label>
                                        <select name="brand_id" class="form-select" required>
                                            <option value="">Pilih Brand</option>
                                            @foreach($brands as $brand)
                                                <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                                    {{ $brand->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Kategori <span class="text-danger">*</span></label>
                                        <select name="category_id" class="form-select" required>
                                            <option value="">Pilih Kategori</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                                @foreach($category->children as $child)
                                                    <option value="{{ $child->id }}" {{ old('category_id') == $child->id ? 'selected' : '' }}>
                                                        &nbsp;&nbsp; - {{ $child->name }}
                                                    </option>
                                                @endforeach
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Deskripsi</label>
                                    <textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Cara Penggunaan</label>
                                    <textarea name="how_to_use" class="form-control" rows="3">{{ old('how_to_use') }}</textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Komposisi (Ingredients)</label>
                                    <textarea name="ingredients" class="form-control" rows="3">{{ old('ingredients') }}</textarea>
                                </div>
                            </div>

                            <!-- Pricing & Details -->
                            <div class="col-md-4">
                                <h5 class="mb-3 text-secondary">Detail & Harga</h5>
                                
                                <div class="mb-3">
                                    <label class="form-label">Harga (IDR) <span class="text-danger">*</span></label>
                                    <input type="number" name="price" class="form-control" value="{{ old('price') }}" required min="0">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Harga Diskon (Optional)</label>
                                    <input type="number" name="discount_price" class="form-control" value="{{ old('discount_price') }}" min="0">
                                    <small class="text-muted">Kosongkan jika tidak ada diskon</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">SKU</label>
                                    <input type="text" name="sku" class="form-control" value="{{ old('sku') }}">
                                </div>
                                
                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <label class="form-label">Stok Awal</label>
                                        <input type="number" name="stock_quantity" class="form-control" value="{{ old('stock_quantity', 10) }}" min="0">
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label class="form-label">Min. Stok</label>
                                        <input type="number" name="min_quantity" class="form-control" value="{{ old('min_quantity', 5) }}" min="0">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Gambar Utama</label>
                                    <input type="file" name="image" class="form-control" accept="image/*">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Tipe Kulit</label>
                                    <div class="card p-2" style="max-height: 200px; overflow-y: auto;">
                                        @foreach($skinTypes as $skinType)
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="skin_type_ids[]" value="{{ $skinType->id }}" id="st{{ $skinType->id }}" {{ in_array($skinType->id, old('skin_type_ids', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="st{{ $skinType->id }}">
                                                    {{ $skinType->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" checked>
                                    <label class="form-check-label" for="isActive">Aktif / Dijual</label>
                                </div>
                                
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="isFeatured">
                                    <label class="form-check-label" for="isFeatured">Produk Unggulan</label>
                                </div>

                                <button type="submit" class="btn btn-success w-100 py-2 fw-bold">
                                    <i class="fas fa-save me-2"></i> Simpan Produk
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
