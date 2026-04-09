@extends('skinbae.layout')

@section('title', 'Skinbae.ID — Elegan & Harmonis')
@section('meta_description', 'Skinbae.ID: perawatan kecantikan premium dengan pendekatan personal dan tenang.')

@section('content')
<section class="position-relative overflow-hidden" style="min-height: 85vh; background: linear-gradient(135deg, #2c2a26 0%, #3d3832 50%, #2c2a26 100%);">
    <div class="position-absolute top-0 start-0 w-100 h-100 opacity-30" style="background: url('https://picsum.photos/seed/skinbaehero/1920/1080') center/cover no-repeat;"></div>
    <div class="container position-relative text-white d-flex align-items-center" style="min-height: 85vh;">
        <div class="row py-5 w-100">
            <div class="col-lg-8 col-xl-7">
                <p class="section-label text-white-50 mb-3" style="color: var(--sb-gold) !important;">Est. untuk Anda</p>
                <h1 class="display-3 fw-semibold mb-4 lh-sm" style="font-family: 'Cormorant Garamond', serif;">Harmoni kecantikan yang menyentuh setiap detail.</h1>
                <p class="lead text-white-75 mb-4 fw-light" style="max-width: 32rem;">Skinbae.ID menghadirkan ritual perawatan yang tenang, terukur, dan disesuaikan — agar Anda merasa percaya diri dalam irama kehidupan sehari-hari.</p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ config('skinbae.booking_url') }}" class="btn btn-sb-primary px-4 py-3" target="_blank" rel="noopener">Jadwalkan kunjungan</a>
                    <a href="{{ route('skinbae.services') }}" class="btn btn-sb-outline border-white text-white px-4 py-3">Jelajahi layanan</a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 py-lg-6" style="background: var(--sb-cream);">
    <div class="container py-lg-4">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <p class="section-label mb-2">Misi kami</p>
                <div class="divider-gold mb-4"></div>
                <h2 class="display-6 mb-4">Kecantikan sebagai pengalaman utuh, bukan sekadar tampilan.</h2>
                <p class="text-secondary mb-4">Kami percaya perawatan sejati memadukan keahlian terlatih, produk yang dipilih dengan cermat, dan suasana yang memungkinkan Anda benar-benar beristirahat. Setiap sesi dirancang seperti satu gerakan dalam simfoni — mulus, bermakna, dan personal.</p>
                <ul class="list-unstyled small text-secondary">
                    <li class="mb-2"><i class="fas fa-leaf text-success me-2"></i> Protokol higienis & konsultasi menyeluruh</li>
                    <li class="mb-2"><i class="fas fa-heart text-danger me-2 opacity-75"></i> Pendekatan lembut untuk kulit sensitif</li>
                    <li><i class="fas fa-star text-warning me-2"></i> Terapis bersertifikat & berpengalaman</li>
                </ul>
            </div>
            <div class="col-lg-6">
                <div class="ratio ratio-4x3 shadow-sm overflow-hidden" style="background: #ddd;">
                    <img src="https://picsum.photos/seed/skinbaemission/800/600" alt="Interior studio kecantikan" class="object-fit-cover" loading="lazy">
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 py-lg-6">
    <div class="container">
        <div class="text-center mb-5">
            <p class="section-label mb-2">Layanan unggulan</p>
            <h2 class="display-6">Yang paling sering dipilih klien</h2>
        </div>
        <div class="row g-4">
            @foreach([
                ['title' => 'Skinbae Glow Facial', 'desc' => 'Pembersihan mendalam, hidrasi intensif, dan pijat wajah untuk kulit bersinar.', 'icon' => 'fa-spa'],
                ['title' => 'Silk Hair Ritual', 'desc' => 'Perawatan rambut premium dengan masker keratin dan scalp massage.', 'icon' => 'fa-wand-magic-sparkles'],
                ['title' => 'Bridal Harmony Package', 'desc' => 'Paket lengkap pra-acara: wajah, tangan, dan konsultasi gaya.', 'icon' => 'fa-ring'],
            ] as $svc)
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm rounded-0 bg-white">
                        <div class="card-body p-4 p-lg-5">
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width:3.5rem;height:3.5rem;background:var(--sb-cream);color:var(--sb-rose);">
                                <i class="fas {{ $svc['icon'] }} fa-lg"></i>
                            </div>
                            <h3 class="h4 mb-3">{{ $svc['title'] }}</h3>
                            <p class="text-secondary small mb-4">{{ $svc['desc'] }}</p>
                            <a href="{{ route('skinbae.services') }}" class="text-uppercase small fw-semibold text-decoration-none" style="color:var(--sb-rose);letter-spacing:.08em;">Detail →</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-5 py-lg-6" style="background: var(--sb-charcoal); color: rgba(255,255,255,.85);">
    <div class="container">
        <div class="text-center mb-5">
            <p class="section-label mb-2" style="color: var(--sb-gold);">Testimoni</p>
            <h2 class="display-6 text-white">Suara klien kami</h2>
        </div>
        <div class="row g-4">
            @foreach([
                ['q' => 'Suasana studio sangat menenangkan. Kulit saya terasa jauh lebih sehat setelah satu sesi facial.', 'a' => 'M. R.', 'l' => 'Jakarta'],
                ['q' => 'Paket bridal-nya detail banget — timnya profesional dan tidak terburu-buru.', 'a' => 'S. L.', 'l' => 'Tangerang'],
                ['q' => 'Hair ritual favorit saya. Rambut halus berhari-hari tanpa berat.', 'a' => 'K. P.', 'l' => 'Bekasi'],
            ] as $t)
                <div class="col-md-4">
                    <blockquote class="border border-secondary border-opacity-25 p-4 h-100 mb-0">
                        <p class="fst-italic mb-4 opacity-90">“{{ $t['q'] }}”</p>
                        <footer class="small text-white-50">{{ $t['a'] }} — {{ $t['l'] }}</footer>
                    </blockquote>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-5 py-lg-6">
    <div class="container">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
            <div>
                <p class="section-label mb-2">Portfolio</p>
                <h2 class="display-6 mb-0">Cuplikan hasil karya</h2>
            </div>
            <a href="{{ route('skinbae.gallery') }}" class="btn btn-sb-outline">Lihat galeri</a>
        </div>
        <div class="row g-3">
            @foreach([1 => 'Facial glow', 2 => 'Hair silk', 3 => 'Bridal prep', 4 => 'Spa hands'] as $i => $cap)
                <div class="col-6 col-lg-3">
                    <a href="{{ route('skinbae.gallery') }}" class="text-decoration-none text-dark">
                        <div class="ratio ratio-1x1 overflow-hidden shadow-sm">
                            <img src="https://picsum.photos/seed/sbport{{ $i }}/600/600" alt="{{ $cap }}" class="object-fit-cover hover-scale" loading="lazy">
                        </div>
                        <p class="small mt-2 mb-0 text-center text-secondary">{{ $cap }}</p>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-5" style="background: var(--sb-cream);">
    <div class="container text-center py-lg-4">
        <h2 class="display-6 mb-3">Siap merasakan perbedaannya?</h2>
        <p class="text-secondary mx-auto mb-4" style="max-width: 28rem;">Hubungi kami untuk konsultasi singkat atau pesan slot melalui kalender online.</p>
        <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="{{ config('skinbae.whatsapp_url') }}" class="btn btn-sb-primary px-4" target="_blank" rel="noopener"><i class="fab fa-whatsapp me-2"></i> WhatsApp</a>
            <a href="{{ route('skinbae.contact') }}" class="btn btn-sb-outline">Form kontak</a>
        </div>
    </div>
</section>

@push('styles')
<style>
    .object-fit-cover { object-fit: cover; width: 100%; height: 100%; }
    .hover-scale { transition: transform .4s ease; }
    a:hover .hover-scale { transform: scale(1.05); }
    @media (min-width: 992px) {
        .py-lg-6 { padding-top: 5rem !important; padding-bottom: 5rem !important; }
    }
</style>
@endpush
@endsection
