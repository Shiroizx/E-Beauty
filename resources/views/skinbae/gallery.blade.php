@extends('skinbae.layout')

@section('title', 'Gallery — Skinbae.ID')
@section('meta_description', 'Galeri hasil perawatan dan suasana Skinbae.ID.')

@section('content')
<section class="py-5" style="background: var(--sb-cream);">
    <div class="container py-lg-2 text-center">
        <p class="section-label mb-2">Portfolio</p>
        <h1 class="display-4 mb-3">Gallery</h1>
        <p class="text-secondary mx-auto mb-0" style="max-width: 32rem;">Ilustrasi suasana dan hasil perawatan. Foto klien asli hanya ditampilkan dengan persetujuan tertulis.</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <ul class="nav nav-pills justify-content-center flex-wrap gap-2 mb-5" id="galleryTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active rounded-0 px-4" id="tab-all" data-bs-toggle="pill" data-bs-target="#pane-all" type="button" role="tab">Semua</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-0 px-4" id="tab-face" data-bs-toggle="pill" data-bs-target="#pane-face" type="button" role="tab">Wajah</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-0 px-4" id="tab-hair" data-bs-toggle="pill" data-bs-target="#pane-hair" type="button" role="tab">Rambut</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-0 px-4" id="tab-space" data-bs-toggle="pill" data-bs-target="#pane-space" type="button" role="tab">Studio</button>
            </li>
        </ul>

        <div class="tab-content">
            @php
                $items = [
                    ['id' => 'g1', 'cat' => 'face', 'seed' => 'sbgal1', 'cap' => 'Setelah facial glow'],
                    ['id' => 'g2', 'cat' => 'hair', 'seed' => 'sbgal2', 'cap' => 'Silk hair finishing'],
                    ['id' => 'g3', 'cat' => 'face', 'seed' => 'sbgal3', 'cap' => 'Kulit lembut & sehat'],
                    ['id' => 'g4', 'cat' => 'space', 'seed' => 'sbgal4', 'cap' => 'Ruang treatment'],
                    ['id' => 'g5', 'cat' => 'hair', 'seed' => 'sbgal5', 'cap' => 'Natural waves'],
                    ['id' => 'g6', 'cat' => 'space', 'seed' => 'sbgal6', 'cap' => 'Area tunggu'],
                    ['id' => 'g7', 'cat' => 'face', 'seed' => 'sbgal7', 'cap' => 'Before & after (ilustrasi)'],
                    ['id' => 'g8', 'cat' => 'hair', 'seed' => 'sbgal8', 'cap' => 'Bridal styling'],
                ];
            @endphp

            <div class="tab-pane fade show active" id="pane-all" role="tabpanel">
                <div class="row g-3">
                    @foreach($items as $img)
                        <div class="col-6 col-md-4 col-lg-3">
                            <button type="button" class="btn p-0 border-0 w-100 text-start gallery-thumb" data-bs-toggle="modal" data-bs-target="#modal-{{ $img['id'] }}">
                                <div class="ratio ratio-1x1 overflow-hidden shadow-sm">
                                    <img src="https://picsum.photos/seed/{{ $img['seed'] }}/600/600" alt="{{ $img['cap'] }}" class="w-100 h-100 object-fit-cover gallery-img" loading="lazy">
                                </div>
                                <p class="small text-secondary mt-2 mb-0 px-1">{{ $img['cap'] }}</p>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>

            @foreach(['face' => 'pane-face', 'hair' => 'pane-hair', 'space' => 'pane-space'] as $cat => $paneId)
                <div class="tab-pane fade" id="{{ $paneId }}" role="tabpanel">
                    <div class="row g-3">
                        @foreach($items as $img)
                            @continue($img['cat'] !== $cat)
                            <div class="col-6 col-md-4 col-lg-3">
                                <button type="button" class="btn p-0 border-0 w-100 text-start gallery-thumb" data-bs-toggle="modal" data-bs-target="#modal-{{ $img['id'] }}">
                                    <div class="ratio ratio-1x1 overflow-hidden shadow-sm">
                                        <img src="https://picsum.photos/seed/{{ $img['seed'] }}/600/600" alt="{{ $img['cap'] }}" class="w-100 h-100 object-fit-cover gallery-img" loading="lazy">
                                    </div>
                                    <p class="small text-secondary mt-2 mb-0 px-1">{{ $img['cap'] }}</p>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        @foreach($items as $img)
            <div class="modal fade" id="modal-{{ $img['id'] }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content border-0 rounded-0">
                        <div class="modal-body p-0">
                            <img src="https://picsum.photos/seed/{{ $img['seed'] }}/1200/900" class="w-100" alt="{{ $img['cap'] }}">
                        </div>
                        <div class="modal-footer border-0">
                            <p class="small mb-0 text-secondary">{{ $img['cap'] }}</p>
                            <button type="button" class="btn btn-sb-outline btn-sm" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>

@push('styles')
<style>
    .nav-pills .nav-link { color: var(--sb-charcoal); border: 1px solid transparent; }
    .nav-pills .nav-link.active { background: var(--sb-charcoal); color: #fff; }
    .nav-pills .nav-link:not(.active):hover { border-color: rgba(44,42,38,.15); }
    .gallery-img { object-fit: cover; transition: transform .35s ease; }
    .gallery-thumb:hover .gallery-img { transform: scale(1.04); }
</style>
@endpush
@endsection
