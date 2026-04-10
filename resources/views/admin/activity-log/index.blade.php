@extends('layouts.admin')

@section('title', 'Activity Log — Super Admin')
@section('page_title', 'Activity log')

@section('content')
<div class="space-y-5">

    <div>
        <h2 class="text-xl font-bold tracking-tight text-gray-900">Log aktivitas</h2>
        <p class="mt-0.5 text-sm text-gray-400">Login staff dan perubahan data pengguna</p>
    </div>

    <div class="overflow-hidden rounded-2xl bg-white shadow-card">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-stone-100 text-left">
                        <th class="py-3 pl-5 pr-3 text-[11px] font-semibold uppercase tracking-wider text-gray-400">Waktu</th>
                        <th class="px-3 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-400">Aksi</th>
                        <th class="px-3 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-400">Keterangan</th>
                        <th class="px-3 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-400">Aktor</th>
                        <th class="py-3 pl-3 pr-5 text-[11px] font-semibold uppercase tracking-wider text-gray-400">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-50">
                    @forelse($logs as $log)
                        <tr class="transition hover:bg-stone-50/60">
                            <td class="whitespace-nowrap py-3 pl-5 pr-3 text-gray-500">{{ $log->created_at->format('d M Y H:i:s') }}</td>
                            <td class="px-3 py-3">
                                <span class="inline-flex rounded-lg bg-stone-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-stone-600">{{ $log->action }}</span>
                            </td>
                            <td class="max-w-md px-3 py-3 text-gray-700">{{ $log->description ?? '—' }}</td>
                            <td class="px-3 py-3 text-gray-600">{{ $log->actor?->email ?? '—' }}</td>
                            <td class="py-3 pl-3 pr-5 text-gray-500">{{ $log->ip_address ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-16 text-center text-sm text-gray-400">Belum ada log.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="border-t border-stone-100 px-5 py-3">
                {{ $logs->links('pagination::tailwind') }}
            </div>
        @endif
    </div>
</div>
@endsection
