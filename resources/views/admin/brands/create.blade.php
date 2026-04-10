@extends('layouts.admin')

@section('title', 'Tambah Brand — Admin Skinbae.ID')
@section('page_title', 'Tambah Brand')

@section('content')
<div class="mx-auto max-w-lg space-y-5">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold tracking-tight text-gray-900">Tambah Brand Baru</h2>
            <p class="mt-0.5 text-sm text-gray-400">Buat brand produk baru</p>
        </div>
        <a href="{{ route('admin.brands.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-stone-200 bg-white px-4 py-2 text-sm font-semibold text-gray-600 shadow-sm transition hover:bg-stone-50">
            <i class="fas fa-arrow-left text-[10px]"></i> Kembali
        </a>
    </div>

    <div class="rounded-2xl bg-white p-5 shadow-card">
        <form action="{{ route('admin.brands.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div>
                <label class="mb-1.5 block text-xs font-semibold text-gray-600">Nama Brand <span class="text-red-400">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full rounded-xl border border-stone-200 px-3.5 py-2.5 text-sm text-gray-700 transition focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-100">
            </div>

            <div>
                <label class="mb-1.5 block text-xs font-semibold text-gray-600">Logo</label>
                <input type="file" name="logo" accept="image/*"
                       class="w-full rounded-xl border border-stone-200 bg-stone-50/50 px-3 py-2 text-sm text-gray-500 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-1 file:text-xs file:font-semibold file:text-brand-600 hover:file:bg-brand-100">
            </div>

            <div>
                <label class="mb-1.5 block text-xs font-semibold text-gray-600">Deskripsi</label>
                <textarea name="description" rows="4" class="w-full rounded-xl border border-stone-200 px-3.5 py-2.5 text-sm text-gray-700 transition focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-100">{{ old('description') }}</textarea>
            </div>

            <label class="flex cursor-pointer items-center justify-between rounded-xl border border-stone-100 p-3 transition hover:border-stone-200 hover:bg-stone-50/50">
                <span class="text-sm font-medium text-gray-700">Aktif / Tampilkan</span>
                <input type="checkbox" name="is_active" value="1" checked
                       class="relative h-5 w-9 cursor-pointer appearance-none rounded-full bg-gray-200 transition checked:bg-emerald-500 after:absolute after:left-0.5 after:top-0.5 after:h-4 after:w-4 after:rounded-full after:bg-white after:shadow after:transition checked:after:translate-x-4">
            </label>

            <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-brand-500 to-brand-600 py-2.5 text-sm font-bold text-white shadow-md shadow-brand-500/20 transition hover:shadow-lg hover:shadow-brand-500/25">
                <i class="fas fa-save mr-2 text-xs"></i> Simpan Brand
            </button>
        </form>
    </div>
</div>
@endsection
