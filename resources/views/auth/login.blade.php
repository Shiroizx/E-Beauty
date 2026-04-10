@extends('layouts.app')

@section('title', 'Masuk — Skinbae.ID')

@push('styles')
<style>
    @keyframes blob { 0%,100% { border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%; } 50% { border-radius: 30% 60% 70% 40% / 50% 60% 30% 60%; } }
    @keyframes float { 0%,100% { transform: translateY(0px); } 50% { transform: translateY(-20px); } }
    @keyframes gradient-shift { 0% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } 100% { background-position: 0% 50%; } }
    @keyframes grain { 0%,100% { transform: translate(0,0); } 10% { transform: translate(-2%,-2%); } 20% { transform: translate(2%,2%); } 30% { transform: translate(-1%,3%); } 40% { transform: translate(3%,-1%); } 50% { transform: translate(-3%,2%); } 60% { transform: translate(3%,3%); } 70% { transform: translate(-2%,-3%); } 80% { transform: translate(2%,-2%); } 90% { transform: translate(-1%,2%); } }
    @keyframes slide-up { from { opacity: 0; transform: translateY(24px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes fade-in { from { opacity: 0; } to { opacity: 1; } }

    .brand-side {
        background: linear-gradient(135deg, #b82d5c 0%, #f4518c 50%, #ff6b9d 100%);
        background-size: 200% 200%;
        animation: gradient-shift 10s ease infinite;
    }
    .blob-1 { animation: blob 12s ease-in-out infinite; }
    .blob-2 { animation: blob 18s ease-in-out infinite reverse; }
    .blob-3 { animation: blob 15s ease-in-out infinite; animation-delay: -5s; }

    .brand-float { animation: float 6s ease-in-out infinite; }
    .brand-float-delay { animation: float 6s ease-in-out infinite; animation-delay: -2s; }

    .brand-side::before {
        content: '';
        position: absolute; inset: 0;
        background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 400 400' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)' opacity='0.04'/%3E%3C/svg%3E");
        pointer-events: none;
    }

    .form-container { animation: slide-up 0.7s cubic-bezier(0.22, 1, 0.36, 1) 0.1s both; }

    .input-float { transition: border-color 0.25s ease, box-shadow 0.25s ease, transform 0.25s ease; }
    .input-float:focus { border-color: #f4518c; box-shadow: 0 0 0 3px rgba(244,81,140,0.15); transform: translateY(-1px); }
    .input-float.error { border-color: #ef4444; box-shadow: 0 0 0 3px rgba(239,68,68,0.1); }

    .btn-login {
        background: linear-gradient(135deg, #f4518c 0%, #d6386f 100%);
        transition: all 0.3s cubic-bezier(0.22, 1, 0.36, 1);
        position: relative; overflow: hidden;
    }
    .btn-login::before {
        content: ''; position: absolute; inset: 0;
        background: linear-gradient(135deg, #d6386f 0%, #b82d5c 100%);
        opacity: 0; transition: opacity 0.3s ease;
    }
    .btn-login:hover::before { opacity: 1; }
    .btn-login:hover { transform: translateY(-2px); box-shadow: 0 12px 28px -6px rgba(244,81,140,0.4); }
    .btn-login:active { transform: translateY(0); }
    .btn-login span { position: relative; z-index: 1; }

    .btn-login.loading { pointer-events: none; }
    .btn-login.loading span { opacity: 0; }
    .btn-login .spinner {
        position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
        width: 20px; height: 20px;
        border: 2px solid rgba(255,255,255,0.3);
        border-top-color: white;
        border-radius: 50%;
        animation: spin 0.7s linear infinite;
        display: none;
    }
    .btn-login.loading .spinner { display: block; }

    @keyframes spin { to { transform: translate(-50%, -50%) rotate(360deg); } }

    .password-toggle { transition: color 0.2s ease; }
    .password-toggle:hover { color: #f4518c; }

    .input-field:-webkit-autofill,
    .input-field:-webkit-autofill:hover, 
    .input-field:-webkit-autofill:focus, 
    .input-field:-webkit-autofill:active {
        -webkit-box-shadow: 0 0 0 30px white inset !important;
        -webkit-text-fill-color: #171717 !important;
        background-color: white !important;
        background-clip: content-box !important;
        transition: background-color 5000s ease-in-out 0s;
    }

    .floating-label {
        transition: all 0.25s cubic-bezier(0.22, 1, 0.36, 1);
        pointer-events: none;
        background-color: white;
        padding: 0 4px;
        border-radius: 4px;
    }
    .input-field:not(:placeholder-shown) + .floating-label,
    .input-field:focus + .floating-label {
        transform: translateY(-24px) scale(0.78);
        color: #f4518c;
        font-weight: 600;
        background-color: white;
    }

    .check-custom { transition: all 0.2s ease; }
    .check-custom:checked { background-color: #f4518c; border-color: #f4518c; }

    .divider-line { position: relative; }
    .divider-line::before {
        content: ''; position: absolute; top: 50%; left: 0; right: 0; height: 1px;
        background: #f0e8ef;
    }
    .divider-text { position: relative; background: white; padding: 0 1rem; }

    .brand-logo-text {
        font-family: 'Cormorant Garamond', Georgia, serif;
        font-weight: 700; font-style: italic;
    }

    .error-msg { animation: fade-in 0.3s ease; }

    .back-link { transition: all 0.2s ease; }
    .back-link:hover { color: #f4518c; padding-left: 4px; }

    @media (prefers-reduced-motion: reduce) {
        *, *::before, *::after { animation-duration: 0.01ms !important; transition-duration: 0.01ms !important; }
    }
</style>
@endpush

@section('content')
<div class="min-h-screen flex">

    <!-- ======================== BRAND / LEFT SIDE ======================== -->
    <div class="relative hidden lg:flex lg:w-1/2 brand-side overflow-hidden flex-col justify-between p-12 text-white">
        <!-- Decorative blobs -->
        <div class="blob-1 absolute -top-20 -left-20 w-96 h-96 bg-white/10" aria-hidden="true"></div>
        <div class="blob-2 absolute top-1/3 -right-10 w-72 h-72 bg-white/10" aria-hidden="true"></div>
        <div class="blob-3 absolute -bottom-20 left-1/3 w-80 h-80 bg-white/10" aria-hidden="true"></div>

        <!-- Logo -->
        <div class="relative z-10">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-3 group" aria-label="Kembali ke beranda Skinbae.ID">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20 backdrop-blur backdrop-saturate-150">
                    <i class="fas fa-gem text-lg text-white" aria-hidden="true"></i>
                </div>
                <span class="brand-logo-text text-2xl text-white tracking-wide">Skinbae.ID</span>
            </a>
        </div>

        <!-- Center quote -->
        <div class="relative z-10 brand-float">
            <div class="mb-8 brand-float-delay">
                <svg class="text-white/20 mb-4" width="48" height="36" viewBox="0 0 48 36" fill="currentColor" aria-hidden="true">
                    <path d="M0 36V22.8C0 15.6 1.2 10.2 3.6 6.6 6 3 9.6 1.2 14.4 0L18 5.4C15.6 6.6 13.8 8.4 12.6 10.8 11.4 13.2 10.8 16.2 10.8 19.8H21.6V36H0ZM26.4 36V22.8C26.4 15.6 27.6 10.2 30 6.6 32.4 3 36 1.2 40.8 0L44.4 5.4C42 6.6 40.2 8.4 39 10.8 37.8 13.2 37.2 16.2 37.2 19.8H48V36H26.4Z"/>
                </svg>
                <p class="text-3xl font-light leading-relaxed text-white/90 italic" style="font-family: 'Cormorant Garamond', Georgia, serif;">
                    Tempat terbaik untuk menemukan produk kecantikan premium yang asli dan terpercaya.
                </p>
            </div>
            <div class="flex items-center gap-4">
                <div class="h-px w-12 bg-white/30" aria-hidden="true"></div>
                <p class="text-sm font-medium text-white/70">Trusted by 15,000+ customers</p>
            </div>
        </div>

        <!-- Bottom stats -->
        <div class="relative z-10 grid grid-cols-3 gap-6 border-t border-white/20 pt-8">
            @foreach([
                ['val' => '15K+', 'label' => 'Pelanggan'],
                ['val' => '50+', 'label' => 'Brand'],
                ['val' => '4.9', 'label' => 'Rating'],
            ] as $stat)
                <div class="text-center">
                    <div class="text-2xl font-black text-white">{{ $stat['val'] }}</div>
                    <div class="text-xs text-white/60 mt-0.5">{{ $stat['label'] }}</div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- ======================== FORM / RIGHT SIDE ======================== -->
    <div class="flex w-full flex-col justify-center px-6 py-10 lg:w-1/2 lg:px-16 xl:px-24">

        <!-- Mobile logo -->
        <div class="lg:hidden mb-8 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 text-brand-700" aria-label="Kembali ke beranda">
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-brand-500 text-white">
                    <i class="fas fa-gem text-sm" aria-hidden="true"></i>
                </div>
                <span class="brand-logo-text text-xl text-brand-700">Skinbae.ID</span>
            </a>
            <a href="{{ route('home') }}" class="text-sm font-medium text-neutral-500 back-link flex items-center gap-1.5">
                <i class="fas fa-arrow-left text-xs" aria-hidden="true"></i> Beranda
            </a>
        </div>

        <div class="form-container mx-auto w-full max-w-md">

            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-3xl font-black text-neutral-900 tracking-tight">Masuk</h1>
                <p class="mt-2 text-neutral-500">Selamat datang kembali! Silakan masuk ke akun Anda.</p>
            </div>

            @if(session('success'))
                <div class="error-msg mb-6 rounded-2xl border border-emerald-100 bg-emerald-50 p-4 text-sm text-emerald-800" role="status">
                    <i class="fas fa-circle-check me-2" aria-hidden="true"></i>{{ session('success') }}
                </div>
            @endif

            <!-- Error Alert -->
            @if($errors->any())
                <div class="error-msg mb-6 rounded-2xl border border-red-100 bg-red-50 p-4" role="alert" aria-live="polite">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-circle-exclamation mt-0.5 text-sm text-red-500" aria-hidden="true"></i>
                        <div>
                            @foreach($errors->all() as $error)
                                <p class="text-sm text-red-700">{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" id="loginForm" class="relative" novalidate x-data="loginForm()">
                @csrf
                <x-auth.honeypot-field />

                <!-- Email -->
                <div class="mb-5 relative">
                    <label for="email" class="sr-only">Alamat Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autocomplete="email"
                        autofocus
                        placeholder=" "
                        class="input-field input-float w-full rounded-2xl border-2 border-brand-100 bg-white px-5 py-3.5 text-sm text-neutral-900 placeholder-transparent outline-none @error('email') error @enderror"
                        :class="{ 'error': emailError }"
                        @blur="validateEmail"
                        @input="clearEmailError"
                        aria-describedby="email-error"
                        aria-invalid="emailError ? 'true' : 'false'"
                    >
                    <label for="email" class="floating-label absolute left-4 top-3.5 z-10 text-sm text-neutral-400">
                        Alamat Email
                    </label>
                    <p x-show="emailError" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" id="email-error" class="mt-1.5 flex items-center gap-1.5 text-xs text-red-500" role="alert">
                        <i class="fas fa-exclamation-circle" aria-hidden="true"></i>
                        <span x-text="emailError"></span>
                    </p>
                </div>

                <!-- Password -->
                <div class="mb-5 relative">
                    <label for="password" class="sr-only">Kata Sandi</label>
                    <input
                        :type="showPassword ? 'text' : 'password'"
                        id="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder=" "
                        class="input-field input-float w-full rounded-2xl border-2 border-brand-100 bg-white px-5 py-3.5 pr-12 text-sm text-neutral-900 placeholder-transparent outline-none @error('password') error @enderror"
                        :class="{ 'error': passwordError }"
                        @blur="validatePassword"
                        @input="clearPasswordError"
                        aria-describedby="password-error"
                        aria-invalid="passwordError ? 'true' : 'false'"
                    >
                    <label for="password" class="floating-label absolute left-4 top-3.5 z-10 text-sm text-neutral-400">
                        Kata Sandi
                    </label>
                    <button type="button" @click="showPassword = !showPassword" class="password-toggle absolute right-4 top-1/2 -translate-y-1/2 text-neutral-400" :aria-label="showPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'" aria-live="polite">
                        <i class="fas" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'" aria-hidden="true"></i>
                    </button>
                    <p x-show="passwordError" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" id="password-error" class="mt-1.5 flex items-center gap-1.5 text-xs text-red-500" role="alert">
                        <i class="fas fa-exclamation-circle" aria-hidden="true"></i>
                        <span x-text="passwordError"></span>
                    </p>
                </div>

                <!-- Remember + Forgot -->
                <div class="mb-7 flex items-center justify-between">
                    <label class="flex items-center gap-2.5 cursor-pointer select-none">
                        <input type="checkbox" name="remember" id="remember" class="check-custom h-4.5 w-4.5 rounded border-2 border-brand-200 text-brand-500 outline-none focus:ring-2 focus:ring-brand-200 focus:ring-offset-2" {{ old('remember') ? 'checked' : '' }}>
                        <span class="text-sm text-neutral-600">Ingat saya</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="text-sm font-semibold text-brand-600 transition hover:text-brand-800">
                        Lupa sandi?
                    </a>
                </div>

                <div class="mb-6">
                    <x-auth.captcha-field :captcha="$captcha" />
                </div>

                <!-- Submit -->
                <button type="submit" class="btn-login relative w-full rounded-2xl py-3.5 text-sm font-bold text-white shadow-lg shadow-brand-500/20" :class="{ 'loading': loading }" :disabled="loading">
                    <span class="flex items-center justify-center gap-2">
                        <i class="fas fa-sign-in-alt" aria-hidden="true"></i>
                        Masuk
                    </span>
                    <div class="spinner" aria-hidden="true"></div>
                </button>

                <!-- Divider -->
                <div class="divider-line my-7 flex items-center gap-4">
                    <span class="divider-text text-xs font-semibold uppercase tracking-widest text-neutral-400">atau</span>
                </div>

                <!-- Social login -->
                <div>
                    <a href="{{ route('socialite.redirect', 'google') }}" class="flex w-full items-center justify-center gap-3 rounded-2xl border-2 border-brand-100 bg-white py-3.5 text-sm font-semibold text-neutral-700 transition hover:border-brand-200 hover:bg-brand-50">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" aria-hidden="true"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                        Masuk dengan Google
                    </a>
                </div>
            </form>

            <!-- Register link -->
            <p class="mt-8 text-center text-sm text-neutral-500">
                Belum punya akun?
                <a href="{{ $registerReturnUrl ?? route('register') }}" class="font-bold text-brand-600 transition hover:text-brand-800 hover:underline underline-offset-4">
                    Daftar sekarang
                </a>
            </p>

            <!-- Back to home -->
            <div class="mt-6 text-center">
                <a href="{{ route('home') }}" class="back-link inline-flex items-center gap-1.5 text-sm text-neutral-400 hover:text-brand-600">
                    <i class="fas fa-arrow-left text-xs" aria-hidden="true"></i>
                    Kembali ke beranda
                </a>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function loginForm() {
    return {
        loading: false,
        showPassword: false,
        emailError: '',
        passwordError: '',

        init() {
            const form = document.getElementById('loginForm');
            form.addEventListener('submit', (e) => {
                if (this.emailError || this.passwordError) {
                    e.preventDefault();
                    this.validateEmail();
                    this.validatePassword();
                    return;
                }
                const btn = form.querySelector('button[type=submit]');
                btn.classList.add('loading');
                this.loading = true;
            });
        },

        validateEmail() {
            const email = document.getElementById('email').value;
            if (!email) {
                this.emailError = 'Email wajib diisi.';
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                this.emailError = 'Format email tidak valid.';
            } else {
                this.emailError = '';
            }
        },

        clearEmailError() {
            this.emailError = '';
        },

        validatePassword() {
            const pw = document.getElementById('password').value;
            if (!pw) {
                this.passwordError = 'Kata sandi wajib diisi.';
            } else if (pw.length < 6) {
                this.passwordError = 'Kata sandi minimal 6 karakter.';
            } else {
                this.passwordError = '';
            }
        },

        clearPasswordError() {
            this.passwordError = '';
        },
    }
}
</script>
@endpush
