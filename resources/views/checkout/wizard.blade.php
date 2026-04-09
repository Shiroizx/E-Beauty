@extends('layouts.app')

@section('title', 'Checkout — Skinbae.ID')

@section('content')
@php
    $b = $wizard['biodata'] ?? [];
    $s = $wizard['shipping'] ?? [];
@endphp
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    <nav aria-label="Breadcrumb" class="mb-4 text-sm text-neutral-500">
        <ol class="flex flex-wrap gap-2">
            <li><a href="{{ route('cart.index') }}" class="hover:text-brand-600">Keranjang</a></li>
            <li class="text-brand-300" aria-hidden="true">/</li>
            <li class="font-medium text-brand-800">Checkout</li>
        </ol>
    </nav>

    <h1 class="text-2xl font-bold text-brand-900 sm:text-3xl">Checkout</h1>
    <p class="mt-1 text-sm text-neutral-600">Tiga langkah: biodata → pengiriman → pembayaran.</p>

    <div class="mt-6 overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-md shadow-brand-200/20">
        <div class="flex flex-wrap items-center justify-center gap-2 p-4 md:gap-3 md:p-5">
            @foreach([1 => 'Biodata', 2 => 'Pengiriman', 3 => 'Pembayaran'] as $num => $label)
                <div class="flex min-w-0 flex-[1_1_30%] items-center justify-center gap-2 rounded-xl px-2 py-2 text-center transition md:flex-initial md:px-4
                    {{ $step === $num ? 'bg-gradient-to-r from-brand-100/80 to-brand-50 ring-2 ring-brand-200' : '' }}
                    {{ $step > $num ? 'bg-emerald-50/80' : '' }}
                    {{ $step < $num ? 'opacity-60' : '' }}">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-sm font-bold
                        {{ $step === $num ? 'bg-gradient-to-br from-brand-400 to-brand-600 text-white shadow-md shadow-brand-400/30' : '' }}
                        {{ $step > $num ? 'bg-emerald-500 text-white' : '' }}
                        {{ $step < $num ? 'bg-brand-100 text-brand-500' : '' }}">
                        @if($step > $num)
                            <i class="fas fa-check text-xs" aria-hidden="true"></i>
                        @else
                            {{ $num }}
                        @endif
                    </span>
                    <span class="truncate text-xs font-semibold text-brand-900 sm:text-sm">{{ $label }}</span>
                </div>
                @if($num < 3)
                    <i class="fas fa-chevron-right hidden text-brand-200 md:block" aria-hidden="true"></i>
                @endif
            @endforeach
        </div>
    </div>

    <div class="mt-8 flex flex-col gap-8 lg:flex-row lg:items-start">
        <div class="min-w-0 flex-1 lg:order-1">
            @if($step === 1)
                <form action="{{ route('checkout.step1') }}" method="POST" class="overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-md" novalidate>
                    @csrf
                    <div class="border-b border-brand-50 px-5 pb-0 pt-6">
                        <h2 class="text-lg font-bold text-brand-900"><i class="fas fa-id-card me-2 text-brand-400" aria-hidden="true"></i> Biodata</h2>
                        <p class="mt-1 text-sm text-neutral-600">Data kontak untuk konfirmasi pesanan.</p>
                    </div>
                    <div class="space-y-4 p-5">
                        <div>
                            <label class="mb-1 block text-sm font-semibold text-brand-800" for="full_name">Nama lengkap</label>
                            <input type="text" name="full_name" id="full_name" required class="input-brand @error('full_name') border-red-300 ring-2 ring-red-100 @enderror" value="{{ old('full_name', $b['full_name'] ?? auth()->user()->name) }}" autocomplete="name">
                            @error('full_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-semibold text-brand-800" for="email">Email</label>
                            <input type="email" name="email" id="email" required readonly class="input-brand cursor-not-allowed bg-brand-50/80 @error('email') border-red-300 @enderror" value="{{ old('email', $b['email'] ?? auth()->user()->email) }}" autocomplete="email">
                            <p class="mt-1 text-xs text-neutral-500">Menggunakan email akun Anda.</p>
                            @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-semibold text-brand-800" for="phone">Nomor telepon / WhatsApp</label>
                            <input type="tel" name="phone" id="phone" required inputmode="tel" class="input-brand @error('phone') border-red-300 ring-2 ring-red-100 @enderror" value="{{ old('phone', $b['phone'] ?? '') }}" placeholder="08xxxxxxxxxx" autocomplete="tel">
                            @error('phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div class="flex flex-col-reverse gap-2 border-t border-brand-50 p-5 sm:flex-row sm:justify-between">
                        <a href="{{ route('cart.index') }}" class="btn-brand-outline py-2.5 text-center text-sm">Kembali ke keranjang</a>
                        <button type="submit" class="btn-brand py-2.5 text-sm">Lanjut ke pengiriman</button>
                    </div>
                </form>
            @elseif($step === 2)
                <form action="{{ route('checkout.step2') }}" method="POST" class="overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-md" novalidate>
                    @csrf
                    <div class="border-b border-brand-50 px-5 pb-0 pt-6">
                        <h2 class="text-lg font-bold text-brand-900"><i class="fas fa-truck me-2 text-brand-400" aria-hidden="true"></i> Pengiriman</h2>
                        <p class="mt-1 text-sm text-neutral-600">Alamat pengiriman dan catatan kurir.</p>
                    </div>
                    <div class="space-y-4 p-5">
                        <div>
                            <label class="mb-1 block text-sm font-semibold text-brand-800" for="recipient_name">Nama penerima <span class="font-normal text-neutral-500">(opsional)</span></label>
                            <input type="text" name="recipient_name" id="recipient_name" class="input-brand @error('recipient_name') border-red-300 @enderror" value="{{ old('recipient_name', $s['recipient_name'] ?? '') }}" placeholder="Kosongkan jika sama dengan biodata">
                            @error('recipient_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-semibold text-brand-800" for="shipping_address_line">Alamat lengkap</label>
                            <textarea name="shipping_address_line" id="shipping_address_line" rows="3" required class="input-brand @error('shipping_address_line') border-red-300 @enderror" placeholder="Jalan, nomor rumah, RT/RW">{{ old('shipping_address_line', $s['shipping_address_line'] ?? '') }}</textarea>
                            @error('shipping_address_line')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-sm font-semibold text-brand-800" for="prov_select">Provinsi</label>
                                <select id="prov_select" class="input-brand @error('shipping_province') border-red-300 @enderror" required>
                                    <option value="">Pilih Provinsi</option>
                                </select>
                                <input type="hidden" name="shipping_province" id="shipping_province" value="{{ old('shipping_province', $s['shipping_province'] ?? '') }}">
                                @error('shipping_province')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-semibold text-brand-800" for="city_select">Kota / Kabupaten</label>
                                <select id="city_select" class="input-brand @error('shipping_city') border-red-300 @enderror" required disabled>
                                    <option value="">Pilih Kota/Kab</option>
                                </select>
                                <input type="hidden" name="shipping_city" id="shipping_city" value="{{ old('shipping_city', $s['shipping_city'] ?? '') }}">
                                @error('shipping_city')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-semibold text-brand-800" for="dist_select">Kecamatan</label>
                                <select id="dist_select" class="input-brand @error('shipping_district') border-red-300 @enderror" required disabled>
                                    <option value="">Pilih Kecamatan</option>
                                </select>
                                <input type="hidden" name="shipping_district" id="shipping_district" value="{{ old('shipping_district', $s['shipping_district'] ?? '') }}">
                                @error('shipping_district')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-semibold text-brand-800" for="subdist_select">Kelurahan</label>
                                <select id="subdist_select" class="input-brand @error('shipping_subdistrict') border-red-300 @enderror" required disabled>
                                    <option value="">Pilih Kelurahan</option>
                                </select>
                                <input type="hidden" name="shipping_subdistrict" id="shipping_subdistrict" value="{{ old('shipping_subdistrict', $s['shipping_subdistrict'] ?? '') }}">
                                @error('shipping_subdistrict')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-semibold text-brand-800" for="shipping_postal_code">Kode pos</label>
                            <input type="text" name="shipping_postal_code" id="shipping_postal_code" required class="input-brand @error('shipping_postal_code') border-red-300 @enderror" value="{{ old('shipping_postal_code', $s['shipping_postal_code'] ?? '') }}">
                            @error('shipping_postal_code')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-semibold text-brand-800" for="customer_notes">Catatan <span class="font-normal text-neutral-500">(opsional)</span></label>
                            <textarea name="customer_notes" id="customer_notes" rows="2" maxlength="500" class="input-brand @error('customer_notes') border-red-300 @enderror" placeholder="Contoh: titip ke satpam">{{ old('customer_notes', $s['customer_notes'] ?? '') }}</textarea>
                            @error('customer_notes')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div class="mt-8 border-t border-brand-50 pt-6">
                            <h3 class="mb-4 text-base font-bold text-brand-900"><i class="fas fa-box-open me-2 text-brand-400" aria-hidden="true"></i> Opsi Pengiriman</h3>
                            @if($errors->has('shipping_service') || $errors->has('shipping_courier'))
                                <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-2 text-sm text-red-800">
                                    Silakan pilih layanan dan kurir pengiriman yang valid.
                                </div>
                            @endif
                            <div id="shipping_options_container" class="rounded-xl border border-brand-100 bg-brand-50/50 p-6 text-center text-sm text-neutral-500">
                                Pilih lokasi pengiriman terlebih dahulu untuk melihat opsi dan ongkos kirim.
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col-reverse gap-2 border-t border-brand-50 p-5 sm:flex-row sm:justify-between">
                        <a href="{{ route('checkout.step', ['step' => 1]) }}" class="btn-brand-outline py-2.5 text-center text-sm">Kembali</a>
                        <button type="submit" class="btn-brand py-2.5 text-sm">Lanjut ke pembayaran</button>
                    </div>
                </form>
            @else
                <form action="{{ route('checkout.step3') }}" method="POST" class="overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-md" novalidate>
                    @csrf
                    <div class="border-b border-brand-50 px-5 pb-0 pt-6">
                        <h2 class="text-lg font-bold text-brand-900"><i class="fas fa-credit-card me-2 text-brand-400" aria-hidden="true"></i> Pembayaran</h2>
                        <p class="mt-1 text-sm text-neutral-600">Pilih metode pembayaran.</p>
                    </div>
                    <div class="p-5">
                        @error('payment_method')
                            <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-2 text-sm text-red-800">{{ $message }}</div>
                        @enderror

                        <div class="space-y-3">
                            <label class="block cursor-pointer rounded-2xl border-2 border-brand-100 bg-white p-4 transition has-[:checked]:border-brand-400 has-[:checked]:shadow-[0_0_0_3px_rgba(244,81,140,0.2)]">
                                <div class="flex items-start gap-3">
                                    <input class="mt-1 h-4 w-4 border-brand-300 text-brand-500 focus:ring-brand-400" type="radio" name="payment_method" value="doku" required checked>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <span class="block font-bold text-brand-900">Bayar Online Otomatis</span>
                                            <span class="rounded-full bg-gradient-to-r from-brand-100 to-brand-200 px-2 py-0.5 text-xs font-semibold text-brand-800">Cepat &amp; Aman</span>
                                        </div>
                                        <span class="text-sm text-neutral-600">Bayar dengan mudah via Virtual Account, QRIS, kartu kredit/debit, e-wallet, paylater dan lainnya.</span>
                                        <div class="mt-2 flex flex-wrap items-center gap-2">
                                            <span class="rounded-md border border-neutral-200 bg-neutral-50 px-2 py-0.5 text-[0.6rem] font-semibold text-neutral-600">QRIS</span>
                                            <span class="rounded-md border border-neutral-200 bg-neutral-50 px-2 py-0.5 text-[0.6rem] font-semibold text-neutral-600">Virtual Account</span>
                                            <span class="rounded-md border border-neutral-200 bg-neutral-50 px-2 py-0.5 text-[0.6rem] font-semibold text-neutral-600">Credit Card</span>
                                            <span class="rounded-md border border-neutral-200 bg-neutral-50 px-2 py-0.5 text-[0.6rem] font-semibold text-neutral-600">e-Wallet</span>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="mt-6 rounded-2xl border border-brand-100 bg-brand-50/50 p-4 text-sm text-neutral-700">
                            <p class="mb-2 font-semibold text-brand-900">Ringkas data</p>
                            <p class="mb-1"><span class="text-neutral-500">Kontak:</span> {{ $b['full_name'] ?? '—' }}, {{ $b['phone'] ?? '—' }}</p>
                            <p><span class="text-neutral-500">Kirim ke:</span> {{ $s['shipping_address_line'] ?? '—' }}, {{ $s['shipping_city'] ?? '' }} — {{ $s['shipping_postal_code'] ?? '' }}</p>
                        </div>
                    </div>
                    <div class="flex flex-col-reverse gap-2 border-t border-brand-50 p-5 sm:flex-row sm:justify-between">
                        <a href="{{ route('checkout.step', ['step' => 2]) }}" class="btn-brand-outline py-2.5 text-center text-sm">Kembali</a>
                        <button type="submit" class="btn-brand py-2.5 text-sm"><i class="fas fa-lock me-2" aria-hidden="true"></i> Bayar &amp; buat pesanan</button>
                    </div>
                </form>
            @endif
        </div>

        <div class="w-full shrink-0 lg:sticky lg:top-24 lg:order-2 lg:w-96">
            <div class="rounded-2xl border border-brand-100 bg-gradient-to-b from-white to-brand-50/60 p-5 shadow-xl shadow-brand-300/25">
                <h2 class="mb-4 text-base font-bold text-brand-900">Ringkasan pesanan</h2>
                <ul class="mb-4 max-h-64 space-y-3 overflow-y-auto text-sm">
                    @foreach($lines as $line)
                        @php $p = $line->product; @endphp
                        <li class="flex gap-3 border-b border-brand-100 pb-3">
                            <img src="{{ $p->image_url }}" alt="" class="h-14 w-14 shrink-0 rounded-lg object-cover" width="56" height="56">
                            <div class="min-w-0 flex-1">
                                <p class="truncate font-semibold text-neutral-900">{{ $p->name }}</p>
                                <p class="text-neutral-500">{{ $line->quantity }} × {{ $p->formatted_final_price }}</p>
                            </div>
                            <p class="shrink-0 font-bold text-brand-800">Rp {{ number_format($p->final_price * $line->quantity, 0, ',', '.') }}</p>
                        </li>
                    @endforeach
                </ul>
                <div class="flex justify-between text-sm">
                    <span class="text-neutral-600">Subtotal</span>
                    <span class="font-semibold text-brand-900">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="mt-2 flex justify-between text-sm">
                    <span class="text-neutral-600">Ongkir</span>
                    <span class="font-semibold text-brand-900" id="summary_shipping_cost">
                        @if($shippingCost > 0)
                            Rp {{ number_format($shippingCost, 0, ',', '.') }}
                        @else
                            <span class="text-emerald-600">Gratis</span>
                        @endif
                    </span>
                </div>
                @if($amountToFreeShipping > 0)
                    <div class="mt-3 rounded-xl border border-brand-200 bg-brand-50 px-3 py-2 text-xs text-neutral-700">
                        + <strong>Rp {{ number_format($amountToFreeShipping, 0, ',', '.') }}</strong> lagi untuk gratis ongkir.
                    </div>
                @endif
                <div class="mt-4 flex items-center justify-between border-t border-brand-200 pt-4">
                    <span class="font-bold text-brand-900">Total</span>
                    <span class="text-xl font-bold text-gradient-brand" id="summary_total">Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if($step === 2)
            const API_BASE = '/api/regions';
            const els = {
                prov: document.getElementById('prov_select'),
                city: document.getElementById('city_select'),
                dist: document.getElementById('dist_select'),
                subdist: document.getElementById('subdist_select'),
                provHidden: document.getElementById('shipping_province'),
                cityHidden: document.getElementById('shipping_city'),
                distHidden: document.getElementById('shipping_district'),
                subdistHidden: document.getElementById('shipping_subdistrict'),
                shippingContainer: document.getElementById('shipping_options_container')
            };

            // Selected values from old input or session
            let selProv = els.provHidden.value;
            let selCity = els.cityHidden.value;
            let selDist = els.distHidden.value;
            let selSubdist = els.subdistHidden.value;

            // Load Provinces
            els.prov.innerHTML = '<option value="">Memuat Provinsi...</option>';
            els.prov.disabled = true;
            fetch(`${API_BASE}/provinces`)
                .then(r => r.json())
                .then(provinces => {
                    els.prov.innerHTML = '<option value="">Pilih Provinsi</option>';
                    els.prov.disabled = false;
                    provinces.forEach(p => {
                        const opt = document.createElement('option');
                        opt.value = p.id;
                        opt.textContent = p.name;
                        // Match by name
                        if (p.name === selProv) opt.selected = true;
                        els.prov.appendChild(opt);
                    });
                    if (els.prov.value) els.prov.dispatchEvent(new Event('change'));
                })
                .catch(err => {
                    els.prov.innerHTML = '<option value="">Gagal memuat</option>';
                });

            els.prov.addEventListener('change', function() {
                const id = this.value;
                const name = this.options[this.selectedIndex].text;
                els.provHidden.value = id ? name : '';
                
                resetSelect(els.city, 'Kota/Kab');
                resetSelect(els.dist, 'Kecamatan');
                resetSelect(els.subdist, 'Kelurahan');
                els.shippingContainer.innerHTML = '<div class="rounded-xl border border-brand-100 bg-brand-50/50 p-6 text-center text-sm text-neutral-500">Pilih lokasi pengiriman terlebih dahulu untuk melihat opsi dan ongkos kirim.</div>';

                if (!id) return;

                els.city.innerHTML = '<option value="">Memuat Kota/Kab...</option>';
                els.city.disabled = true;

                fetch(`${API_BASE}/cities/${id}`)
                    .then(r => r.json())
                    .then(cities => {
                        els.city.innerHTML = '<option value="">Pilih Kota/Kab</option>';
                        els.city.disabled = false;
                        cities.forEach(c => {
                            const opt = document.createElement('option');
                            opt.value = c.id;
                            opt.textContent = c.name;
                            if (c.name === selCity) opt.selected = true;
                            els.city.appendChild(opt);
                        });
                        if (els.city.value) els.city.dispatchEvent(new Event('change'));
                    })
                    .catch(err => {
                        els.city.innerHTML = '<option value="">Gagal memuat</option>';
                    });
            });

            els.city.addEventListener('change', function() {
                const id = this.value;
                const name = this.options[this.selectedIndex].text;
                els.cityHidden.value = id ? name : '';
                
                resetSelect(els.dist, 'Kecamatan');
                resetSelect(els.subdist, 'Kelurahan');

                if (!id) return;

                calculateShipping(); // Trigger calculation

                els.dist.innerHTML = '<option value="">Memuat Kecamatan...</option>';
                els.dist.disabled = true;

                fetch(`${API_BASE}/districts/${id}`)
                    .then(r => r.json())
                    .then(districts => {
                        els.dist.innerHTML = '<option value="">Pilih Kecamatan</option>';
                        els.dist.disabled = false;
                        districts.forEach(d => {
                            const opt = document.createElement('option');
                            opt.value = d.id;
                            opt.textContent = d.name;
                            if (d.name === selDist) opt.selected = true;
                            els.dist.appendChild(opt);
                        });
                        if (els.dist.value) els.dist.dispatchEvent(new Event('change'));
                    })
                    .catch(err => {
                        els.dist.innerHTML = '<option value="">Gagal memuat</option>';
                    });
            });

            els.dist.addEventListener('change', function() {
                const id = this.value;
                const name = this.options[this.selectedIndex].text;
                els.distHidden.value = id ? name : '';
                
                resetSelect(els.subdist, 'Kelurahan');

                if (!id) return;

                els.subdist.innerHTML = '<option value="">Memuat Kelurahan...</option>';
                els.subdist.disabled = true;

                fetch(`${API_BASE}/villages/${id}`)
                    .then(r => r.json())
                    .then(villages => {
                        els.subdist.innerHTML = '<option value="">Pilih Kelurahan</option>';
                        els.subdist.disabled = false;
                        villages.forEach(v => {
                            const opt = document.createElement('option');
                            opt.value = v.id;
                            opt.textContent = v.name;
                            if (v.name === selSubdist) opt.selected = true;
                            els.subdist.appendChild(opt);
                        });
                    })
                    .catch(err => {
                        els.subdist.innerHTML = '<option value="">Gagal memuat</option>';
                    });
            });

            els.subdist.addEventListener('change', function() {
                const id = this.value;
                const name = this.options[this.selectedIndex].text;
                els.subdistHidden.value = id ? name : '';
            });

            function resetSelect(el, label) {
                el.innerHTML = `<option value="">Pilih ${label}</option>`;
                el.disabled = true;
                const hidden = document.getElementById(el.id.replace('_select', ''));
                if (hidden) hidden.value = '';
            }

            function calculateShipping() {
                if (!els.provHidden.value || !els.cityHidden.value) return;

                els.shippingContainer.innerHTML = '<div class="p-6 text-center text-sm text-neutral-500"><i class="fas fa-spinner fa-spin me-2"></i> Menghitung ongkos kirim...</div>';

                fetch('{{ route("checkout.calculate-shipping") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        province: els.provHidden.value,
                        city: els.cityHidden.value
                    })
                })
                .then(r => r.json())
                .then(data => {
                    if(data.success) {
                        renderShippingOptions(data.rates);
                    }
                }).catch(err => {
                    els.shippingContainer.innerHTML = '<div class="text-red-500 text-sm">Gagal menghitung ongkos kirim.</div>';
                });
            }

            function renderShippingOptions(rates) {
                const oldCourier = '{{ old("shipping_courier", $s["shipping_courier"] ?? "") }}';
                let html = '<div class="space-y-4 text-left">';
                
                for (const [serviceKey, service] of Object.entries(rates)) {
                    html += `
                        <div class="rounded-xl border border-brand-100 bg-white p-4">
                            <div class="mb-3 flex items-center justify-between border-b border-brand-50 pb-2">
                                <span class="font-bold text-brand-900">${service.name}</span>
                                <span class="font-bold text-brand-600">Rp ${service.price.toLocaleString('id-ID')}</span>
                            </div>
                            <div class="space-y-2">
                                ${service.couriers.map(c => `
                                    <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-transparent p-2 hover:bg-brand-50 has-[:checked]:border-brand-200 has-[:checked]:bg-brand-50/50">
                                        <input type="radio" name="shipping_courier" value="${c.id}" class="h-4 w-4 border-brand-300 text-brand-500 focus:ring-brand-400" required onchange="setService('${serviceKey}')" 
                                            ${oldCourier === c.id ? 'checked' : ''}>
                                        <span class="text-sm font-medium text-neutral-700">${c.name}</span>
                                    </label>
                                `).join('')}
                            </div>
                        </div>
                    `;
                }

                html += `<input type="hidden" name="shipping_service" id="shipping_service" value="{{ old('shipping_service', $s['shipping_service'] ?? '') }}">`;
                html += '</div>';

                els.shippingContainer.innerHTML = html;
                
                // Set default service if courier is already checked
                const checked = document.querySelector('input[name="shipping_courier"]:checked');
                if (checked) {
                    checked.dispatchEvent(new Event('change'));
                }
            }
        @endif
        
        // Expose setService to window to update Total
        let currentRates = {};
        const subtotal = {{ $subtotal }};
        const freeShippingAt = {{ $freeShippingAt }};

        window.setService = function(val) {
            document.getElementById('shipping_service').value = val;
            
            // Recalculate summary
            const costElement = document.getElementById('summary_shipping_cost');
            const totalElement = document.getElementById('summary_total');
            if (!costElement || !totalElement || !currentRates[val]) return;

            let cost = currentRates[val].price;
            if (subtotal >= freeShippingAt) {
                cost = 0;
            }

            if (cost > 0) {
                costElement.textContent = `Rp ${cost.toLocaleString('id-ID')}`;
            } else {
                costElement.innerHTML = `<span class="text-emerald-600">Gratis</span>`;
            }

            const total = subtotal + cost;
            totalElement.textContent = `Rp ${total.toLocaleString('id-ID')}`;
        }

        // Intercept API rates to store globally
        const originalRenderShippingOptions = renderShippingOptions;
        renderShippingOptions = function(rates) {
            currentRates = rates;
            originalRenderShippingOptions(rates);
        };
    });
</script>
@endpush
