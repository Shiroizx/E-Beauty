@extends('layouts.app')

@section('title', 'Register — Skinbae.ID')

@section('content')
<div class="mx-auto max-w-lg px-4 py-10 sm:px-6">
    <div class="overflow-hidden rounded-3xl border border-brand-100 bg-white shadow-xl shadow-brand-200/40">
        <div class="border-b border-brand-50 px-6 pb-2 pt-8 text-center">
            <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-50 text-brand-600">
                <i class="fas fa-user-shield text-xl" aria-hidden="true"></i>
            </div>
            <h1 class="text-2xl font-bold text-gradient-brand">Buat akun</h1>
            <p class="mt-2 text-sm text-neutral-600">Bergabung dengan Skinbae.ID — keamanan data Anda diprioritaskan.</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="relative space-y-4 p-6" x-data="registerPassword()">
            @csrf
            <x-auth.honeypot-field />

            <div>
                <label for="name" class="mb-1 block text-sm font-medium text-brand-800">Nama lengkap</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="Nama Anda" class="input-brand @error('name') border-red-300 ring-2 ring-red-100 @enderror">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="email" class="mb-1 block text-sm font-medium text-brand-800">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="nama@email.com" class="input-brand @error('email') border-red-300 ring-2 ring-red-100 @enderror">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="mb-1 block text-sm font-medium text-brand-800">Kata sandi</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="new-password"
                    placeholder="••••••••"
                    class="input-brand @error('password') border-red-300 ring-2 ring-red-100 @enderror"
                    x-model="password"
                    @input="updateStrength"
                >
                <div class="mt-2 space-y-2 rounded-xl border border-brand-100 bg-brand-50/50 p-3 text-xs text-neutral-700">
                    <p class="font-semibold text-brand-900">Persyaratan sandi</p>
                    <ul class="space-y-1">
                        <li class="flex items-center gap-2" :class="lenOk ? 'text-emerald-700' : ''">
                            <i class="fas fa-circle text-[0.35rem]" :class="lenOk ? 'text-emerald-500' : 'text-neutral-300'" aria-hidden="true"></i>
                            Minimal <strong>8 karakter</strong>
                        </li>
                        <li class="flex items-center gap-2 text-neutral-600">
                            <i class="fas fa-shield-halved text-brand-500" aria-hidden="true"></i>
                            Disarankan: campuran huruf besar/kecil, angka, dan simbol.
                        </li>
                    </ul>
                    <div class="mt-2">
                        <div class="mb-1 flex justify-between text-[0.65rem] font-medium uppercase tracking-wide text-neutral-500">
                            <span>Kekuatan</span>
                            <span x-text="strengthLabel"></span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-neutral-200">
                            <div class="h-full rounded-full transition-all duration-300" :class="strengthClass" :style="'width: ' + strengthPct + '%'"></div>
                        </div>
                    </div>
                </div>
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="password-confirm" class="mb-1 block text-sm font-medium text-brand-800">Konfirmasi kata sandi</label>
                <input id="password-confirm" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" class="input-brand">
            </div>

            <x-auth.captcha-field :captcha="$captcha" />

            <button type="submit" class="btn-brand w-full py-3 text-base">
                <i class="fas fa-user-plus me-2" aria-hidden="true"></i> Daftar
            </button>
            <p class="text-center text-sm text-neutral-600">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="font-semibold text-brand-600 hover:text-brand-800">Login</a>
            </p>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function registerPassword() {
    return {
        password: '',
        lenOk: false,
        strengthPct: 0,
        strengthLabel: '—',
        strengthClass: 'bg-neutral-300',

        init() {
            this.updateStrength();
        },

        updateStrength() {
            const p = this.password || '';
            this.lenOk = p.length >= 8;
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
