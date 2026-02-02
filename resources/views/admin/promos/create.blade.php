@extends('layouts.admin')

@section('title', 'Buat Promo Baru')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h4 mb-0"><i class="fas fa-percent me-2"></i> Buat Promo Baru</h2>
                <a href="{{ route('admin.promos.index') }}" class="btn btn-outline-secondary rounded-pill">
                    <i class="fas fa-arrow-left me-2"></i> Kembali
                </a>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('admin.promos.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Promo <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="Contoh: Diskon Lebaran">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kode Promo <span class="text-danger">*</span></label>
                                <input type="text" name="code" class="form-control text-uppercase" value="{{ old('code') }}" required placeholder="LEBARAN2026">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tipe Diskon <span class="text-danger">*</span></label>
                                <select name="discount_type" class="form-select" required>
                                    <option value="percentage" {{ old('discount_type') == 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
                                    <option value="fixed" {{ old('discount_type') == 'fixed' ? 'selected' : '' }}>Nominal Tetap (Rp)</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nilai Diskon <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="discount_value" class="form-control" value="{{ old('discount_value') }}" required min="0">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="start_date" class="form-control" value="{{ old('start_date') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Berakhir <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="end_date" class="form-control" value="{{ old('end_date') }}" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Batas Penggunaan (Optional)</label>
                            <input type="number" name="usage_limit" class="form-control" value="{{ old('usage_limit') }}" min="0">
                            <small class="text-muted">Kosongkan jika tidak terbatas</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Minimum Pembelian (Optional)</label>
                            <input type="number" name="min_purchase_amount" class="form-control" value="{{ old('min_purchase_amount') }}" min="0">
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Produk Spesifik (Optional)</label>
                            <div class="card p-2" style="max-height: 200px; overflow-y: auto;">
                                @foreach($products as $product)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="product_ids[]" value="{{ $product->id }}" id="p{{ $product->id }}" {{ in_array($product->id, old('product_ids', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="p{{ $product->id }}">
                                            {{ $product->name }} ({{ $product->sku }})
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            <small class="text-muted">Pilih produk jika promo hanya berlaku untuk produk tertentu</small>
                        </div>
                        
                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" checked>
                            <label class="form-check-label" for="isActive">Aktifkan Promo</label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                            <i class="fas fa-save me-2"></i> Simpan Promo
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
