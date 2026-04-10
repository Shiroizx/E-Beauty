@extends('layouts.app')

@section('title', 'Pesanan Dikonfirmasi — Skinbae.ID')

@section('content')
<div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="mb-6 overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-lg shadow-brand-200/25">
        <div class="p-6 text-center md:flex md:items-start md:justify-between md:text-left">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-brand-500">Nomor pesanan</p>
                <h1 class="mt-1 font-mono text-2xl font-bold text-brand-900">{{ $order->order_number }}</h1>
                <p class="mt-2 text-sm text-neutral-500">{{ $order->created_at->translatedFormat('d F Y, H:i') }}</p>
            </div>
            <div class="mt-4 md:mt-0 md:text-right">
                <span class="inline-block rounded-full border border-brand-200 bg-brand-50 px-4 py-2 text-sm font-semibold text-brand-800">{{ $order->statusLabel() }}</span>
                <p class="mt-2 text-sm text-neutral-600">Pembayaran: {{ $order->payment_status === 'paid' ? 'Lunas' : 'Menunggu' }}</p>
            </div>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div class="rounded-2xl border border-brand-100 bg-white p-5 shadow-md">
            <h2 class="mb-3 text-sm font-bold text-brand-900"><i class="fas fa-map-marker-alt me-2 text-brand-400" aria-hidden="true"></i> Pengiriman</h2>
            <p class="font-semibold text-neutral-900">{{ $order->shipping_name }}</p>
            <p class="text-sm text-neutral-500">{{ $order->shipping_phone }}</p>
            <p class="mt-2 text-sm leading-relaxed text-neutral-700">{{ $order->shipping_address_line }}<br>
                {{ $order->shipping_city }}, {{ $order->shipping_province }} {{ $order->shipping_postal_code }}</p>
            @if($order->customer_notes)
                <p class="mt-3 text-sm"><span class="font-semibold text-brand-800">Catatan:</span> {{ $order->customer_notes }}</p>
            @endif
        </div>
        <div class="rounded-2xl border border-brand-100 bg-white p-5 shadow-md">
            <h2 class="mb-3 text-sm font-bold text-brand-900"><i class="fas fa-wallet me-2 text-brand-400" aria-hidden="true"></i> Pembayaran</h2>
            <p class="text-neutral-800">{{ $order->paymentMethodLabel() }}</p>

            @if($order->isDokuPayment())
                @if($order->payment_status === 'paid')
                    <div class="mt-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                        <i class="fas fa-check-circle me-1 text-emerald-500" aria-hidden="true"></i>
                        <strong>Pembayaran berhasil!</strong> Pesanan Anda sedang diproses.
                    </div>
                @elseif($order->payment_status === 'failed' || $order->payment_status === 'expired' || $order->isDokuExpired())
                    <div class="mt-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900">
                        <i class="fas fa-times-circle me-1 text-red-500" aria-hidden="true"></i>
                        <strong>Pembayaran gagal / kedaluwarsa.</strong> Silakan buat pesanan baru.
                    </div>
                @elseif($order->payment_status === 'pending' && $order->doku_payment_url)
                    <div class="mt-3 space-y-3">
                        <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                            <i class="fas fa-clock me-1 text-amber-500" aria-hidden="true"></i>
                            <strong>Menunggu pembayaran.</strong>
                            @if($order->payment_expired_at)
                                Batas waktu: <strong>{{ $order->payment_expired_at->format('d M Y, H:i') }} WIB</strong>
                            @endif
                        </div>
                        <a href="{{ $order->doku_payment_url }}" target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-brand-400 to-brand-600 px-6 py-3 text-sm font-bold text-white shadow-md shadow-brand-400/30 transition hover:from-brand-500 hover:to-brand-700 hover:shadow-lg">
                            <i class="fas fa-external-link-alt" aria-hidden="true"></i>
                            Bayar Sekarang via DOKU
                        </a>
                        <p class="text-xs text-neutral-500">Anda akan diarahkan ke halaman pembayaran DOKU.</p>
                    </div>
                @else
                    <p class="mt-2 text-sm text-neutral-600">Status: {{ $order->payment_status }}</p>
                @endif
            @elseif($order->payment_method === 'bank_transfer' && $order->payment_status === 'pending')
                <div class="mt-3 rounded-xl border border-brand-100 bg-brand-50/80 p-3 text-sm text-neutral-700">
                    <strong class="text-brand-900">Transfer ke:</strong><br>
                    {{ $bank['name'] ?? 'Bank' }} — {{ $bank['account_number'] ?? '-' }}<br>
                    a.n. {{ $bank['account_holder'] ?? '-' }}<br>
                    <span class="text-neutral-500">Cantumkan nomor pesanan di berita transfer.</span>
                </div>
            @elseif($order->payment_method === 'cod')
                <p class="mt-2 text-sm text-neutral-600">Siapkan pembayaran tunai sesuai total saat kurir tiba.</p>
            @elseif($order->payment_method === 'simulated_card')
                <div class="mt-3 rounded-xl border border-sky-200 bg-sky-50 px-3 py-2 text-sm text-sky-900">
                    Pembayaran simulasi berhasil (mode demo). Tidak ada kartu yang disimpan.
                </div>
            @endif
        </div>
    </div>

    <div class="mt-6 overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-md">
        <div class="border-b border-brand-50 px-5 py-4">
            <h2 class="font-bold text-brand-900">Item pesanan</h2>
        </div>
        <div class="hidden md:block">
            <table class="w-full text-sm">
                <thead class="bg-brand-50/80 text-left text-xs font-semibold uppercase tracking-wide text-brand-600">
                    <tr>
                        <th class="px-5 py-3">Produk</th>
                        <th class="px-5 py-3 text-center">Qty</th>
                        <th class="px-5 py-3 text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-100">
                    @foreach($order->items as $item)
                        <tr>
                            <td class="px-5 py-4">
                                <div class="font-semibold text-neutral-900">{{ $item->product_name }}</div>
                                @if($item->product_sku)<span class="text-xs text-neutral-500">SKU: {{ $item->product_sku }}</span>@endif
                            </td>
                            <td class="px-5 py-4 text-center">{{ $item->quantity }}</td>
                            <td class="px-5 py-4 text-end font-semibold text-brand-900">{{ $item->formatted_line_total }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <ul class="divide-y divide-brand-100 md:hidden">
            @foreach($order->items as $item)
                <li class="flex justify-between gap-3 px-5 py-4">
                    <div>
                        <p class="text-sm font-semibold text-neutral-900">{{ $item->product_name }}</p>
                        <p class="text-xs text-neutral-500">{{ $item->quantity }} × {{ $item->formatted_unit_price }}</p>
                    </div>
                    <p class="shrink-0 text-sm font-bold text-brand-800">{{ $item->formatted_line_total }}</p>
                </li>
            @endforeach
        </ul>
        <div class="border-t border-brand-100 px-5 py-5">
            <div class="flex justify-between text-sm">
                <span class="text-neutral-600">Subtotal</span>
                <span>{{ $order->formatted_subtotal }}</span>
            </div>
            @if((float) $order->discount_amount > 0)
                <div class="mt-2 flex justify-between text-sm text-emerald-700">
                    <span>Diskon promo @if($order->promo_code)<span class="font-mono text-xs">({{ $order->promo_code }})</span>@endif</span>
                    <span>− {{ $order->formatted_discount_amount }}</span>
                </div>
            @endif
            <div class="mt-2 flex justify-between text-sm">
                <span class="text-neutral-600">Ongkir</span>
                <span>{{ $order->formatted_shipping }}</span>
            </div>
            <div class="mt-4 flex items-center justify-between border-t border-brand-100 pt-4">
                <span class="font-bold text-brand-900">Total</span>
                <span class="text-xl font-bold text-gradient-brand">{{ $order->formatted_total }}</span>
            </div>
        </div>
    </div>

    <div class="mt-10 flex flex-col gap-3 sm:flex-row sm:justify-center">
        <a href="{{ route('catalog') }}" class="btn-brand-outline px-8 py-3 text-center">Lanjut belanja</a>
        <a href="{{ route('home') }}" class="btn-brand px-8 py-3 text-center">Ke beranda</a>
    </div>
</div>
@endsection

@if($order->isDokuPayment() && $order->payment_status === 'pending' && $order->doku_payment_url)
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                let checkInterval = setInterval(function () {
                    fetch('{{ route('orders.status', $order->order_number) }}', {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.payment_status === 'paid' || data.payment_status === 'failed') {
                            clearInterval(checkInterval);
                            window.location.reload();
                        }
                    })
                    .catch(error => console.error('Error checking status:', error));
                }, 5000); // Cek setiap 5 detik
            });
        </script>
    @endpush
@endif
