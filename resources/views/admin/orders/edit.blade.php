@extends('layouts.admin')

@section('title', 'Edit Pesanan — Admin Skinbae.ID')
@section('page_title', 'Edit Pesanan')

@section('content')
<div class="mx-auto max-w-5xl space-y-5">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h2 class="text-xl font-bold tracking-tight text-gray-900">Edit Pesanan #{{ $order->order_number }}</h2>
            <p class="mt-0.5 text-sm text-gray-400">Dibuat pada {{ $order->created_at->format('d M Y, H:i') }} WIB</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-stone-200 bg-white px-3.5 py-2 text-xs font-semibold text-gray-600 shadow-sm transition hover:bg-stone-50">
                <i class="fas fa-arrow-left text-[10px]"></i> Kembali
            </a>
            <a href="{{ route('admin.orders.show', $order) }}" class="inline-flex items-center gap-1.5 rounded-xl border border-blue-200 bg-blue-50 px-3.5 py-2 text-xs font-semibold text-blue-600 transition hover:bg-blue-100">
                <i class="fas fa-eye text-[10px]"></i> Lihat Detail
            </a>
        </div>
    </div>

    {{-- Form --}}
    <form action="{{ route('admin.orders.update', $order) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">

            {{-- Status & Payment --}}
            <div class="rounded-2xl bg-white p-5 shadow-card">
                <div class="mb-4 flex items-center gap-2.5">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-50 text-amber-500">
                        <i class="fas fa-sync-alt text-sm"></i>
                    </span>
                    <h3 class="text-sm font-bold text-gray-800">Status Pesanan</h3>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-gray-600">Status Pesanan</label>
                        <select name="status" required class="w-full rounded-xl border border-stone-200 px-3.5 py-2.5 text-sm text-gray-700 transition focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-100">
                            @foreach($statuses as $key => $label)
                                <option value="{{ $key }}" {{ old('status', $order->status) === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-gray-600">Status Pembayaran</label>
                        <select name="payment_status" required class="w-full rounded-xl border border-stone-200 px-3.5 py-2.5 text-sm text-gray-700 transition focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-100">
                            @foreach($paymentStatuses as $key => $label)
                                <option value="{{ $key }}" {{ old('payment_status', $order->payment_status) === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-gray-600">Catatan</label>
                        <textarea name="customer_notes" rows="3" placeholder="Tambahkan catatan khusus..."
                                  class="w-full rounded-xl border border-stone-200 px-3.5 py-2.5 text-sm text-gray-700 placeholder-gray-300 transition focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-100">{{ old('customer_notes', $order->customer_notes) }}</textarea>
                        <p class="mt-1 text-[11px] text-gray-400">Catatan ini bisa dilihat oleh customer</p>
                    </div>
                </div>
            </div>

            {{-- Shipping --}}
            <div class="rounded-2xl bg-white p-5 shadow-card">
                <div class="mb-4 flex items-center gap-2.5">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 text-blue-500">
                        <i class="fas fa-truck text-sm"></i>
                    </span>
                    <h3 class="text-sm font-bold text-gray-800">Detail Pengiriman</h3>
                </div>

                <div class="space-y-4">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-gray-600">Nama Penerima</label>
                            <input type="text" name="shipping_name" value="{{ old('shipping_name', $order->shipping_name) }}" required
                                   class="w-full rounded-xl border border-stone-200 px-3.5 py-2.5 text-sm text-gray-700 transition focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-100">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-gray-600">No. Telepon</label>
                            <input type="text" name="shipping_phone" value="{{ old('shipping_phone', $order->shipping_phone) }}" required
                                   class="w-full rounded-xl border border-stone-200 px-3.5 py-2.5 text-sm text-gray-700 transition focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-100">
                        </div>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-gray-600">Alamat Lengkap</label>
                        <textarea name="shipping_address_line" rows="2" required
                                  class="w-full rounded-xl border border-stone-200 px-3.5 py-2.5 text-sm text-gray-700 transition focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-100">{{ old('shipping_address_line', $order->shipping_address_line) }}</textarea>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-gray-600">Kota/Kab.</label>
                            <input type="text" name="shipping_city" value="{{ old('shipping_city', $order->shipping_city) }}" required
                                   class="w-full rounded-xl border border-stone-200 px-3.5 py-2.5 text-sm text-gray-700 transition focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-100">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-gray-600">Provinsi</label>
                            <input type="text" name="shipping_province" value="{{ old('shipping_province', $order->shipping_province) }}" required
                                   class="w-full rounded-xl border border-stone-200 px-3.5 py-2.5 text-sm text-gray-700 transition focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-100">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-gray-600">Kode Pos</label>
                            <input type="text" name="shipping_postal_code" value="{{ old('shipping_postal_code', $order->shipping_postal_code) }}" required
                                   class="w-full rounded-xl border border-stone-200 px-3.5 py-2.5 text-sm text-gray-700 transition focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-100">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-gray-600">Kurir</label>
                            <input type="text" name="shipping_courier" value="{{ old('shipping_courier', $order->shipping_courier) }}" placeholder="JNE, J&T, dll"
                                   class="w-full rounded-xl border border-stone-200 px-3.5 py-2.5 text-sm text-gray-700 placeholder-gray-300 transition focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-100">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-gray-600">Layanan</label>
                            <input type="text" name="shipping_service" value="{{ old('shipping_service', $order->shipping_service) }}" placeholder="REG, YES, dll"
                                   class="w-full rounded-xl border border-stone-200 px-3.5 py-2.5 text-sm text-gray-700 placeholder-gray-300 transition focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-100">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-5 flex items-center justify-end gap-2">
            <button type="reset" class="rounded-xl border border-stone-200 bg-white px-5 py-2.5 text-sm font-semibold text-gray-600 transition hover:bg-stone-50">
                Reset
            </button>
            <button type="submit" class="rounded-xl bg-gradient-to-r from-brand-500 to-brand-600 px-6 py-2.5 text-sm font-bold text-white shadow-md shadow-brand-500/20 transition hover:shadow-lg hover:shadow-brand-500/25">
                <i class="fas fa-save mr-2 text-xs"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
