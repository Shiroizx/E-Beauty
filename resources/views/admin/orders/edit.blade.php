@extends('layouts.admin')

@section('title', 'Edit Pesanan — Admin Skinbae.ID')
@section('page_title', 'Edit Pesanan')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">Edit Pesanan #{{ $order->order_number }}</h4>
            <div class="text-muted small">Dibuat pada {{ $order->created_at->format('d M Y, H:i') }} WIB</div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-info btn-sm text-white">
                <i class="fas fa-eye me-1"></i> Lihat Detail
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('admin.orders.update', $order) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row g-4">
            <!-- Status & Payment -->
            <div class="col-lg-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom border-light py-3">
                        <h6 class="mb-0 fw-bold text-secondary"><i class="fas fa-sync-alt me-2 text-warning"></i> Status Pesanan</h6>
                    </div>
                    <div class="card-body py-4">
                        <div class="mb-4">
                            <label for="status" class="form-label fw-semibold text-dark small">Status Pesanan</label>
                            <select name="status" id="status" class="form-select" required>
                                @foreach($statuses as $key => $label)
                                    <option value="{{ $key }}" {{ old('status', $order->status) === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="payment_status" class="form-label fw-semibold text-dark small">Status Pembayaran</label>
                            <select name="payment_status" id="payment_status" class="form-select" required>
                                @foreach($paymentStatuses as $key => $label)
                                    <option value="{{ $key }}" {{ old('payment_status', $order->payment_status) === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-0">
                            <label for="customer_notes" class="form-label fw-semibold text-dark small">Catatan (Internal/Customer)</label>
                            <textarea name="customer_notes" id="customer_notes" class="form-control" rows="3" placeholder="Tambahkan catatan khusus untuk order ini...">{{ old('customer_notes', $order->customer_notes) }}</textarea>
                            <div class="form-text small text-muted mt-1">Catatan ini bisa dilihat oleh customer.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shipping Information -->
            <div class="col-lg-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom border-light py-3">
                        <h6 class="mb-0 fw-bold text-secondary"><i class="fas fa-truck me-2 text-primary"></i> Detail Pengiriman</h6>
                    </div>
                    <div class="card-body py-4">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="shipping_name" class="form-label fw-semibold text-dark small">Nama Penerima</label>
                                <input type="text" name="shipping_name" id="shipping_name" class="form-control" value="{{ old('shipping_name', $order->shipping_name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="shipping_phone" class="form-label fw-semibold text-dark small">No. Telepon</label>
                                <input type="text" name="shipping_phone" id="shipping_phone" class="form-control" value="{{ old('shipping_phone', $order->shipping_phone) }}" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="shipping_address_line" class="form-label fw-semibold text-dark small">Alamat Lengkap</label>
                            <textarea name="shipping_address_line" id="shipping_address_line" class="form-control" rows="2" required>{{ old('shipping_address_line', $order->shipping_address_line) }}</textarea>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label for="shipping_city" class="form-label fw-semibold text-dark small">Kota/Kab.</label>
                                <input type="text" name="shipping_city" id="shipping_city" class="form-control" value="{{ old('shipping_city', $order->shipping_city) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label for="shipping_province" class="form-label fw-semibold text-dark small">Provinsi</label>
                                <input type="text" name="shipping_province" id="shipping_province" class="form-control" value="{{ old('shipping_province', $order->shipping_province) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label for="shipping_postal_code" class="form-label fw-semibold text-dark small">Kode Pos</label>
                                <input type="text" name="shipping_postal_code" id="shipping_postal_code" class="form-control" value="{{ old('shipping_postal_code', $order->shipping_postal_code) }}" required>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="shipping_courier" class="form-label fw-semibold text-dark small">Kurir</label>
                                <input type="text" name="shipping_courier" id="shipping_courier" class="form-control" value="{{ old('shipping_courier', $order->shipping_courier) }}" placeholder="JNE, J&T, dll">
                            </div>
                            <div class="col-md-6">
                                <label for="shipping_service" class="form-label fw-semibold text-dark small">Layanan Kurir</label>
                                <input type="text" name="shipping_service" id="shipping_service" class="form-control" value="{{ old('shipping_service', $order->shipping_service) }}" placeholder="REG, YES, dll">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 d-flex justify-content-end gap-2">
            <button type="reset" class="btn btn-light border px-4">Reset</button>
            <button type="submit" class="btn btn-primary px-4 shadow-sm">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
