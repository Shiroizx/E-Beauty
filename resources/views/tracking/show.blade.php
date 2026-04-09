@extends('layouts.app')

@section('title', 'Detail Pelacakan — Skinbae.ID')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    #map { height: 400px; width: 100%; border-radius: 1rem; z-index: 10; }
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
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-brand-900 sm:text-3xl">Status Pengiriman</h1>
            <p class="mt-1 text-sm text-neutral-500">Resi/Pesanan: <span class="font-bold text-brand-700">{{ $order->order_number }}</span></p>
        </div>
        <div class="flex gap-2">
            <button onclick="navigator.clipboard.writeText(window.location.href); alert('Tautan berhasil disalin!')" class="btn-brand-outline inline-flex items-center gap-2 text-sm">
                <i class="fas fa-share-alt"></i> Bagikan
            </button>
            <a href="{{ route('track.index') }}" class="btn-brand inline-flex items-center gap-2 text-sm">
                <i class="fas fa-search"></i> Lacak Lainnya
            </a>
        </div>
    </div>

    <div class="grid gap-8 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            <!-- Peta Interaktif -->
            <div class="rounded-2xl border border-brand-100 bg-white p-4 shadow-md shadow-brand-200/15">
                <h2 class="mb-4 text-lg font-bold text-brand-900"><i class="fas fa-map-marked-alt text-brand-400 me-2"></i> Peta Pengiriman</h2>
                <div id="map" class="border border-brand-50"></div>
                <div class="mt-3 flex justify-between text-xs text-neutral-500">
                    <span><i class="fas fa-circle text-emerald-600"></i> Tujuan</span>
                    <span><i class="fas fa-circle text-brand-500"></i> Posisi Terkini</span>
                </div>
            </div>
            
            <!-- Detail Informasi -->
            <div class="rounded-2xl border border-brand-100 bg-white p-5 shadow-md shadow-brand-200/15">
                <h2 class="mb-4 text-lg font-bold text-brand-900"><i class="fas fa-info-circle text-brand-400 me-2"></i> Informasi Pengiriman</h2>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <div class="text-neutral-500">Kurir</div>
                        <div class="font-bold text-neutral-900">{{ strtoupper($order->shipping_courier ?? 'REG') }} - {{ strtoupper($order->shipping_service ?? 'Standard') }}</div>
                    </div>
                    <div>
                        <div class="text-neutral-500">Penerima</div>
                        <div class="font-bold text-neutral-900">{{ $order->shipping_name }}</div>
                    </div>
                    <div class="col-span-2">
                        <div class="text-neutral-500">Alamat Tujuan</div>
                        <div class="text-neutral-900">{{ $order->shipping_address_line }}, {{ $order->shipping_city }}, {{ $order->shipping_province }} {{ $order->shipping_postal_code }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-1">
            <!-- Timeline Status -->
            <div class="rounded-2xl border border-brand-100 bg-white p-6 shadow-md shadow-brand-200/15">
                <h2 class="mb-6 text-lg font-bold text-brand-900">Riwayat Perjalanan</h2>
                
                <div class="timeline">
                    @foreach($trackingData as $event)
                        <div class="timeline-item">
                            <div class="timeline-dot {{ $event['completed'] ? 'completed' : 'pending' }}"></div>
                            <div class="flex flex-col gap-1">
                                <h3 class="text-sm font-bold {{ $event['completed'] ? 'text-neutral-900' : 'text-neutral-500' }}">{{ $event['status'] }}</h3>
                                <p class="text-xs {{ $event['completed'] ? 'text-neutral-600' : 'text-neutral-400' }}">{{ $event['description'] }}</p>
                                <div class="mt-1 flex items-center justify-between text-[11px] font-medium text-brand-600">
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
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const trackingData = @json($trackingData);
        
        // Temukan lokasi valid terkini
        const locations = trackingData.filter(d => d.lat !== null && d.lng !== null);
        
        if (locations.length === 0) {
            document.getElementById('map').innerHTML = '<div class="flex h-full items-center justify-center text-neutral-400">Peta belum tersedia untuk pesanan ini.</div>';
            return;
        }

        const currentPos = locations[0]; // Data pertama adalah yang terbaru karena desc
        const map = L.map('map').setView([currentPos.lat, currentPos.lng], 10);

        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; OpenStreetMap & CARTO',
            maxZoom: 18
        }).addTo(map);

        // Marker style
        const iconCurrent = L.divIcon({
            className: 'custom-div-icon',
            html: "<div style='background-color:#f4518c;width:15px;height:15px;border-radius:50%;border:3px solid white;box-shadow:0 0 5px rgba(0,0,0,0.5);'></div>",
            iconSize: [15, 15],
            iconAnchor: [7, 7]
        });

        const iconDestination = L.divIcon({
            className: 'custom-div-icon',
            html: "<div style='background-color:#059669;width:15px;height:15px;border-radius:50%;border:3px solid white;box-shadow:0 0 5px rgba(0,0,0,0.5);'></div>",
            iconSize: [15, 15],
            iconAnchor: [7, 7]
        });

        const latlngs = [];
        
        // Add markers and build path
        locations.forEach((loc, index) => {
            const isDestination = index === locations.length - 1 && loc.status === 'Pesanan Selesai';
            const marker = L.marker([loc.lat, loc.lng], {
                icon: isDestination ? iconDestination : iconCurrent
            }).addTo(map);
            
            marker.bindPopup(`<b>${loc.status}</b><br>${loc.location}`);
            latlngs.push([loc.lat, loc.lng]);
        });

        // Draw path
        if (latlngs.length > 1) {
            L.polyline(latlngs, {color: '#ffd0e5', weight: 3, dashArray: '5, 10'}).addTo(map);
            map.fitBounds(L.polyline(latlngs).getBounds(), {padding: [50, 50]});
        }
    });
</script>
@endpush