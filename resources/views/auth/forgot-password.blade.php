@extends('layouts.app')

@section('title', 'Reset Password — Skinbae.ID')

@section('content')
<div class="mx-auto flex min-h-[70vh] max-w-7xl items-center justify-center px-4 py-10 sm:px-6 lg:px-8">
    <div class="w-full max-w-md rounded-3xl border border-brand-100 bg-white p-7 shadow-xl shadow-brand-200/30">
        <div class="mb-2 flex items-center gap-3">
            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-brand-50 text-brand-600">
                <i class="fas fa-envelope-circle-check text-lg" aria-hidden="true"></i>
            </div>
            <div>
                <h1 class="text-2xl font-black tracking-tight text-neutral-900">Lupa kata sandi</h1>
                <p class="text-xs font-medium text-emerald-700">Reset aman dengan tautan sekali pakai</p>
            </div>
        </div>
        <p class="mt-2 text-sm text-neutral-500">Masukkan email akun Anda. Kami akan mengirim tautan reset jika email terdaftar.</p>

        <div class="mt-5">
            <x-auth.reset-steps />
        </div>

        <form action="{{ route('password.email') }}" method="POST" class="relative mt-6 space-y-4">
            @csrf
            <x-auth.honeypot-field />

            <div>
                <label for="email" class="mb-1 block text-sm font-semibold text-neutral-700">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email" class="w-full rounded-2xl border border-brand-200 px-4 py-3 text-sm outline-none transition focus:border-brand-400 focus:ring-4 focus:ring-brand-100">
                @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <x-auth.captcha-field :captcha="$captcha" />

            <button type="submit" class="w-full rounded-2xl bg-gradient-to-r from-brand-500 to-brand-600 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-brand-500/25 transition hover:translate-y-[-1px]">
                <i class="fas fa-paper-plane me-2" aria-hidden="true"></i>
                Kirim tautan reset
            </button>
        </form>

        @if(session('success'))
            <div class="mt-4 rounded-2xl border border-emerald-100 bg-emerald-50 p-4 text-sm text-emerald-800" role="status">
                <p class="font-semibold"><i class="fas fa-circle-check me-2" aria-hidden="true"></i>{{ session('success') }}</p>
                <p class="mt-2 text-xs text-emerald-700/90">Jika tidak ada email, periksa folder spam atau pastikan alamat benar. Demi keamanan, kami menampilkan pesan yang sama untuk semua permintaan.</p>
            </div>
        @endif

        <a href="{{ route('login') }}" class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-brand-600 hover:text-brand-700">
            <i class="fas fa-arrow-left" aria-hidden="true"></i>
            Kembali ke login
        </a>
    </div>
</div>
@endsection
