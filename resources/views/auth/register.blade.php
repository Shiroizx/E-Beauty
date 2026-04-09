@extends('layouts.app')

@section('title', 'Register — Skinbae.ID')

@section('content')
<div class="mx-auto max-w-md px-4 py-12 sm:px-6">
    <div class="overflow-hidden rounded-3xl border border-brand-100 bg-white shadow-xl shadow-brand-200/40">
        <div class="border-b border-brand-50 px-6 pb-2 pt-8 text-center">
            <h1 class="text-2xl font-bold text-gradient-brand">Buat akun</h1>
            <p class="mt-2 text-sm text-neutral-600">Bergabung dengan Skinbae.ID</p>
        </div>
        <form method="POST" action="{{ route('register') }}" class="space-y-4 p-6">
            @csrf
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
                <label for="password" class="mb-1 block text-sm font-medium text-brand-800">Password</label>
                <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="••••••••" class="input-brand @error('password') border-red-300 ring-2 ring-red-100 @enderror">
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="password-confirm" class="mb-1 block text-sm font-medium text-brand-800">Konfirmasi password</label>
                <input id="password-confirm" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" class="input-brand">
            </div>
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
