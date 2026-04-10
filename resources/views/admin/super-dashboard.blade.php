@extends('layouts.admin')

@section('title', 'Super Admin — Skinbae.ID')
@section('page_title', 'Dashboard Super Admin')

@section('content')
<div class="space-y-6">

    <div>
        <h2 class="text-xl font-bold tracking-tight text-gray-900">Ringkasan sistem</h2>
        <p class="mt-0.5 text-sm text-gray-400">Pengawasan pengguna, analitik, dan jejak aktivitas</p>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach([
            ['key' => 'super_admin', 'label' => 'Super Admin', 'icon' => 'fa-shield-alt', 'wrap' => 'bg-violet-50 text-violet-600'],
            ['key' => 'admin', 'label' => 'Admin', 'icon' => 'fa-user-tie', 'wrap' => 'bg-blue-50 text-blue-600'],
            ['key' => 'customer', 'label' => 'Pelanggan', 'icon' => 'fa-user', 'wrap' => 'bg-emerald-50 text-emerald-600'],
        ] as $card)
            @php $count = (int) ($roleCounts[$card['key']] ?? 0); @endphp
            <div class="rounded-2xl bg-white p-5 shadow-card ring-1 ring-stone-100">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">{{ $card['label'] }}</span>
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl {{ $card['wrap'] }}">
                        <i class="fas {{ $card['icon'] }} text-sm"></i>
                    </span>
                </div>
                <p class="mt-3 text-3xl font-bold tabular-nums text-gray-900">{{ number_format($count) }}</p>
            </div>
        @endforeach
        <div class="rounded-2xl bg-white p-5 shadow-card ring-1 ring-stone-100">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">Total pengguna</span>
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-stone-100 text-stone-600">
                    <i class="fas fa-users text-sm"></i>
                </span>
            </div>
            <p class="mt-3 text-3xl font-bold tabular-nums text-gray-900">{{ number_format($roleCounts->sum()) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
        <a href="{{ route('admin.users.index') }}" class="group flex items-center gap-4 rounded-2xl bg-white p-5 shadow-card ring-1 ring-stone-100 transition hover:ring-brand-200">
            <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-50 text-brand-600 transition group-hover:bg-brand-100">
                <i class="fas fa-users text-lg"></i>
            </span>
            <div class="min-w-0 flex-1">
                <h3 class="text-sm font-bold text-gray-900">Kelola pengguna</h3>
                <p class="text-xs text-gray-400">CRUD role admin, super admin, dan pelanggan</p>
            </div>
            <i class="fas fa-chevron-right text-xs text-gray-300 group-hover:text-brand-500"></i>
        </a>
        <a href="{{ route('admin.activity-log.index') }}" class="group flex items-center gap-4 rounded-2xl bg-white p-5 shadow-card ring-1 ring-stone-100 transition hover:ring-brand-200">
            <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-50 text-amber-600 transition group-hover:bg-amber-100">
                <i class="fas fa-history text-lg"></i>
            </span>
            <div class="min-w-0 flex-1">
                <h3 class="text-sm font-bold text-gray-900">Activity log</h3>
                <p class="text-xs text-gray-400">Login staff dan perubahan data pengguna</p>
            </div>
            <i class="fas fa-chevron-right text-xs text-gray-300 group-hover:text-brand-500"></i>
        </a>
        <a href="{{ route('admin.analytics') }}" class="group flex items-center gap-4 rounded-2xl bg-white p-5 shadow-card ring-1 ring-stone-100 transition hover:ring-brand-200">
            <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600 transition group-hover:bg-blue-100">
                <i class="fas fa-chart-pie text-lg"></i>
            </span>
            <div class="min-w-0 flex-1">
                <h3 class="text-sm font-bold text-gray-900">Analitik</h3>
                <p class="text-xs text-gray-400">KPI, pendapatan, dan prediksi</p>
            </div>
            <i class="fas fa-chevron-right text-xs text-gray-300 group-hover:text-brand-500"></i>
        </a>
        <a href="{{ route('admin.analytics.trend') }}" class="group flex items-center gap-4 rounded-2xl bg-white p-5 shadow-card ring-1 ring-stone-100 transition hover:ring-brand-200">
            <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-violet-50 text-violet-600 transition group-hover:bg-violet-100">
                <i class="fas fa-chart-area text-lg"></i>
            </span>
            <div class="min-w-0 flex-1">
                <h3 class="text-sm font-bold text-gray-900">Analitik tren</h3>
                <p class="text-xs text-gray-400">Grafik tren &amp; prediksi (anchor halaman analitik)</p>
            </div>
            <i class="fas fa-chevron-right text-xs text-gray-300 group-hover:text-brand-500"></i>
        </a>
    </div>

    <div class="overflow-hidden rounded-2xl bg-white shadow-card ring-1 ring-stone-100">
        <div class="flex items-center justify-between border-b border-stone-100 px-5 py-4">
            <h3 class="text-sm font-bold text-gray-800">Aktivitas terbaru</h3>
            <a href="{{ route('admin.activity-log.index') }}" class="text-xs font-semibold text-brand-600 hover:text-brand-700">Lihat semua</a>
        </div>
        <ul class="divide-y divide-stone-50">
            @forelse($recentLogs as $log)
                <li class="flex flex-wrap items-start gap-3 px-5 py-3 text-sm">
                    <span class="inline-flex shrink-0 rounded-lg bg-stone-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-stone-600">{{ $log->action }}</span>
                    <div class="min-w-0 flex-1">
                        <p class="text-gray-800">{{ $log->description ?? '—' }}</p>
                        <p class="mt-0.5 text-xs text-gray-400">
                            {{ $log->actor?->email ?? 'Sistem' }}
                            &middot; {{ $log->created_at->format('d M Y, H:i') }}
                            @if($log->ip_address)
                                &middot; {{ $log->ip_address }}
                            @endif
                        </p>
                    </div>
                </li>
            @empty
                <li class="px-5 py-12 text-center text-sm text-gray-400">Belum ada aktivitas tercatat.</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
