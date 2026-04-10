@props(['captcha'])

@php
    $captcha = $captcha ?? ['mode' => 'math', 'question' => '?', 'form' => 'login'];
@endphp

<div class="rounded-2xl border border-amber-100 bg-amber-50/60 p-4">
    <div class="mb-3 flex items-center gap-2 text-sm font-semibold text-amber-900">
        <i class="fas fa-user-shield text-amber-600" aria-hidden="true"></i>
        Verifikasi keamanan
    </div>

    @if(($captcha['mode'] ?? '') === 'recaptcha' && !empty($captcha['site_key']))
        <p class="mb-3 text-xs text-amber-900/80">Selesaikan CAPTCHA Google di bawah ini.</p>
        <div class="g-recaptcha" data-sitekey="{{ $captcha['site_key'] }}"></div>
        @once
            @push('scripts')
                <script src="https://www.google.com/recaptcha/api.js" async defer></script>
            @endpush
        @endonce
    @else
        <label for="captcha_answer" class="mb-1 block text-sm font-medium text-amber-950">
            Berapa hasil <span class="font-mono font-bold text-brand-700">{{ $captcha['question'] ?? '?' }}</span> ?
        </label>
        <input
            type="number"
            name="captcha_answer"
            id="captcha_answer"
            inputmode="numeric"
            required
            autocomplete="off"
            class="w-full max-w-xs rounded-xl border-2 border-amber-200 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-brand-400 focus:ring-2 focus:ring-brand-100"
            placeholder="Jawaban"
        >
    @endif
</div>

@error('captcha')
    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
@enderror
