@extends('layouts.admin')

@section('title', 'Manajemen Brand — Admin Skinbae.ID')
@section('page_title', 'Brand')

@section('content')
<div class="space-y-5">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-bold tracking-tight text-gray-900">Manajemen Brand</h2>
            <p class="mt-0.5 text-sm text-gray-400">Kelola brand produk skincare</p>
        </div>
        <a href="{{ route('admin.brands.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-brand-500 to-brand-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-brand-500/20 transition hover:shadow-lg hover:shadow-brand-500/25">
            <i class="fas fa-plus text-xs"></i> Tambah Brand
        </a>
    </div>

    <div class="overflow-hidden rounded-2xl bg-white shadow-card">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-stone-100 text-left">
                        <th class="py-3 pl-5 pr-3 text-[11px] font-semibold uppercase tracking-wider text-gray-400">Brand</th>
                        <th class="px-3 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-400">Deskripsi</th>
                        <th class="px-3 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-400">Produk</th>
                        <th class="px-3 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-400">Status</th>
                        <th class="py-3 pl-3 pr-5 text-right text-[11px] font-semibold uppercase tracking-wider text-gray-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-50">
                    @forelse($brands as $brand)
                        <tr class="transition hover:bg-stone-50/60">
                            <td class="py-3 pl-5 pr-3">
                                <div class="flex items-center gap-3">
                                    @if($brand->logo)
                                        <img src="{{ Storage::url($brand->logo) }}" alt="" class="h-9 w-9 rounded-full object-cover ring-1 ring-stone-100">
                                    @else
                                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-stone-100 text-stone-400">
                                            <i class="fas fa-tag text-xs"></i>
                                        </div>
                                    @endif
                                    <span class="font-semibold text-gray-800">{{ $brand->name }}</span>
                                </div>
                            </td>
                            <td class="px-3 py-3 text-gray-500 max-w-[200px] truncate">{{ Str::limit($brand->description, 50) }}</td>
                            <td class="px-3 py-3">
                                <span class="inline-flex items-center rounded-full bg-stone-100 px-2 py-0.5 text-xs font-semibold text-gray-600">{{ $brand->products_count }} Produk</span>
                            </td>
                            <td class="px-3 py-3">
                                @if($brand->is_active)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-200/60">
                                        <span class="h-1 w-1 rounded-full bg-emerald-500"></span> Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-gray-50 px-2 py-0.5 text-xs font-semibold text-gray-500 ring-1 ring-inset ring-gray-200/60">
                                        <span class="h-1 w-1 rounded-full bg-gray-400"></span> Non-Aktif
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 pl-3 pr-5">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.brands.edit', $brand->id) }}" class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 transition hover:bg-blue-50 hover:text-blue-500" title="Edit">
                                        <i class="fas fa-pen text-xs"></i>
                                    </a>
                                    <form action="{{ route('admin.brands.destroy', $brand->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus brand ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 transition hover:bg-red-50 hover:text-red-500" title="Hapus">
                                            <i class="fas fa-trash-alt text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-16 text-center">
                                <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-stone-100">
                                    <i class="fas fa-tag text-stone-300 text-lg"></i>
                                </div>
                                <p class="text-sm text-gray-400">Belum ada brand</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($brands->hasPages())
            <div class="border-t border-stone-100 px-5 py-3">
                {{ $brands->links('pagination::tailwind') }}
            </div>
        @endif
    </div>
</div>
@endsection
