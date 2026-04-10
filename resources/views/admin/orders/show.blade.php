@extends('layouts.admin')

@section('title', 'Detail Pesanan — Admin Skinbae.ID')
@section('page_title', 'Detail Pesanan')

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h2 class="text-xl font-bold tracking-tight text-gray-900">Pesanan #{{ $order->order_number }}</h2>
            <p class="mt-0.5 text-sm text-gray-400">Dibuat pada {{ $order->created_at->format('d M Y, H:i') }} WIB</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-stone-200 bg-white px-3.5 py-2 text-xs font-semibold text-gray-600 shadow-sm transition hover:bg-stone-50">
                <i class="fas fa-arrow-left text-[10px]"></i> Kembali
            </a>
            <a href="{{ route('admin.orders.edit', $order) }}" class="inline-flex items-center gap-1.5 rounded-xl border border-amber-200 bg-amber-50 px-3.5 py-2 text-xs font-semibold text-amber-700 transition hover:bg-amber-100">
                <i class="fas fa-pen text-[10px]"></i> Edit Status
            </a>
            {{-- Print Dropdown --}}
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="inline-flex items-center gap-1.5 rounded-xl bg-gradient-to-r from-brand-500 to-brand-600 px-3.5 py-2 text-xs font-semibold text-white shadow-sm transition hover:shadow-md">
                    <i class="fas fa-print text-[10px]"></i> Cetak Struk <i class="fas fa-chevron-down text-[8px] ml-1"></i>
                </button>
                <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 z-10 mt-1 w-44 rounded-xl bg-white py-1 shadow-lg ring-1 ring-stone-100" x-cloak>
                    <a href="{{ route('admin.orders.print', ['order' => $order, 'format' => 'thermal']) }}" target="_blank" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 transition hover:bg-stone-50">
                        <i class="fas fa-receipt text-gray-400 text-xs w-4"></i> Format Thermal
                    </a>
                    <a href="{{ route('admin.orders.print', ['order' => $order, 'format' => 'a4']) }}" target="_blank" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 transition hover:bg-stone-50">
                        <i class="fas fa-file-alt text-gray-400 text-xs w-4"></i> Format A4
                    </a>
                </div>
            </div>
            <a href="{{ route('admin.orders.invoice', $order) }}" class="inline-flex items-center gap-1.5 rounded-xl border border-red-200 bg-red-50 px-3.5 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-100" target="_blank">
                <i class="fas fa-file-pdf text-[10px]"></i> Invoice
            </a>
        </div>
    </div>

    {{-- Info Cards --}}
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">

        {{-- Customer Info --}}
        <div class="rounded-2xl bg-white p-5 shadow-card">
            <div class="mb-4 flex items-center gap-2.5">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 text-blue-500">
                    <i class="fas fa-user text-sm"></i>
                </span>
                <h3 class="text-sm font-bold text-gray-800">Customer Info</h3>
            </div>
            <div class="space-y-3">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Nama Lengkap</p>
                    <p class="mt-0.5 text-sm font-medium text-gray-800">{{ $order->user->name ?? 'Guest' }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Email</p>
                    <p class="mt-0.5 text-sm text-gray-700">{{ $order->user->email ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Telepon</p>
                    <p class="mt-0.5 text-sm text-gray-700">{{ $order->shipping_phone }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Catatan</p>
                    <p class="mt-0.5 rounded-lg bg-stone-50 p-2.5 text-xs text-gray-600 ring-1 ring-stone-100">{{ $order->customer_notes ?: 'Tidak ada catatan.' }}</p>
                </div>
            </div>
        </div>

        {{-- Shipping Info --}}
        <div class="rounded-2xl bg-white p-5 shadow-card">
            <div class="mb-4 flex items-center gap-2.5">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-violet-50 text-violet-500">
                    <i class="fas fa-map-marker-alt text-sm"></i>
                </span>
                <h3 class="text-sm font-bold text-gray-800">Info Pengiriman</h3>
            </div>
            <div class="space-y-3">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Penerima</p>
                    <p class="mt-0.5 text-sm font-medium text-gray-800">{{ $order->shipping_name }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Kurir / Layanan</p>
                    <p class="mt-0.5 text-sm font-bold uppercase text-gray-800">{{ $order->shipping_courier ?? 'REG' }} {{ $order->shipping_service ? '— ' . $order->shipping_service : '' }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Alamat Lengkap</p>
                    <p class="mt-0.5 text-sm leading-relaxed text-gray-700">
                        {{ $order->shipping_address_line }}<br>
                        {{ $order->shipping_city }}, {{ $order->shipping_province }}<br>
                        Kode Pos: {{ $order->shipping_postal_code }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Payment & Status --}}
        <div class="rounded-2xl bg-white p-5 shadow-card">
            <div class="mb-4 flex items-center gap-2.5">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-emerald-500">
                    <i class="fas fa-wallet text-sm"></i>
                </span>
                <h3 class="text-sm font-bold text-gray-800">Pembayaran & Status</h3>
            </div>
            <div class="space-y-3">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Metode</p>
                    <p class="mt-0.5 text-sm font-bold text-gray-800">{{ $order->paymentMethodLabel() }}</p>
                </div>
                <div class="flex gap-4">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Status Pesanan</p>
                        @php
                            $statusStyle = match($order->status) {
                                'pending_payment' => 'bg-amber-50 text-amber-700 ring-amber-200/60',
                                'processing' => 'bg-sky-50 text-sky-700 ring-sky-200/60',
                                'confirmed' => 'bg-blue-50 text-blue-700 ring-blue-200/60',
                                'shipped' => 'bg-violet-50 text-violet-700 ring-violet-200/60',
                                'completed' => 'bg-emerald-50 text-emerald-700 ring-emerald-200/60',
                                'cancelled' => 'bg-red-50 text-red-600 ring-red-200/60',
                                default => 'bg-gray-50 text-gray-600 ring-gray-200/60'
                            };
                        @endphp
                        <span class="mt-1 inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-semibold ring-1 ring-inset {{ $statusStyle }}">{{ $order->statusLabel() }}</span>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Status Bayar</p>
                        @php
                            $payStyle = match($order->payment_status) {
                                'paid' => 'bg-emerald-50 text-emerald-700 ring-emerald-200/60',
                                'pending' => 'bg-amber-50 text-amber-700 ring-amber-200/60',
                                'failed', 'expired' => 'bg-red-50 text-red-600 ring-red-200/60',
                                default => 'bg-gray-50 text-gray-600 ring-gray-200/60'
                            };
                        @endphp
                        <span class="mt-1 inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-semibold ring-1 ring-inset {{ $payStyle }}">{{ ucfirst($order->payment_status) }}</span>
                    </div>
                </div>
                @if($order->isDokuPayment())
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">DOKU Invoice ID</p>
                    <p class="mt-0.5 font-mono text-xs text-gray-600">{{ $order->doku_invoice_id ?? '-' }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Order Items --}}
    <div class="overflow-hidden rounded-2xl bg-white shadow-card">
        <div class="flex items-center gap-2.5 border-b border-stone-100 px-5 py-4">
            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-50 text-brand-500">
                <i class="fas fa-shopping-bag text-sm"></i>
            </span>
            <h3 class="text-sm font-bold text-gray-800">Detail Item Pesanan</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-stone-50 text-left">
                        <th class="py-3 pl-5 pr-3 text-[11px] font-semibold uppercase tracking-wider text-gray-400">Produk</th>
                        <th class="px-3 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-gray-400">Harga Satuan</th>
                        <th class="px-3 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-gray-400">Qty</th>
                        <th class="py-3 pl-3 pr-5 text-right text-[11px] font-semibold uppercase tracking-wider text-gray-400">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-50">
                    @foreach($order->items as $item)
                    <tr class="transition hover:bg-stone-50/60">
                        <td class="py-3 pl-5 pr-3">
                            <div class="flex items-center gap-3">
                                @if($item->product && $item->product->image)
                                    <img src="{{ Storage::url($item->product->image) }}" alt="" class="h-11 w-11 rounded-lg border border-stone-100 bg-white object-contain p-0.5">
                                @else
                                    <div class="flex h-11 w-11 items-center justify-center rounded-lg border border-stone-100 bg-stone-50 text-stone-300">
                                        <i class="fas fa-image text-sm"></i>
                                    </div>
                                @endif
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $item->product_name }}</p>
                                    <p class="text-[11px] text-gray-400">SKU: {{ $item->product_sku ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-3 py-3 text-center text-gray-500">{{ $item->formatted_unit_price }}</td>
                        <td class="px-3 py-3 text-center font-bold text-gray-800">{{ $item->quantity }}</td>
                        <td class="py-3 pl-3 pr-5 text-right font-bold text-gray-800">{{ $item->formatted_line_total }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="border-t border-stone-100 bg-stone-50/50 px-5 py-4">
            <div class="ml-auto max-w-xs space-y-1.5">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Subtotal Produk</span>
                    <span class="font-semibold text-gray-700">{{ $order->formatted_subtotal }}</span>
                </div>
                @if((float) $order->discount_amount > 0)
                    <div class="flex items-center justify-between text-sm text-emerald-700">
                        <span class="text-gray-500">Diskon promo @if($order->promo_code)<span class="font-mono text-xs">({{ $order->promo_code }})</span>@endif</span>
                        <span class="font-semibold">− {{ $order->formatted_discount_amount }}</span>
                    </div>
                @endif
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Ongkos Kirim</span>
                    <span class="font-semibold text-gray-700">{{ $order->formatted_shipping }}</span>
                </div>
                <div class="my-2 border-t border-stone-200"></div>
                <div class="flex items-center justify-between">
                    <span class="text-sm font-bold text-gray-800">Total Akhir</span>
                    <span class="text-lg font-extrabold tracking-tight text-brand-600">{{ $order->formatted_total }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
