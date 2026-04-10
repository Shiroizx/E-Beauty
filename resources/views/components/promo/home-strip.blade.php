@props(['promos'])

@if($promos->isNotEmpty())
<section class="border-y border-pink-100 bg-gradient-to-br from-brand-50/80 via-white to-pink-50/50 py-14 lg:py-16" aria-labelledby="promo-home-heading" x-data="promoHomeStrip()">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-10 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="mb-2 text-xs font-bold uppercase tracking-[0.2em] text-pink-500">Khusus member</p>
                <h2 id="promo-home-heading" class="text-2xl font-black text-neutral-900 sm:text-3xl">Promo untuk Anda</h2>
                <p class="mt-2 max-w-xl text-sm text-neutral-600">
                    Kode promo aktif dari toko. Salin kode dan masukkan di keranjang saat Anda melakukan checkout.
                </p>
            </div>
            <a href="{{ route('catalog') }}" class="inline-flex items-center gap-2 self-start rounded-full border-2 border-pink-200 bg-white px-5 py-2.5 text-sm font-semibold text-pink-700 shadow-sm transition hover:border-pink-400 hover:bg-pink-50">
                <i class="fas fa-store" aria-hidden="true"></i>
                Belanja pakai promo
            </a>
        </div>

        <div class="flex snap-x snap-mandatory gap-4 overflow-x-auto scroll-hidden pb-2 sm:grid sm:grid-cols-2 sm:overflow-visible lg:grid-cols-3">
            @foreach($promos as $promo)
                <article class="min-w-[min(100%,280px)] shrink-0 snap-start rounded-3xl border border-brand-100/80 bg-white/90 p-5 shadow-md shadow-brand-200/20 backdrop-blur-sm sm:min-w-0">
                    <div class="mb-3 flex items-start justify-between gap-2">
                        <div>
                            <h3 class="text-base font-bold text-neutral-900">{{ $promo->name }}</h3>
                            @if($promo->description)
                                <p class="mt-1 line-clamp-2 text-xs text-neutral-500">{{ $promo->description }}</p>
                            @endif
                        </div>
                        <span class="shrink-0 rounded-full bg-gradient-to-r from-pink-500 to-rose-500 px-2.5 py-1 text-[11px] font-black text-white shadow-sm">
                            {{ $promo->formatted_discount }}
                        </span>
                    </div>

                    <div class="mb-3 flex flex-wrap items-center gap-2 text-[11px] text-neutral-600">
                        <span class="inline-flex items-center gap-1 rounded-lg bg-brand-50 px-2 py-1 font-medium text-brand-800">
                            <i class="fas fa-calendar-alt text-brand-500" aria-hidden="true"></i>
                            {{ $promo->start_date->translatedFormat('d M') }} – {{ $promo->end_date->translatedFormat('d M Y') }}
                        </span>
                        @if($promo->min_purchase)
                            <span class="inline-flex items-center gap-1 rounded-lg bg-amber-50 px-2 py-1 font-medium text-amber-900">
                                <i class="fas fa-receipt text-amber-600" aria-hidden="true"></i>
                                Min. belanja Rp {{ number_format((float) $promo->min_purchase, 0, ',', '.') }}
                            </span>
                        @endif
                        @if($promo->products_count > 0)
                            <span class="inline-flex items-center gap-1 rounded-lg bg-violet-50 px-2 py-1 font-medium text-violet-800">
                                <i class="fas fa-box-open text-violet-500" aria-hidden="true"></i>
                                Produk tertentu
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 rounded-lg bg-emerald-50 px-2 py-1 font-medium text-emerald-800">
                                <i class="fas fa-globe text-emerald-500" aria-hidden="true"></i>
                                Semua produk
                            </span>
                        @endif
                    </div>

                    <div class="flex items-center gap-2 rounded-2xl border-2 border-dashed border-pink-200 bg-pink-50/50 px-3 py-2.5">
                        <code class="flex-1 font-mono text-sm font-bold tracking-wider text-pink-800">{{ $promo->code }}</code>
                        <button
                            type="button"
                            class="rounded-xl bg-pink-600 px-3 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-pink-400 focus:ring-offset-2"
                            data-code="{{ $promo->code }}"
                            @click="copyCode($event.currentTarget.dataset.code)"
                            aria-label="Salin kode promo ke papan klip"
                        >
                            <span x-show="!copied || lastCode !== @js($promo->code)"><i class="fas fa-copy me-1" aria-hidden="true"></i> Salin</span>
                            <span x-show="copied && lastCode === @js($promo->code)" x-cloak><i class="fas fa-check me-1" aria-hidden="true"></i> Disalin</span>
                        </button>
                    </div>

                    @if($promo->products->isNotEmpty())
                        <div class="mt-3 border-t border-brand-100 pt-3">
                            <p class="mb-2 text-[10px] font-bold uppercase tracking-wider text-neutral-400">Contoh produk</p>
                            <ul class="flex flex-wrap gap-1.5">
                                @foreach($promo->products as $p)
                                    <li>
                                        <a href="{{ route('products.show', $p->slug) }}" class="inline-block max-w-[140px] truncate rounded-lg bg-neutral-100 px-2 py-1 text-[11px] font-medium text-neutral-700 transition hover:bg-pink-100 hover:text-pink-800">
                                            {{ $p->name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </article>
            @endforeach
        </div>

        <p class="sr-only" aria-live="polite" x-text="announce"></p>
    </div>
</section>
@endif

@once
    @push('scripts')
    <script>
        function promoHomeStrip() {
            return {
                copied: false,
                lastCode: '',
                announce: '',
                copyCode(code) {
                    if (!code) return;
                    navigator.clipboard.writeText(code).then(() => {
                        this.copied = true;
                        this.lastCode = code;
                        this.announce = 'Kode ' + code + ' disalin ke papan klip';
                        clearTimeout(this._t);
                        this._t = setTimeout(() => { this.copied = false; }, 2000);
                    }).catch(() => {
                        this.announce = 'Gagal menyalin, salin manual';
                    });
                },
            };
        }
    </script>
    @endpush
@endonce
