@extends('layouts.admin')

@section('title', 'Manajemen Promo — Admin Skinbae.ID')
@section('page_title', 'Promo')

@section('content')
<div class="space-y-5">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-bold tracking-tight text-gray-900">Manajemen Promo</h2>
            <p class="mt-0.5 text-sm text-gray-400">Kelola kode promo dan diskon</p>
        </div>
        <a href="{{ route('admin.promos.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-brand-500 to-brand-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-brand-500/20 transition hover:shadow-lg hover:shadow-brand-500/25">
            <i class="fas fa-plus text-xs"></i> Buat Promo Baru
        </a>
    </div>

    <div class="overflow-hidden rounded-2xl bg-white shadow-card">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-stone-100 text-left">
                        <th class="py-3 pl-5 pr-3 text-[11px] font-semibold uppercase tracking-wider text-gray-400">Nama Promo</th>
                        <th class="px-3 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-400">Kode</th>
                        <th class="px-3 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-400">Diskon</th>
                        <th class="px-3 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-400">Periode</th>
                        <th class="px-3 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-400">Usage</th>
                        <th class="px-3 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-400">Status</th>
                        <th class="py-3 pl-3 pr-5 text-right text-[11px] font-semibold uppercase tracking-wider text-gray-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-50">
                    @forelse($promos as $promo)
                        <tr class="transition hover:bg-stone-50/60">
                            <td class="py-3 pl-5 pr-3">
                                <p class="font-semibold text-gray-800">{{ $promo->name }}</p>
                                <p class="text-[11px] text-gray-400">{{ Str::limit($promo->description, 30) }}</p>
                            </td>
                            <td class="px-3 py-3">
                                <span class="inline-flex rounded-lg bg-gray-900 px-2.5 py-1 font-mono text-[11px] font-bold tracking-wider text-gray-100">{{ $promo->code }}</span>
                            </td>
                            <td class="px-3 py-3">
                                @if($promo->discount_type === 'percentage')
                                    <span class="font-bold text-emerald-600">{{ $promo->discount_value }}%</span>
                                @else
                                    <span class="font-bold text-emerald-600">Rp {{ number_format($promo->discount_value, 0, ',', '.') }}</span>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-xs text-gray-500">
                                {{ $promo->start_date->format('d M') }} — {{ $promo->end_date->format('d M Y') }}
                            </td>
                            <td class="px-3 py-3 text-gray-600">
                                {{ $promo->used_count }}@if($promo->usage_limit) / {{ $promo->usage_limit }}@endif
                            </td>
                            <td class="px-3 py-3">
                                @if($promo->is_active && $promo->end_date->isFuture())
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-200/60">
                                        <span class="h-1 w-1 rounded-full bg-emerald-500"></span> Aktif
                                    </span>
                                @elseif(!$promo->is_active)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-gray-50 px-2 py-0.5 text-xs font-semibold text-gray-500 ring-1 ring-inset ring-gray-200/60">
                                        <span class="h-1 w-1 rounded-full bg-gray-400"></span> Non-Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-stone-100 px-2 py-0.5 text-xs font-semibold text-stone-600 ring-1 ring-inset ring-stone-200/60">
                                        Expired
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 pl-3 pr-5">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.promos.edit', $promo->id) }}" class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 transition hover:bg-blue-50 hover:text-blue-500">
                                        <i class="fas fa-pen text-xs"></i>
                                    </a>
                                    <form action="{{ route('admin.promos.destroy', $promo->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus promo ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 transition hover:bg-red-50 hover:text-red-500">
                                            <i class="fas fa-trash-alt text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-16 text-center">
                                <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-stone-100">
                                    <i class="fas fa-percent text-stone-300 text-lg"></i>
                                </div>
                                <p class="text-sm text-gray-400">Belum ada promo</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($promos->hasPages())
            <div class="border-t border-stone-100 px-5 py-3">
                {{ $promos->links('pagination::tailwind') }}
            </div>
        @endif
    </div>
</div>
@endsection
