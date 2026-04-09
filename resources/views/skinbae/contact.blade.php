@extends('skinbae.layout')

@section('title', 'Contact — Skinbae.ID')
@section('meta_description', 'Hubungi Skinbae.ID untuk pertanyaan, booking, atau kolaborasi.')

@section('content')
<section class="py-5" style="background: var(--sb-cream);">
    <div class="container py-lg-2 text-center">
        <p class="section-label mb-2">Reach us</p>
        <h1 class="display-4 mb-3">Contact</h1>
        <p class="text-secondary mx-auto mb-0" style="max-width: 30rem;">Isi formulir di bawah — tim kami biasanya merespons dalam 1×24 jam kerja.</p>
    </div>
</section>

<section class="py-5 py-lg-6">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-5">
                <h2 class="h4 font-serif mb-4">Kunjungi & hubungi</h2>
                <ul class="list-unstyled text-secondary small mb-4">
                    <li class="mb-3 d-flex gap-3">
                        <span class="text-dark flex-shrink-0"><i class="fas fa-map-marker-alt"></i></span>
                        <span>{{ config('skinbae.address_line') }}</span>
                    </li>
                    <li class="mb-3 d-flex gap-3">
                        <span class="text-dark flex-shrink-0"><i class="fas fa-clock"></i></span>
                        <span>{{ config('skinbae.hours') }}</span>
                    </li>
                    <li class="mb-3 d-flex gap-3">
                        <span class="text-dark flex-shrink-0"><i class="fas fa-phone"></i></span>
                        @php $telHref = preg_replace('/[^0-9+]/', '', config('skinbae.phone_display')); @endphp
                        <span><a href="tel:{{ $telHref }}" class="text-secondary">{{ config('skinbae.phone_display') }}</a></span>
                    </li>
                </ul>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ config('skinbae.whatsapp_url') }}" class="btn btn-sb-primary" target="_blank" rel="noopener"><i class="fab fa-whatsapp me-2"></i>Chat WhatsApp</a>
                    <a href="{{ config('skinbae.booking_url') }}" class="btn btn-sb-outline" target="_blank" rel="noopener">Booking online</a>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-0">
                    <div class="card-body p-4 p-lg-5">
                        <form action="{{ route('skinbae.contact.store') }}" method="POST" novalidate>
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-uppercase" for="name">Nama</label>
                                    <input type="text" name="name" id="name" required class="form-control rounded-0 @error('name') is-invalid @enderror" value="{{ old('name') }}" autocomplete="name">
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-uppercase" for="email">Email</label>
                                    <input type="email" name="email" id="email" required class="form-control rounded-0 @error('email') is-invalid @enderror" value="{{ old('email') }}" autocomplete="email">
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-uppercase" for="phone">Telepon <span class="text-muted fw-normal">(opsional)</span></label>
                                    <input type="tel" name="phone" id="phone" class="form-control rounded-0 @error('phone') is-invalid @enderror" value="{{ old('phone') }}" autocomplete="tel">
                                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-uppercase" for="inquiry_type">Jenis pertanyaan</label>
                                    <select name="inquiry_type" id="inquiry_type" required class="form-select rounded-0 @error('inquiry_type') is-invalid @enderror">
                                        <option value="general" {{ old('inquiry_type', 'general') === 'general' ? 'selected' : '' }}>Umum</option>
                                        <option value="booking" {{ old('inquiry_type') === 'booking' ? 'selected' : '' }}>Booking / jadwal</option>
                                        <option value="collaboration" {{ old('inquiry_type') === 'collaboration' ? 'selected' : '' }}>Kolaborasi / media</option>
                                    </select>
                                    @error('inquiry_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-semibold text-uppercase" for="service_interest">Layanan yang diminati <span class="text-muted fw-normal">(opsional)</span></label>
                                    <input type="text" name="service_interest" id="service_interest" class="form-control rounded-0 @error('service_interest') is-invalid @enderror" value="{{ old('service_interest', request('interest')) }}" placeholder="Contoh: Skinbae Glow Facial">
                                    @error('service_interest')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-uppercase" for="preferred_date">Preferensi tanggal <span class="text-muted fw-normal">(opsional)</span></label>
                                    <input type="date" name="preferred_date" id="preferred_date" class="form-control rounded-0 @error('preferred_date') is-invalid @enderror" value="{{ old('preferred_date') }}" min="{{ date('Y-m-d') }}">
                                    @error('preferred_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-semibold text-uppercase" for="message">Pesan</label>
                                    <textarea name="message" id="message" rows="5" required class="form-control rounded-0 @error('message') is-invalid @enderror" placeholder="Ceritakan kebutuhan Anda...">{{ old('message') }}</textarea>
                                    @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input rounded-0 @error('privacy') is-invalid @enderror" type="checkbox" name="privacy" id="privacy" value="1" {{ old('privacy') ? 'checked' : '' }} required>
                                        <label class="form-check-label small text-secondary" for="privacy">Saya setuju data saya digunakan untuk menanggapi pesan ini sesuai kebijakan privasi studio.</label>
                                        @error('privacy')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-12 mt-2">
                                    <button type="submit" class="btn btn-sb-primary px-5">Kirim pesan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('styles')
<style>@media (min-width: 992px) { .py-lg-6 { padding-top: 5rem !important; padding-bottom: 5rem !important; } }</style>
@endpush
@endsection
