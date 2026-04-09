@extends('layouts.app')

@section('title', 'Profil Saya — Skinbae.ID')

@section('content')
<div class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-2xl font-black tracking-tight text-neutral-900 sm:text-3xl">Profil Saya</h1>
        <p class="mt-1 text-sm text-neutral-500">Kelola data akun dan keamanan Anda di satu tempat.</p>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-3xl border border-brand-100 bg-gradient-to-br from-brand-50 to-white p-6 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-500 text-white shadow-lg shadow-brand-500/30">
                    <i class="fas fa-user text-lg" aria-hidden="true"></i>
                </div>
                <div>
                    <p class="text-lg font-bold text-neutral-900">{{ $user->name }}</p>
                    <p class="text-sm text-neutral-500">{{ $user->email }}</p>
                </div>
            </div>

            <div class="mt-6 space-y-3 text-sm">
                <a href="{{ route('password.request') }}" class="flex items-center gap-2 rounded-xl border border-brand-100 bg-white px-4 py-3 font-semibold text-brand-700 transition hover:border-brand-200 hover:bg-brand-50">
                    <i class="fas fa-key text-brand-500" aria-hidden="true"></i>
                    Reset Password
                </a>
                <a href="{{ route('orders.index') }}" class="flex items-center gap-2 rounded-xl border border-brand-100 bg-white px-4 py-3 font-semibold text-brand-700 transition hover:border-brand-200 hover:bg-brand-50">
                    <i class="fas fa-receipt text-brand-500" aria-hidden="true"></i>
                    Riwayat Pesanan
                </a>
            </div>
        </div>

        <div class="rounded-3xl border border-brand-100 bg-white p-6 shadow-sm lg:col-span-2">
            <h2 class="mb-5 text-lg font-bold text-neutral-900">Edit Profil</h2>

            <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="name" class="mb-1 block text-sm font-semibold text-neutral-700">Nama Lengkap</label>
                        <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required class="w-full rounded-2xl border border-brand-200 px-4 py-3 text-sm outline-none transition focus:border-brand-400 focus:ring-4 focus:ring-brand-100">
                        @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="email" class="mb-1 block text-sm font-semibold text-neutral-700">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required class="w-full rounded-2xl border border-brand-200 px-4 py-3 text-sm outline-none transition focus:border-brand-400 focus:ring-4 focus:ring-brand-100">
                        @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="phone" class="mb-1 block text-sm font-semibold text-neutral-700">No. HP</label>
                        <input id="phone" name="phone" type="text" value="{{ old('phone', $user->phone) }}" class="w-full rounded-2xl border border-brand-200 px-4 py-3 text-sm outline-none transition focus:border-brand-400 focus:ring-4 focus:ring-brand-100">
                        @error('phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="address" class="mb-1 block text-sm font-semibold text-neutral-700">Alamat</label>
                        <input id="address" name="address" type="text" value="{{ old('address', $user->address) }}" class="w-full rounded-2xl border border-brand-200 px-4 py-3 text-sm outline-none transition focus:border-brand-400 focus:ring-4 focus:ring-brand-100">
                        @error('address') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-brand-500 to-brand-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-brand-500/25 transition hover:translate-y-[-1px] hover:shadow-brand-500/40">
                        <i class="fas fa-save" aria-hidden="true"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
