@extends('layouts.app')

@section('title', 'Buat sandi baru — Skinbae.ID')

@section('content')
<div class="mx-auto flex min-h-[70vh] max-w-7xl items-center justify-center px-4 py-10 sm:px-6 lg:px-8">
    <div class="w-full max-w-md rounded-3xl border border-brand-100 bg-white p-7 shadow-xl shadow-brand-200/30">
        <div class="mb-2 flex items-center gap-3">
            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-amber-50 text-amber-700">
                <i class="fas fa-key text-lg" aria-hidden="true"></i>
            </div>
            <div>
                <h1 class="text-2xl font-black tracking-tight text-neutral-900">Sandi baru</h1>
                <p class="text-xs text-neutral-500">Token dari email hanya berlaku terbatas</p>
            </div>
        </div>
        <p class="mt-2 text-sm text-neutral-500">Gunakan sandi yang belum pernah dipakai di situs lain dan mudah Anda ingat.</p>

        <form action="{{ route('password.update') }}" method="POST" class="relative mt-6 space-y-4" x-data="registerPassword()">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <x-auth.honeypot-field />

            <div>
                <label for="email" class="mb-1 block text-sm font-semibold text-neutral-700">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email', $email) }}" required autocomplete="username" class="w-full rounded-2xl border border-brand-200 px-4 py-3 text-sm outline-none transition focus:border-brand-400 focus:ring-4 focus:ring-brand-100">
                @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password" class="mb-1 block text-sm font-semibold text-neutral-700">Kata sandi baru</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    autocomplete="new-password"
                    class="w-full rounded-2xl border border-brand-200 px-4 py-3 text-sm outline-none transition focus:border-brand-400 focus:ring-4 focus:ring-brand-100"
                    x-model="password"
                    @input="updateStrength"
                >
                <div class="mt-2 space-y-2 rounded-xl border border-brand-100 bg-brand-50/50 p-3 text-xs text-neutral-700">
                    <p class="font-semibold text-brand-900">Indikator kekuatan</p>
                    <div class="h-2 overflow-hidden rounded-full bg-neutral-200">
                        <div class="h-full rounded-full transition-all duration-300" :class="strengthClass" :style="'width: ' + strengthPct + '%'"></div>
                    </div>
                    <p class="text-[0.7rem] text-neutral-600"><span class="font-medium text-neutral-800" x-text="strengthLabel"></span> — minimal 8 karakter (wajib).</p>
                </div>
                @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password_confirmation" class="mb-1 block text-sm font-semibold text-neutral-700">Konfirmasi sandi</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="w-full rounded-2xl border border-brand-200 px-4 py-3 text-sm outline-none transition focus:border-brand-400 focus:ring-4 focus:ring-brand-100">
            </div>

            <x-auth.captcha-field :captcha="$captcha" />

            <button type="submit" class="w-full rounded-2xl bg-gradient-to-r from-brand-500 to-brand-600 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-brand-500/25 transition hover:translate-y-[-1px]">
                <i class="fas fa-lock me-2" aria-hidden="true"></i>
                Simpan sandi baru
            </button>
        </form>

        <a href="{{ route('login') }}" class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-brand-600 hover:text-brand-700">
            <i class="fas fa-arrow-left" aria-hidden="true"></i>
            Kembali ke login
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
function registerPassword() {
    return {
        password: '',
        strengthPct: 0,
        strengthLabel: '—',
        strengthClass: 'bg-neutral-300',

        init() {
            this.updateStrength();
        },

        updateStrength() {
            const p = this.password || '';
            let score = 0;
            if (p.length >= 8) score += 25;
            if (p.length >= 12) score += 15;
            if (/[a-z]/.test(p) && /[A-Z]/.test(p)) score += 20;
            if (/\d/.test(p)) score += 15;
            if (/[^A-Za-z0-9]/.test(p)) score += 25;
            score = Math.min(100, score);
            this.strengthPct = p.length ? score : 0;
            if (p.length === 0) {
                this.strengthLabel = '—';
                this.strengthClass = 'bg-neutral-300';
            } else if (score < 40) {
                this.strengthLabel = 'Lemah';
                this.strengthClass = 'bg-red-500';
            } else if (score < 70) {
                this.strengthLabel = 'Sedang';
                this.strengthClass = 'bg-amber-500';
            } else {
                this.strengthLabel = 'Kuat';
                this.strengthClass = 'bg-emerald-500';
            }
        },
    };
}
</script>
@endpush
