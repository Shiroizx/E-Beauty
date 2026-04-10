@extends('layouts.admin')

@section('title', 'Kelola Pesanan — Admin Skinbae.ID')
@section('page_title', 'Pesanan')

@section('content')
<div class="space-y-5" x-data="orderManager()">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-bold tracking-tight text-gray-900">Daftar Pesanan</h2>
            <p class="mt-0.5 text-sm text-gray-400">Kelola dan pantau status pesanan</p>
        </div>
        <div class="flex items-center gap-2">
            {{-- Bulk Print Dropdown --}}
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" :disabled="selectedCount === 0"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-stone-200 bg-white px-3.5 py-2 text-xs font-semibold text-gray-600 shadow-sm transition hover:bg-stone-50 disabled:opacity-40 disabled:cursor-not-allowed">
                    <i class="fas fa-print text-[10px]"></i>
                    Cetak Massal
                    <template x-if="selectedCount > 0"><span class="ml-0.5 rounded-full bg-brand-500 px-1.5 py-0.5 text-[10px] font-bold text-white" x-text="selectedCount"></span></template>
                </button>
                <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 z-10 mt-1 w-44 rounded-xl bg-white py-1 shadow-lg ring-1 ring-stone-100" x-cloak>
                    <button @click="submitBulkPrint('thermal'); open = false" class="flex w-full items-center gap-2 px-3 py-2 text-sm text-gray-700 transition hover:bg-stone-50">
                        <i class="fas fa-receipt text-gray-400 text-xs w-4"></i> Format Thermal
                    </button>
                    <button @click="submitBulkPrint('a4'); open = false" class="flex w-full items-center gap-2 px-3 py-2 text-sm text-gray-700 transition hover:bg-stone-50">
                        <i class="fas fa-file-alt text-gray-400 text-xs w-4"></i> Format A4
                    </button>
                </div>
            </div>
            <a href="{{ route('admin.orders.export') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-stone-200 bg-white px-3.5 py-2 text-xs font-semibold text-gray-600 shadow-sm transition hover:bg-stone-50" target="_blank">
                <i class="fas fa-file-csv text-[10px]"></i> Export CSV
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <div class="rounded-2xl bg-white p-4 shadow-card">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
            <div class="col-span-2 sm:col-span-1 lg:col-span-2">
                <label class="mb-1 block text-[11px] font-semibold uppercase tracking-wider text-gray-400">Pencarian</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="No. Pesanan / Customer"
                       class="w-full rounded-xl border border-stone-200 bg-stone-50/50 px-3.5 py-2 text-sm text-gray-700 placeholder-gray-300 transition focus:border-brand-300 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-100">
            </div>
            <div>
                <label class="mb-1 block text-[11px] font-semibold uppercase tracking-wider text-gray-400">Status</label>
                <select name="status" class="w-full rounded-xl border border-stone-200 bg-stone-50/50 px-3 py-2 text-sm text-gray-700 transition focus:border-brand-300 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-100">
                    <option value="">Semua</option>
                    @foreach($statuses as $key => $label)
                        <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-[11px] font-semibold uppercase tracking-wider text-gray-400">Pembayaran</label>
                <select name="payment_status" class="w-full rounded-xl border border-stone-200 bg-stone-50/50 px-3 py-2 text-sm text-gray-700 transition focus:border-brand-300 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-100">
                    <option value="">Semua</option>
                    @foreach($paymentStatuses as $key => $label)
                        <option value="{{ $key }}" {{ request('payment_status') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-[11px] font-semibold uppercase tracking-wider text-gray-400">Dari</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="w-full rounded-xl border border-stone-200 bg-stone-50/50 px-3 py-2 text-sm text-gray-700 transition focus:border-brand-300 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-100">
            </div>
            <div class="flex flex-col">
                <label class="mb-1 block text-[11px] font-semibold uppercase tracking-wider text-gray-400">Sampai</label>
                <div class="flex gap-2 flex-1">
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                           class="w-full rounded-xl border border-stone-200 bg-stone-50/50 px-3 py-2 text-sm text-gray-700 transition focus:border-brand-300 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-100">
                    <button type="submit" class="shrink-0 rounded-xl bg-gray-800 px-4 py-2 text-sm font-semibold text-white transition hover:bg-gray-700">
                        <i class="fas fa-search text-[10px]"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Orders Table --}}
    <div class="overflow-hidden rounded-2xl bg-white shadow-card">
        <form id="bulkPrintForm" action="{{ route('admin.orders.print_bulk') }}" method="POST" target="_blank">
            @csrf
            <input type="hidden" name="format" x-ref="printFormat" value="thermal">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-stone-100 text-left">
                            <th class="py-3 pl-5 pr-2 w-10">
                                <input type="checkbox" @change="toggleAll($event)" class="h-3.5 w-3.5 rounded border-stone-300 text-brand-500 focus:ring-brand-200">
                            </th>
                            <th class="px-3 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-400">No. Pesanan</th>
                            <th class="px-3 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-400">Customer</th>
                            <th class="px-3 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-400">Tanggal</th>
                            <th class="px-3 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-400">Status</th>
                            <th class="px-3 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-400">Pembayaran</th>
                            <th class="px-3 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-gray-400">Total</th>
                            <th class="py-3 pl-3 pr-5 text-center text-[11px] font-semibold uppercase tracking-wider text-gray-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-50">
                        @forelse($orders as $order)
                            <tr class="transition hover:bg-stone-50/60">
                                <td class="py-3 pl-5 pr-2">
                                    <input type="checkbox" name="order_ids[]" value="{{ $order->id }}" class="order-cb h-3.5 w-3.5 rounded border-stone-300 text-brand-500 focus:ring-brand-200" @change="updateCount()">
                                </td>
                                <td class="px-3 py-3">
                                    <p class="font-semibold text-gray-800">{{ $order->order_number }}</p>
                                    <p class="text-[11px] text-gray-400">{{ $order->items_count }} item(s)</p>
                                </td>
                                <td class="px-3 py-3">
                                    <p class="font-medium text-gray-700">{{ $order->user->name ?? 'Guest' }}</p>
                                    <p class="text-[11px] text-gray-400">{{ $order->user->email ?? '' }}</p>
                                </td>
                                <td class="px-3 py-3 text-xs text-gray-500">
                                    {{ $order->created_at->format('d M Y') }}<br>
                                    <span class="text-gray-400">{{ $order->created_at->format('H:i') }}</span>
                                </td>
                                <td class="px-3 py-3">
                                    @php
                                        $statusStyle = match($order->status) {
                                            'pending_payment' => 'bg-amber-50 text-amber-700 ring-amber-200/60',
                                            'processing' => 'bg-sky-50 text-sky-700 ring-sky-200/60',
                                            'confirmed' => 'bg-blue-50 text-blue-700 ring-blue-200/60',
                                            'shipped' => 'bg-violet-50 text-violet-700 ring-violet-200/60',
                                            'completed' => 'bg-emerald-50 text-emerald-700 ring-emerald-200/60',
                                            'cancelled' => 'bg-red-50 text-red-600 ring-red-200/60',
                                            default => 'bg-gray-50 text-gray-600 ring-gray-200/60'
                                        };
                                    @endphp
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-semibold ring-1 ring-inset {{ $statusStyle }}">{{ $order->statusLabel() }}</span>
                                </td>
                                <td class="px-3 py-3">
                                    @php
                                        $payColor = match($order->payment_status) {
                                            'paid' => 'text-emerald-600',
                                            'pending' => 'text-amber-600',
                                            'failed', 'expired' => 'text-red-600',
                                            default => 'text-gray-400'
                                        };
                                        $payDot = match($order->payment_status) {
                                            'paid' => 'bg-emerald-500',
                                            'pending' => 'bg-amber-500',
                                            'failed', 'expired' => 'bg-red-500',
                                            default => 'bg-gray-400'
                                        };
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold {{ $payColor }}">
                                        <span class="h-1.5 w-1.5 rounded-full {{ $payDot }}"></span>
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 text-right font-bold text-gray-800">{{ $order->formatted_total }}</td>
                                <td class="py-3 pl-3 pr-5 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 transition hover:bg-blue-50 hover:text-blue-500" title="Detail">
                                            <i class="fas fa-eye text-xs"></i>
                                        </a>
                                        <a href="{{ route('admin.orders.edit', $order) }}" class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 transition hover:bg-amber-50 hover:text-amber-500" title="Edit">
                                            <i class="fas fa-pen text-xs"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-16 text-center">
                                    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-stone-100">
                                        <i class="fas fa-inbox text-stone-300 text-lg"></i>
                                    </div>
                                    <p class="text-sm text-gray-400">Belum ada data pesanan yang sesuai filter</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>

        @if($orders->hasPages())
            <div class="border-t border-stone-100 px-5 py-3">
                {{ $orders->links('pagination::tailwind') }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    function orderManager() {
        return {
            selectedCount: 0,
            updateCount() {
                this.selectedCount = document.querySelectorAll('.order-cb:checked').length;
            },
            toggleAll(e) {
                document.querySelectorAll('.order-cb').forEach(cb => cb.checked = e.target.checked);
                this.updateCount();
            },
            submitBulkPrint(format) {
                this.$refs.printFormat.value = format;
                document.getElementById('bulkPrintForm').submit();
            }
        }
    }
</script>
@endpush
