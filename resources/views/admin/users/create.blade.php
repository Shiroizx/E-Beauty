@extends('layouts.admin')

@section('title', 'Tambah Pengguna — Super Admin')
@section('page_title', 'Tambah pengguna')

@section('content')
<div class="mx-auto max-w-xl space-y-5">
    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-gray-500 hover:text-brand-600">
        <i class="fas fa-arrow-left text-[10px]"></i> Kembali
    </a>

    <div class="rounded-2xl bg-white p-6 shadow-card ring-1 ring-stone-100">
        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="mb-1 block text-xs font-semibold text-gray-600">Nama</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="w-full rounded-xl border border-stone-200 px-3 py-2 text-sm focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-100">
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold text-gray-600">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="w-full rounded-xl border border-stone-200 px-3 py-2 text-sm focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-100">
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold text-gray-600">Password</label>
                <input type="password" name="password" required class="w-full rounded-xl border border-stone-200 px-3 py-2 text-sm focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-100">
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold text-gray-600">Konfirmasi password</label>
                <input type="password" name="password_confirmation" required class="w-full rounded-xl border border-stone-200 px-3 py-2 text-sm focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-100">
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold text-gray-600">Role</label>
                <select name="role" class="w-full rounded-xl border border-stone-200 px-3 py-2 text-sm focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-100">
                    <option value="customer" @selected(old('role') === 'customer')>Pelanggan</option>
                    <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                    <option value="super_admin" @selected(old('role') === 'super_admin')>Super Admin</option>
                </select>
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold text-gray-600">Telepon (opsional)</label>
                <input type="text" name="phone" value="{{ old('phone') }}" class="w-full rounded-xl border border-stone-200 px-3 py-2 text-sm focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-100">
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold text-gray-600">Alamat (opsional)</label>
                <textarea name="address" rows="2" class="w-full rounded-xl border border-stone-200 px-3 py-2 text-sm focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-100">{{ old('address') }}</textarea>
            </div>

            <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-brand-500 to-brand-600 py-2.5 text-sm font-bold text-white shadow-md shadow-brand-500/20 transition hover:shadow-lg">
                Simpan
            </button>
        </form>
    </div>
</div>
@endsection
