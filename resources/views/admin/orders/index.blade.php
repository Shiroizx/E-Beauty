@extends('layouts.admin')

@section('title', 'Kelola Pesanan — Admin Skinbae.ID')
@section('page_title', 'Kelola Pesanan')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-dark mb-0">Daftar Pesanan</h4>
        <div class="d-flex gap-2">
            <div class="dropdown">
                <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" id="bulkPrintBtn" disabled>
                    <i class="fas fa-print me-1"></i> Cetak Massal
                </button>
                <ul class="dropdown-menu shadow-sm border-0">
                    <li><a class="dropdown-item" href="#" onclick="submitBulkPrint('thermal')"><i class="fas fa-receipt text-muted me-2"></i>Format Thermal</a></li>
                    <li><a class="dropdown-item" href="#" onclick="submitBulkPrint('a4')"><i class="fas fa-file-alt text-muted me-2"></i>Format A4</a></li>
                </ul>
            </div>
            <a href="{{ route('admin.orders.export') }}" class="btn btn-outline-secondary btn-sm" target="_blank">
                <i class="fas fa-file-csv me-1"></i> Export CSV
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('admin.orders.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label text-muted small fw-bold">Pencarian</label>
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Nomor Pesanan / Nama Customer" value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label text-muted small fw-bold">Status Pesanan</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Semua Status</option>
                        @foreach($statuses as $key => $label)
                            <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-muted small fw-bold">Pembayaran</label>
                    <select name="payment_status" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        @foreach($paymentStatuses as $key => $label)
                            <option value="{{ $key }}" {{ request('payment_status') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-muted small fw-bold">Dari Tanggal</label>
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label text-muted small fw-bold">Sampai Tanggal</label>
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <form id="bulkPrintForm" action="{{ route('admin.orders.print_bulk') }}" method="POST" target="_blank">
                @csrf
                <input type="hidden" name="format" id="printFormat" value="thermal">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4 py-3" style="width: 40px;">
                                    <div class="form-check">
                                        <input class="form-check-input select-all-orders" type="checkbox" id="selectAll">
                                    </div>
                                </th>
                                <th class="py-3">No. Pesanan</th>
                                <th class="py-3">Customer</th>
                                <th class="py-3">Tanggal</th>
                                <th class="py-3">Status</th>
                                <th class="py-3">Pembayaran</th>
                                <th class="py-3 text-end">Total</th>
                                <th class="pe-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            @forelse($orders as $order)
                                <tr>
                                    <td class="ps-4 py-3">
                                        <div class="form-check">
                                            <input class="form-check-input order-checkbox" type="checkbox" name="order_ids[]" value="{{ $order->id }}">
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <div class="fw-bold text-dark">{{ $order->order_number }}</div>
                                        <div class="text-muted small">{{ $order->items_count }} item(s)</div>
                                    </td>
                                <td class="py-3">
                                    <div class="fw-semibold">{{ $order->user->name ?? 'Guest' }}</div>
                                    <div class="text-muted small">{{ $order->user->email ?? '' }}</div>
                                </td>
                                <td class="py-3 text-muted small">
                                    {{ $order->created_at->format('d M Y') }}<br>
                                    {{ $order->created_at->format('H:i') }}
                                </td>
                                <td class="py-3">
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
                                </td>
                                <td class="py-3">
                                    @php
                                        $payBadge = match($order->payment_status) {
                                            'paid' => 'text-success',
                                            'pending' => 'text-warning',
                                            'failed', 'expired' => 'text-danger',
                                            default => 'text-muted'
                                        };
                                    @endphp
                                    <span class="fw-bold small {{ $payBadge }}">
                                        <i class="fas fa-circle me-1" style="font-size: 0.5rem; vertical-align: middle;"></i>
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                </td>
                                <td class="py-3 text-end fw-bold text-dark">
                                    {{ $order->formatted_total }}
                                </td>
                                <td class="pe-4 py-3 text-center">
                                    <div class="btn-group">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-light border" title="Lihat Detail">
                                            <i class="fas fa-eye text-primary"></i>
                                        </a>
                                        <a href="{{ route('admin.orders.edit', $order) }}" class="btn btn-sm btn-light border" title="Edit Pesanan">
                                            <i class="fas fa-edit text-warning"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3 text-light"></i>
                                    <p class="mb-0">Belum ada data pesanan yang sesuai filter.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            </form>
            
            @if($orders->hasPages())
                <div class="px-4 py-3 border-top">
                    {{ $orders->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.order-checkbox');
        const bulkPrintBtn = document.getElementById('bulkPrintBtn');
        const form = document.getElementById('bulkPrintForm');
        const formatInput = document.getElementById('printFormat');

        function updateButtonState() {
            const checkedCount = document.querySelectorAll('.order-checkbox:checked').length;
            if (checkedCount > 0) {
                bulkPrintBtn.removeAttribute('disabled');
                bulkPrintBtn.innerHTML = `<i class="fas fa-print me-1"></i> Cetak Massal (${checkedCount})`;
            } else {
                bulkPrintBtn.setAttribute('disabled', 'disabled');
                bulkPrintBtn.innerHTML = `<i class="fas fa-print me-1"></i> Cetak Massal`;
            }
        }

        if (selectAll) {
            selectAll.addEventListener('change', function() {
                checkboxes.forEach(cb => cb.checked = this.checked);
                updateButtonState();
            });
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                const allChecked = document.querySelectorAll('.order-checkbox:checked').length === checkboxes.length;
                if (selectAll) selectAll.checked = allChecked;
                updateButtonState();
            });
        });

        window.submitBulkPrint = function(format) {
            formatInput.value = format;
            form.submit();
        };
    });
</script>
@endpush
