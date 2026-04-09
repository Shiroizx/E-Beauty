@extends('layouts.admin')

@section('title', 'Detail Pesanan — Admin Skinbae.ID')
@section('page_title', 'Detail Pesanan')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">Pesanan #{{ $order->order_number }}</h4>
            <div class="text-muted small">Dibuat pada {{ $order->created_at->format('d M Y, H:i') }} WIB</div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
            <a href="{{ route('admin.orders.edit', $order) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit me-1"></i> Edit Status
            </a>
            <div class="dropdown d-inline-block">
                <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-print me-1"></i> Cetak Struk
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                    <li><a class="dropdown-item" href="{{ route('admin.orders.print', ['order' => $order, 'format' => 'thermal']) }}" target="_blank"><i class="fas fa-receipt text-muted me-2"></i>Format Thermal</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.orders.print', ['order' => $order, 'format' => 'a4']) }}" target="_blank"><i class="fas fa-file-alt text-muted me-2"></i>Format A4</a></li>
                </ul>
            </div>
            <a href="{{ route('admin.orders.invoice', $order) }}" class="btn btn-outline-danger btn-sm" target="_blank">
                <i class="fas fa-file-pdf me-1"></i> Download Invoice
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4 mb-4">
        <!-- Customer Info -->
        <div class="col-lg-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-white border-bottom border-light py-3">
                    <h6 class="mb-0 fw-bold text-secondary"><i class="fas fa-user me-2 text-primary"></i> Customer Info</h6>
                </div>
                <div class="card-body py-4">
                    <div class="mb-3">
                        <label class="small text-muted mb-1 d-block">Nama Lengkap</label>
                        <div class="fw-semibold text-dark">{{ $order->user->name ?? 'Guest' }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="small text-muted mb-1 d-block">Email</label>
                        <div class="text-dark">{{ $order->user->email ?? '-' }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="small text-muted mb-1 d-block">Nomor Telepon</label>
                        <div class="text-dark">{{ $order->shipping_phone }}</div>
                    </div>
                    <div class="mb-0">
                        <label class="small text-muted mb-1 d-block">Catatan Customer</label>
                        <div class="p-2 bg-light rounded text-dark small border border-light">
                            {{ $order->customer_notes ?: 'Tidak ada catatan.' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Shipping Info -->
        <div class="col-lg-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-white border-bottom border-light py-3">
                    <h6 class="mb-0 fw-bold text-secondary"><i class="fas fa-map-marker-alt me-2 text-danger"></i> Info Pengiriman</h6>
                </div>
                <div class="card-body py-4">
                    <div class="mb-3">
                        <label class="small text-muted mb-1 d-block">Nama Penerima</label>
                        <div class="fw-semibold text-dark">{{ $order->shipping_name }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="small text-muted mb-1 d-block">Kurir / Layanan</label>
                        <div class="text-dark text-uppercase fw-bold">{{ $order->shipping_courier ?? 'REG' }} {{ $order->shipping_service ? '- ' . $order->shipping_service : '' }}</div>
                    </div>
                    <div class="mb-0">
                        <label class="small text-muted mb-1 d-block">Alamat Lengkap</label>
                        <div class="text-dark lh-sm">
                            {{ $order->shipping_address_line }}<br>
                            {{ $order->shipping_city }}, {{ $order->shipping_province }}<br>
                            Kode Pos: {{ $order->shipping_postal_code }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment & Status -->
        <div class="col-lg-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-white border-bottom border-light py-3">
                    <h6 class="mb-0 fw-bold text-secondary"><i class="fas fa-wallet me-2 text-success"></i> Pembayaran & Status</h6>
                </div>
                <div class="card-body py-4">
                    <div class="mb-3">
                        <label class="small text-muted mb-1 d-block">Metode Pembayaran</label>
                        <div class="text-dark fw-bold">{{ $order->paymentMethodLabel() }}</div>
                    </div>
                    <div class="mb-3 d-flex gap-3">
                        <div>
                            <label class="small text-muted mb-1 d-block">Status Pesanan</label>
                            @php
                                $badge = match($order->status) {
                                    'pending_payment' => 'bg-warning text-dark',
                                    'processing' => 'bg-info text-dark',
                                    'confirmed' => 'bg-primary',
                                    'shipped' => 'bg-secondary',
                                    'completed' => 'bg-success',
                                    'cancelled' => 'bg-danger',
                                    default => 'bg-light text-dark'
                                };
                            @endphp
                            <span class="badge {{ $badge }} rounded-pill px-3">{{ $order->statusLabel() }}</span>
                        </div>
                        <div>
                            <label class="small text-muted mb-1 d-block">Status Bayar</label>
                            @php
                                $payBadge = match($order->payment_status) {
                                    'paid' => 'text-success border-success',
                                    'pending' => 'text-warning border-warning',
                                    'failed', 'expired' => 'text-danger border-danger',
                                    default => 'text-muted border-secondary'
                                };
                            @endphp
                            <span class="badge bg-white border {{ $payBadge }} rounded-pill px-3">{{ ucfirst($order->payment_status) }}</span>
                        </div>
                    </div>
                    @if($order->isDokuPayment())
                    <div class="mb-0">
                        <label class="small text-muted mb-1 d-block">DOKU Invoice ID</label>
                        <div class="text-dark font-monospace small">{{ $order->doku_invoice_id ?? '-' }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Order Items -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom border-light py-3">
            <h6 class="mb-0 fw-bold text-secondary"><i class="fas fa-shopping-bag me-2 text-primary"></i> Detail Item Pesanan</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4 py-3">Produk</th>
                            <th class="py-3 text-center">Harga Satuan</th>
                            <th class="py-3 text-center">Qty</th>
                            <th class="pe-4 py-3 text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @foreach($order->items as $item)
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center gap-3">
                                    @if($item->product && $item->product->image)
                                        <img src="{{ Storage::url($item->product->image) }}" alt="{{ $item->product_name }}" class="rounded border p-1 bg-white" style="width: 50px; height: 50px; object-fit: contain;">
                                    @else
                                        <div class="rounded border bg-light d-flex align-items-center justify-content-center text-muted" style="width: 50px; height: 50px;">
                                            <i class="fas fa-image"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="fw-semibold text-dark">{{ $item->product_name }}</div>
                                        <div class="text-muted small">SKU: {{ $item->product_sku ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 text-center text-muted">{{ $item->formatted_unit_price }}</td>
                            <td class="py-3 text-center fw-bold">{{ $item->quantity }}</td>
                            <td class="pe-4 py-3 text-end fw-bold text-dark">{{ $item->formatted_line_total }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-light border-top border-light p-4">
            <div class="row justify-content-end">
                <div class="col-md-5 col-lg-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subtotal Produk:</span>
                        <span class="fw-semibold text-dark">{{ $order->formatted_subtotal }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Ongkos Kirim:</span>
                        <span class="fw-semibold text-dark">{{ $order->formatted_shipping }}</span>
                    </div>
                    <hr class="border-secondary my-2">
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <span class="text-dark fw-bold fs-5">Total Akhir:</span>
                        <span class="text-primary fw-bold fs-4">{{ $order->formatted_total }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
