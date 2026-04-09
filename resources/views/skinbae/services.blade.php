@extends('skinbae.layout')

@section('title', 'Services — Skinbae.ID')
@section('meta_description', 'Layanan perawatan wajah, rambut, tubuh, dan paket khusus di Skinbae.ID.')

@section('content')
<section class="py-5" style="background: var(--sb-charcoal); color: #fff;">
    <div class="container py-lg-3 text-center">
        <p class="section-label mb-2" style="color: var(--sb-gold);">What we offer</p>
        <h1 class="display-4 text-white mb-3">Services</h1>
        <p class="text-white-50 mx-auto mb-0" style="max-width: 36rem;">Setiap treatment dilengkapi konsultasi singkat agar protokol sesuai kondisi kulit dan kebutuhan Anda.</p>
    </div>
</section>

@php
$categories = [
    'Wajah' => [
        ['name' => 'Skinbae Glow Facial', 'time' => '75 menit', 'desc' => 'Deep cleanse, ekstraksi ringan, masker hidrasi, dan facial massage untuk kulit segar dan lembut.'],
        ['name' => 'Luminous Peel Lite', 'time' => '45 menit', 'desc' => 'Eksfoliasi lembut untuk tekstur lebih halus; cocok untuk pemula.'],
        ['name' => 'Calm & Restore', 'time' => '60 menit', 'desc' => 'Fokus kulit sensitif: soothing mask dan pendinginan untuk kemerahan berkurang.'],
    ],
    'Rambut' => [
        ['name' => 'Silk Hair Ritual', 'time' => '90 menit', 'desc' => 'Keratin mask, scalp massage, dan blow style ringan.'],
        ['name' => 'Scalp Detox', 'time' => '45 menit', 'desc' => 'Pembersihan kulit kepala mendalam sebelum perawatan rutin.'],
    ],
    'Tubuh & tangan' => [
        ['name' => 'Harmony Body Polish', 'time' => '60 menit', 'desc' => 'Eksfoliasi tubuh lembut dan pelembapan untuk kulit halus.'],
        ['name' => 'Velvet Manicure', 'time' => '50 menit', 'desc' => 'Perawatan kuku dan kutikula dengan finishing mengkilap natural.'],
    ],
    'Paket acara' => [
        ['name' => 'Bridal Harmony', 'time' => 'By appointment', 'desc' => 'Paket wajah + tangan + trial makeup ringan; timeline disesuaikan jadwal Anda.'],
        ['name' => 'Editorial Glow', 'time' => 'Custom', 'desc' => 'Kolaborasi untuk pemotretan atau acara khusus — hubungi kami untuk brief.'],
    ],
];
@endphp

<section class="py-5 py-lg-6">
    <div class="container">
        @foreach($categories as $cat => $items)
            <div class="mb-5 pb-lg-4 {{ !$loop->last ? 'border-bottom border-light' : '' }}">
                <h2 class="display-6 mb-4">{{ $cat }}</h2>
                <div class="row g-4">
                    @foreach($items as $item)
                        <div class="col-lg-6">
                            <div class="card border-0 shadow-sm rounded-0 h-100">
                                <div class="card-body p-4 d-flex flex-column">
                                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                        <h3 class="h5 mb-0 font-serif">{{ $item['name'] }}</h3>
                                        <span class="badge rounded-0 fw-normal text-secondary border" style="font-size: 0.65rem; letter-spacing: .05em;">{{ $item['time'] }}</span>
                                    </div>
                                    <p class="text-secondary small flex-grow-1 mb-3">{{ $item['desc'] }}</p>
                                    <div class="d-flex flex-wrap gap-2">
                                        <a href="{{ config('skinbae.booking_url') }}" class="btn btn-sb-primary btn-sm" target="_blank" rel="noopener">Book</a>
                                        <a href="{{ route('skinbae.contact') }}?interest={{ urlencode($item['name']) }}" class="btn btn-sb-outline btn-sm">Tanya dulu</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <div class="alert border-0 rounded-0 small" style="background: var(--sb-cream);">
            <strong>Catatan:</strong> Durasi perkiraan. Harga dan ketersediaan slot dapat berubah — konfirmasi melalui WhatsApp atau form kontak.
        </div>
    </div>
</section>

@push('styles')
<style>@media (min-width: 992px) { .py-lg-6 { padding-top: 5rem !important; padding-bottom: 5rem !important; } }</style>
@endpush
@endsection
