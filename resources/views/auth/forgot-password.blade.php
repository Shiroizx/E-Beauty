@extends('layouts.app')

@section('title', 'Reset Password — Skinbae.ID')

@section('content')
<div class="mx-auto flex min-h-[70vh] max-w-7xl items-center justify-center px-4 py-10 sm:px-6 lg:px-8">
    <div class="w-full max-w-md rounded-3xl border border-brand-100 bg-white p-7 shadow-xl shadow-brand-200/30">
        <h1 class="text-2xl font-black tracking-tight text-neutral-900">Lupa Password</h1>
        <p class="mt-2 text-sm text-neutral-500">Masukkan email akun Anda, kami akan kirimkan tautan reset password.</p>

        <form action="{{ route('password.email') }}" method="POST" class="mt-6 space-y-4">
            @csrf
            <div>
                <label for="email" class="mb-1 block text-sm font-semibold text-neutral-700">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required class="w-full rounded-2xl border border-brand-200 px-4 py-3 text-sm outline-none transition focus:border-brand-400 focus:ring-4 focus:ring-brand-100">
                @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="w-full rounded-2xl bg-gradient-to-r from-brand-500 to-brand-600 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-brand-500/25 transition hover:translate-y-[-1px]">
                Kirim Link Reset
            </button>
        </form>

        <a href="{{ route('login') }}" class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-brand-600 hover:text-brand-700">
            <i class="fas fa-arrow-left" aria-hidden="true"></i>
            Kembali ke login
        </a>
    </div>
</div>
@endsection
