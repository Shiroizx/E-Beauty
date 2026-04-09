@extends('layouts.app')

@section('title', 'Detail Pesanan — Skinbae.ID')

@push('styles')
<style>
    .timeline { position: relative; padding-left: 2rem; }
    .timeline::before { content: ''; position: absolute; left: 0.5rem; top: 0.5rem; bottom: 0; width: 2px; background: #ffe8f2; }
    .timeline-item { position: relative; padding-bottom: 2rem; }
    .timeline-item:last-child { padding-bottom: 0; }
    .timeline-dot { position: absolute; left: -2rem; top: 0.25rem; width: 1rem; height: 1rem; border-radius: 50%; background: #f4518c; border: 3px solid white; box-shadow: 0 0 0 2px #ffe8f2; z-index: 10; }
    .timeline-dot.completed { background: #059669; box-shadow: 0 0 0 2px #d1fae5; }
    .timeline-dot.pending { background: #fff; border: 2px solid #a1a1aa; box-shadow: none; }
</style>
@endpush

@section('content')
<div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="mb-6 overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-lg shadow-brand-200/25">
        <div class="p-6 md:flex md:items-start md:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-brand-500">Nomor pesanan</p>
                <h1 class="mt-1 font-mono text-2xl font-bold text-brand-900">{{ $order->order_number }}</h1>
                <p class="mt-2 text-sm text-neutral-500">{{ $order->created_at->translatedFormat('d F Y, H:i') }}</p>
            </div>
            <div class="mt-4 md:mt-0 md:text-right">
                <span class="inline-block rounded-full border border-brand-200 bg-brand-50 px-4 py-2 text-sm font-semibold text-brand-800">{{ $order->statusLabel() }}</span>
                <p class="mt-2 text-sm text-neutral-600">Pembayaran: {{ $order->payment_status === 'paid' ? 'Lunas' : 'Menunggu' }}</p>
                <div class="mt-3 flex gap-2 md:justify-end">
                    <a href="{{ route('orders.invoice', $order->order_number) }}" target="_blank" class="rounded-xl border border-brand-200 bg-white px-4 py-2 text-sm font-semibold text-brand-700 hover:border-brand-400 hover:bg-brand-50">Unduh Invoice</a>
                    <a href="{{ route('orders.index') }}" class="rounded-xl border border-brand-200 bg-white px-4 py-2 text-sm font-semibold text-brand-700 hover:border-brand-400 hover:bg-brand-50">Kembali</a>
                </div>
            </div>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div class="rounded-2xl border border-brand-100 bg-white p-5 shadow-md">
            <h2 class="mb-3 text-sm font-bold text-brand-900"><i class="fas fa-map-marker-alt me-2 text-brand-400" aria-hidden="true"></i> Pengiriman</h2>
            <p class="font-semibold text-neutral-900">{{ $order->shipping_name }}</p>
            <p class="text-sm text-neutral-500">{{ $order->shipping_phone }}</p>
            <p class="mt-2 text-sm leading-relaxed text-neutral-700">{{ $order->shipping_address_line }}<br>{{ $order->shipping_city }}, {{ $order->shipping_province }} {{ $order->shipping_postal_code }}</p>
            @if($order->customer_notes)
                <p class="mt-3 text-sm"><span class="font-semibold text-brand-800">Catatan:</span> {{ $order->customer_notes }}</p>
            @endif
        </div>
        <div class="rounded-2xl border border-brand-100 bg-white p-5 shadow-md">
            <h2 class="mb-3 text-sm font-bold text-brand-900"><i class="fas fa-wallet me-2 text-brand-400" aria-hidden="true"></i> Pembayaran</h2>
            <p class="text-neutral-800">{{ $order->paymentMethodLabel() }}</p>
            @if($order->payment_status === 'paid')
                <div class="mt-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                    <i class="fas fa-check-circle me-1 text-emerald-500" aria-hidden="true"></i>
                    Pembayaran berhasil.
                </div>
            @elseif($order->payment_status === 'pending')
                <div class="mt-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                    <i class="fas fa-clock me-1 text-amber-500" aria-hidden="true"></i>
                    Menunggu pembayaran.
                </div>
            @else
                <div class="mt-3 rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm text-neutral-700">
                    Status pembayaran: {{ ucfirst($order->payment_status) }}
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
                                <div class="text-xs text-neutral-500">{{ $item->formatted_unit_price }}</div>
                            </td>
                            <td class="px-5 py-4 text-center">{{ $item->quantity }}</td>
                            <td class="px-5 py-4 text-end">{{ $item->formatted_line_total }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="md:hidden divide-y divide-brand-100">
            @foreach($order->items as $item)
                <div class="flex items-center justify-between p-4">
                    <div>
                        <div class="font-semibold text-neutral-900">{{ $item->product_name }}</div>
                        <div class="text-xs text-neutral-500">{{ $item->formatted_unit_price }} × {{ $item->quantity }}</div>
                    </div>
                    <div class="text-right font-semibold">{{ $item->formatted_line_total }}</div>
                </div>
            @endforeach
        </div>
        <div class="border-t border-brand-100 bg-brand-50/60 px-5 py-4">
            <div class="ml-auto max-w-sm space-y-1 text-sm">
                <div class="flex justify-between"><span class="text-neutral-600">Subtotal</span><span class="font-semibold text-neutral-900">{{ $order->formatted_subtotal }}</span></div>
                <div class="flex justify-between"><span class="text-neutral-600">Ongkir</span><span class="font-semibold text-neutral-900">{{ $order->formatted_shipping }}</span></div>
                <div class="flex justify-between text-base font-bold"><span class="text-brand-900">Total</span><span class="text-brand-900">{{ $order->formatted_total }}</span></div>
            </div>
        </div>
    </div>

    <div id="tracking" class="mt-6 rounded-2xl border border-brand-100 bg-white p-5 shadow-md">
        <div class="mb-5 flex items-center justify-between">
            <h2 class="text-sm font-bold text-brand-900"><i class="fas fa-truck-fast me-2 text-brand-400" aria-hidden="true"></i> Status Pengiriman</h2>
            <a href="{{ route('track.show', $order->order_number) }}" class="btn-brand-outline px-4 py-2 text-xs">
                Lacak di Peta <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>
        
        <div class="timeline mt-2">
            @foreach($trackingData as $event)
                <div class="timeline-item">
                    <div class="timeline-dot {{ $event['completed'] ? 'completed' : 'pending' }}"></div>
                    <div class="flex flex-col gap-1">
                        <h3 class="text-sm font-bold {{ $event['completed'] ? 'text-neutral-900' : 'text-neutral-500' }}">{{ $event['status'] }}</h3>
                        <p class="text-xs {{ $event['completed'] ? 'text-neutral-600' : 'text-neutral-400' }}">{{ $event['description'] }}</p>
                        <div class="mt-1 flex flex-wrap items-center justify-between gap-2 text-[11px] font-medium text-brand-600">
                            <span><i class="fas fa-map-marker-alt"></i> {{ $event['location'] }}</span>
                            @if($event['time'])
                                <span>{{ \Carbon\Carbon::parse($event['time'])->format('d M Y H:i') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
