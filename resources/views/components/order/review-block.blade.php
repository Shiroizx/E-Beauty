@props([
    'order',
    'item',
    'state' => 'locked',
])

@php
    $p = $item->product;
    $slug = $p?->slug;
    $isOpen = $state === 'open';
    $oldPid = (int) old('product_id', 0);
    $hasOld = $oldPid === (int) $item->product_id;
    $oldRating = $hasOld ? (int) old('rating', 0) : 0;
@endphp

<article class="rounded-2xl border border-brand-100/90 bg-white p-4 shadow-sm shadow-brand-100/40 sm:p-5">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start">
        <div class="flex shrink-0 gap-3">
            @if($p && $p->image_url)
                <a href="{{ $slug ? route('products.show', $slug) : '#' }}" class="block h-16 w-16 shrink-0 overflow-hidden rounded-xl border border-brand-100 bg-brand-50/50 sm:h-20 sm:w-20">
                    <img src="{{ $p->image_url }}" alt="" class="h-full w-full object-cover" width="80" height="80" loading="lazy">
                </a>
            @endif
            <div class="min-w-0 flex-1 sm:hidden">
                <p class="text-sm font-semibold text-neutral-900">{{ $item->product_name }}</p>
                @if($slug)
                    <a href="{{ route('products.show', $slug) }}" class="mt-0.5 inline-flex items-center gap-1 text-xs font-medium text-brand-600 hover:text-brand-800">Lihat produk <i class="fas fa-chevron-right text-[0.6rem]" aria-hidden="true"></i></a>
                @endif
            </div>
        </div>

        <div class="min-w-0 flex-1">
            <div class="hidden sm:block">
                <h3 class="font-semibold text-neutral-900">{{ $item->product_name }}</h3>
                @if($slug)
                    <a href="{{ route('products.show', $slug) }}" class="mt-1 inline-flex items-center gap-1 text-xs font-medium text-brand-600 hover:text-brand-800">Lihat halaman produk <i class="fas fa-external-link-alt text-[0.6rem]" aria-hidden="true"></i></a>
                @endif
            </div>

            @if($state === 'locked')
                <div class="mt-4 flex items-start gap-3 rounded-xl border border-neutral-200/80 bg-neutral-50/80 px-3.5 py-3 text-sm text-neutral-600">
                    <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white text-neutral-400 shadow-sm ring-1 ring-neutral-100" aria-hidden="true">
                        <i class="fas fa-lock text-sm"></i>
                    </span>
                    <div>
                        <p class="font-medium text-neutral-800">Ulasan belum tersedia</p>
                        <p class="mt-0.5 text-xs leading-relaxed text-neutral-500">Anda dapat memberi rating dan komentar setelah pesanan berstatus <strong class="text-neutral-700">Selesai</strong>.</p>
                    </div>
                </div>
            @elseif($state === 'pending')
                <div class="mt-4 flex items-start gap-3 rounded-xl border border-amber-200/80 bg-amber-50/90 px-3.5 py-3 text-sm text-amber-950">
                    <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white text-amber-500 shadow-sm ring-1 ring-amber-100" aria-hidden="true">
                        <i class="fas fa-hourglass-half text-sm"></i>
                    </span>
                    <div>
                        <p class="font-medium">Ulasan terkirim</p>
                        <p class="mt-0.5 text-xs leading-relaxed text-amber-900/80">Terima kasih atas masukan Anda.</p>
                    </div>
                </div>
            @elseif($state === 'approved')
                <div class="mt-4 flex items-start gap-3 rounded-xl border border-emerald-200/80 bg-emerald-50/90 px-3.5 py-3 text-sm text-emerald-950">
                    <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white text-emerald-500 shadow-sm ring-1 ring-emerald-100" aria-hidden="true">
                        <i class="fas fa-check-circle text-sm"></i>
                    </span>
                    <div>
                        <p class="font-medium">Ulasan Anda sudah dipublikasikan</p>
                        @if($slug)
                            <a href="{{ route('products.show', $slug) }}#ulasan" class="mt-2 inline-flex items-center gap-1 text-xs font-semibold text-emerald-800 hover:text-emerald-950">Lihat di halaman produk <i class="fas fa-arrow-right text-[0.6rem]" aria-hidden="true"></i></a>
                        @endif
                    </div>
                </div>
            @elseif($isOpen)
                <form
                    action="{{ route('reviews.store') }}"
                    method="post"
                    enctype="multipart/form-data"
                    class="mt-4 space-y-4"
                    x-data="{
                        rating: {{ max(0, min(5, $oldRating)) }},
                        hover: 0,
                        showRatingError: false,
                        maxComment: 1000,
                        get displayStars() { return this.hover || this.rating; },
                        submitForm(e) {
                            if (this.rating < 1) { e.preventDefault(); this.showRatingError = true; return; }
                            this.showRatingError = false;
                        }
                    }"
                    @submit="submitForm($event)"
                >
                    @csrf
                    <input type="hidden" name="order_number" value="{{ $order->order_number }}">
                    <input type="hidden" name="product_id" value="{{ $item->product_id }}">

                    <div>
                        <p id="rating-label-{{ $item->product_id }}" class="mb-2 text-xs font-semibold uppercase tracking-wide text-neutral-500">Rating</p>
                        <div class="flex flex-wrap items-center gap-3" role="group" aria-labelledby="rating-label-{{ $item->product_id }}">
                            <div class="flex gap-1">
                                @for($s = 1; $s <= 5; $s++)
                                    <button
                                        type="button"
                                        class="rounded-md p-1 text-2xl leading-none text-amber-300 transition hover:scale-105 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-400"
                                        :class="displayStars >= {{ $s }} ? 'text-amber-400' : 'text-neutral-200'"
                                        @mouseenter="hover = {{ $s }}"
                                        @mouseleave="hover = 0"
                                        @click="rating = {{ $s }}; showRatingError = false"
                                        :aria-pressed="rating === {{ $s }}"
                                        aria-label="Beri {{ $s }} bintang"
                                    >
                                        <i class="fas fa-star" aria-hidden="true"></i>
                                    </button>
                                @endfor
                            </div>
                            <input type="hidden" name="rating" :value="rating">
                            <span class="text-xs text-neutral-500" x-show="rating > 0" x-text="rating + '/5'"></span>
                        </div>
                        <p x-show="showRatingError" x-cloak class="mt-1.5 text-xs font-medium text-red-600">Pilih rating minimal 1 bintang.</p>
                        @error('rating')
                            <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="comment-{{ $item->product_id }}" class="mb-2 block text-xs font-semibold uppercase tracking-wide text-neutral-500">Komentar <span class="font-normal normal-case text-neutral-400">(opsional)</span></label>
                        <textarea
                            id="comment-{{ $item->product_id }}"
                            name="comment"
                            rows="3"
                            maxlength="1000"
                            class="w-full rounded-xl border border-brand-100 bg-white px-3.5 py-3 text-sm text-neutral-800 shadow-inner shadow-brand-50/30 placeholder:text-neutral-400 focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-200/60"
                            placeholder="Bagikan pengalaman Anda memakai produk ini…"
                        >{{ old('product_id') == $item->product_id ? old('comment', '') : '' }}</textarea>
                        <p class="mt-1 text-right text-[11px] text-neutral-400">Maks. 1000 karakter</p>
                        @error('comment')
                            <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="images-{{ $item->product_id }}" class="mb-2 block text-xs font-semibold uppercase tracking-wide text-neutral-500">Foto <span class="font-normal normal-case text-neutral-400">(opsional, maks. 3 file)</span></label>
                        <p class="mb-2 text-[11px] leading-relaxed text-neutral-500">Hanya gambar JPG atau PNG, maks. 3 MB per file. Video tidak didukung.</p>
                        <input
                            type="file"
                            name="images[]"
                            id="images-{{ $item->product_id }}"
                            accept="image/jpeg,image/png,image/jpg,.jpg,.jpeg,.png"
                            multiple
                            class="block w-full text-sm text-neutral-600 file:me-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-brand-700 hover:file:bg-brand-100"
                        >
                        @error('images')
                            <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                        @enderror
                        @error('images.*')
                            <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    @error('product_id')
                        <p class="text-xs font-medium text-red-600">{{ $message }}</p>
                    @enderror
                    @error('order_number')
                        <p class="text-xs font-medium text-red-600">{{ $message }}</p>
                    @enderror

                    <div class="flex flex-col-reverse gap-2 border-t border-brand-50 pt-4 sm:flex-row sm:justify-end">
                        <p class="text-[11px] leading-relaxed text-neutral-400 sm:me-auto sm:max-w-sm sm:self-center">
                            Ulasan Anda membantu pembeli lain memilih produk yang tepat.
                        </p>
                        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-brand-500 to-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-brand-400/25 transition hover:from-brand-600 hover:to-brand-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-400 focus-visible:ring-offset-2">
                            <i class="fas fa-paper-plane text-xs" aria-hidden="true"></i>
                            Kirim ulasan
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</article>
