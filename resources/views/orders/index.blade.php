@extends('layouts.app')

@section('title', 'Riwayat Pesanan — Skinbae.ID')

@push('styles')
<style>
    .orders-status-dot {
        width: 8px; height: 8px; border-radius: 9999px; display: inline-block;
    }
    .orders-table { border-collapse: separate; border-spacing: 0; }
    .orders-table thead th {
        background: linear-gradient(135deg, #fff5f9 0%, #ffe8f2 100%);
        font-size: 0.7rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase;
        color: #b82d5c; white-space: nowrap;
    }
    .orders-table thead th:first-child { border-radius: 1rem 0 0 0; }
    .orders-table thead th:last-child { border-radius: 0 1rem 0 0; }
    .orders-table tbody tr { transition: all 0.2s ease; }
    .orders-table tbody tr:hover { background-color: #fff5f9; }
    .orders-card { transition: all 0.25s cubic-bezier(0.22, 1, 0.36, 1); }
    .orders-card:hover { transform: translateY(-2px); box-shadow: 0 12px 40px -8px rgba(244, 81, 140, 0.18); }
    .orders-toast {
        animation: toastSlideIn 0.4s cubic-bezier(0.22, 1, 0.36, 1) both;
    }
    @keyframes toastSlideIn {
        from { opacity: 0; transform: translateX(100%); }
        to { opacity: 1; transform: translateX(0); }
    }
    .orders-toast-exit {
        animation: toastSlideOut 0.3s ease-in both;
    }
    @keyframes toastSlideOut {
        from { opacity: 1; transform: translateX(0); }
        to { opacity: 0; transform: translateX(100%); }
    }
    .skeleton-line {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite;
        border-radius: 0.5rem;
    }
    @keyframes shimmer { 0% { background-position: 200% center; } 100% { background-position: -200% center; } }
    .filter-bar { backdrop-filter: blur(10px); }
</style>
@endpush

@section('content')
<div
    x-data="ordersPage()"
    x-init="init()"
    class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8"
>
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-brand-900 sm:text-4xl">Riwayat Pesanan</h1>
            <p class="mt-1.5 text-sm text-neutral-500 sm:text-base">Pantau semua pesanan Anda di satu tempat</p>
        </div>
        <div class="flex shrink-0 gap-3">
            <a
                href="{{ route('orders.export', ['format' => 'csv']) }}"
                class="btn-brand-outline inline-flex items-center gap-2 text-sm"
                title="Unduh data pesanan dalam format CSV"
            >
                <i class="fas fa-file-csv text-brand-500" aria-hidden="true"></i>
                <span class="hidden sm:inline">Export CSV</span>
            </a>
            <button
                @click="refresh()"
                class="btn-brand-outline inline-flex items-center gap-2 text-sm"
                :disabled="loading"
                title="Refresh data"
            >
                <i class="fas fa-sync-alt" :class="{ 'fa-spin': loading }" aria-hidden="true"></i>
                <span class="hidden sm:inline">Refresh</span>
            </button>
        </div>
    </div>

    <div class="mb-6 rounded-2xl border border-brand-100/80 bg-white/80 p-4 shadow-sm shadow-brand-200/20 filter-bar sm:p-5">
        <form method="GET" action="{{ route('orders.index') }}" x-ref="filterForm" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
            <div class="relative lg:col-span-2">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-neutral-400" aria-hidden="true"></i>
                <input
                    type="text"
                    name="q"
                    x-model="searchLocal"
                    @input.debounce.300ms="applySearch()"
                    placeholder="Cari nomor pesanan..."
                    class="input-brand pl-10"
                    value="{{ $search }}"
                >
            </div>
            <div>
                <select
                    name="status"
                    @change="$refs.filterForm.submit()"
                    class="input-brand"
                >
                    <option value="">Semua Status</option>
                    @foreach($allStatuses as $key => $label)
                        <option value="{{ $key }}" {{ $status == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <input
                    type="date"
                    name="from"
                    x-model="dateFromLocal"
                    @change="$refs.filterForm.submit()"
                    class="input-brand"
                    placeholder="Dari tanggal"
                    value="{{ $dateFrom }}"
                >
            </div>
            <div>
                <input
                    type="date"
                    name="to"
                    x-model="dateToLocal"
                    @change="$refs.filterForm.submit()"
                    class="input-brand"
                    placeholder="Sampai tanggal"
                    value="{{ $dateTo }}"
                >
            </div>
            <input type="hidden" name="sort" :value="sortBy">
            <input type="hidden" name="dir" :value="sortDir">
            <input type="hidden" name="per_page" :value="perPage">
        </form>
    </div>

    <div x-show="toastVisible" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed right-4 top-20 z-50 max-w-sm rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 shadow-lg"
        style="display:none"
        @click="toastVisible = false"
        role="alert"
    >
        <div class="flex items-center gap-3">
            <i class="fas fa-bell text-emerald-500" aria-hidden="true"></i>
            <p class="flex-1 text-sm font-medium text-emerald-900" x-text="toastMessage"></p>
            <i class="fas fa-times text-xs text-emerald-700 cursor-pointer" aria-hidden="true"></i>
        </div>
    </div>

    @if($orders->count() === 0 && empty($search) && empty($status) && empty($dateFrom) && empty($dateTo))
        <div class="rounded-2xl border border-brand-100 bg-white py-20 text-center shadow-md shadow-brand-200/20">
            <i class="fas fa-shopping-bag mb-4 text-5xl text-brand-200" aria-hidden="true"></i>
            <p class="mb-2 text-lg font-semibold text-neutral-700">Belum ada pesanan</p>
            <p class="mb-6 text-sm text-neutral-500">Mulai belanja dan lihat riwayat pesanan Anda di sini.</p>
            <a href="{{ route('catalog') }}" class="btn-brand inline-flex px-8 py-3">Jelajahi Katalog</a>
        </div>
    @elseif($orders->count() === 0)
        <div class="rounded-2xl border border-brand-100 bg-white py-16 text-center shadow-md shadow-brand-200/20">
            <i class="fas fa-search mb-4 text-4xl text-brand-200" aria-hidden="true"></i>
            <p class="mb-6 text-neutral-600">Tidak ada pesanan yang cocok dengan filter Anda.</p>
            <a href="{{ route('orders.index') }}" class="btn-brand-outline inline-flex px-6 py-2.5">Reset Filter</a>
        </div>
    @else
        <div class="mb-4 flex items-center justify-between text-sm text-neutral-500">
            <p>
                Menampilkan <span class="font-semibold text-brand-800">{{ $orders->count() }}</span>
                dari <span class="font-semibold text-brand-800">{{ $orders->total() }}</span> pesanan
                @if($orders->hasPages())
                    — Halaman {{ $orders->currentPage() }} dari {{ $orders->lastPage() }}
                @endif
            </p>
            <div class="flex items-center gap-2">
                <span class="hidden sm:inline text-neutral-500">Tampilkan:</span>
                <select
                    @change="changePerPage($event.target.value)"
                    class="rounded-xl border border-brand-200 bg-white px-3 py-1.5 text-xs font-medium text-brand-700 focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200"
                >
                    <option value="5" {{ $perPage == 5 ? 'selected' : '' }}>5</option>
                    <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                </select>
            </div>
        </div>

        <div class="mb-6 overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-md shadow-brand-200/15">
            <div class="hidden md:block">
                <table class="orders-table w-full text-sm">
                    <thead>
                        <tr>
                            <th class="px-5 py-3.5 text-left">
                                <button type="button" @click="toggleSort('order_number')" class="flex items-center gap-1.5 group">
                                    No. Pesanan
                                    <i class="fas fa-sort text-xs text-brand-300 group-hover:text-brand-500" :class="{ 'fa-sort-up': sortBy === 'order_number' && sortDir === 'asc', 'fa-sort-down': sortBy === 'order_number' && sortDir === 'desc' }" aria-hidden="true"></i>
                                </button>
                            </th>
                            <th class="px-5 py-3.5 text-left">
                                <button type="button" @click="toggleSort('created_at')" class="flex items-center gap-1.5 group">
                                    Tanggal
                                    <i class="fas fa-sort text-xs text-brand-300 group-hover:text-brand-500" :class="{ 'fa-sort-up': sortBy === 'created_at' && sortDir === 'asc', 'fa-sort-down': sortBy === 'created_at' && sortDir === 'desc' }" aria-hidden="true"></i>
                                </button>
                            </th>
                            <th class="px-5 py-3.5 text-center">Items</th>
                            <th class="px-5 py-3.5 text-left">
                                <button type="button" @click="toggleSort('status')" class="flex items-center gap-1.5 group">
                                    Status Pesanan
                                    <i class="fas fa-sort text-xs text-brand-300 group-hover:text-brand-500" :class="{ 'fa-sort-up': sortBy === 'status' && sortDir === 'asc', 'fa-sort-down': sortBy === 'status' && sortDir === 'desc' }" aria-hidden="true"></i>
                                </button>
                            </th>
                            <th class="px-5 py-3.5 text-center">Pembayaran</th>
                            <th class="px-5 py-3.5 text-right">
                                <button type="button" @click="toggleSort('total')" class="flex items-center gap-1.5 group ml-auto">
                                    Total
                                    <i class="fas fa-sort text-xs text-brand-300 group-hover:text-brand-500" :class="{ 'fa-sort-up': sortBy === 'total' && sortDir === 'asc', 'fa-sort-down': sortBy === 'total' && sortDir === 'desc' }" aria-hidden="true"></i>
                                </button>
                            </th>
                            <th class="px-5 py-3.5 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-brand-50">
                        @foreach($orders as $order)
                            <tr class="group">
                                <td class="px-5 py-4">
                                    <div class="font-mono text-sm font-bold text-brand-800">{{ $order->order_number }}</div>
                                    <div class="mt-0.5 text-xs text-neutral-400">{{ $order->paymentMethodLabel() }}</div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="text-sm font-medium text-neutral-800">{{ $order->created_at->translatedFormat('d M Y') }}</div>
                                    <div class="mt-0.5 text-xs text-neutral-400">{{ $order->created_at->format('H:i') }} WIB</div>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="inline-flex h-7 min-w-[1.75rem] items-center justify-center rounded-full bg-brand-50 px-2 text-xs font-bold text-brand-700" title="{{ $order->items_count }} item(s)">
                                        {{ $order->items_count }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    @php
                                        $statusConfig = [
                                            'pending_payment' => ['bg-amber-50 border-amber-200 text-amber-700', 'bg-amber-500', 'Menunggu'],
                                            'processing' => ['bg-sky-50 border-sky-200 text-sky-700', 'bg-sky-500', 'Diproses'],
                                            'confirmed' => ['bg-blue-50 border-blue-200 text-blue-700', 'bg-blue-500', 'Dikonfirmasi'],
                                            'shipped' => ['bg-purple-50 border-purple-200 text-purple-700', 'bg-purple-500', 'Dikirim'],
                                            'completed' => ['bg-emerald-50 border-emerald-200 text-emerald-700', 'bg-emerald-500', 'Selesai'],
                                            'cancelled' => ['bg-red-50 border-red-200 text-red-700', 'bg-red-500', 'Batal'],
                                        ];
                                        $config = $statusConfig[$order->status] ?? ['bg-neutral-50 border-neutral-200 text-neutral-600', 'bg-neutral-400', $order->status];
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-xs font-semibold {{ $config[0] }}">
                                        <span class="orders-status-dot {{ $config[1] }}"></span>
                                        {{ $order->statusLabel() }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    @php
                                        $pConfig = [
                                            'pending' => 'text-amber-600 bg-amber-50 border-amber-200',
                                            'paid' => 'text-emerald-600 bg-emerald-50 border-emerald-200',
                                            'failed' => 'text-red-600 bg-red-50 border-red-200',
                                            'expired' => 'text-neutral-600 bg-neutral-50 border-neutral-200',
                                        ];
                                        $pc = $pConfig[$order->payment_status] ?? 'text-neutral-600 bg-neutral-50 border-neutral-200';
                                    @endphp
                                    <span class="inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-semibold {{ $pc }}">
                                        {{ $order->payment_status === 'paid' ? 'Lunas' : ucfirst($order->payment_status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <div class="text-base font-bold text-brand-900">{{ $order->formatted_total }}</div>
                                    @if($order->shipping_cost > 0)
                                        <div class="mt-0.5 text-xs text-neutral-400">+{{ $order->formatted_shipping }} ongkir</div>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex flex-col items-center gap-2 sm:flex-row sm:justify-center">
                                        <a
                                            href="{{ route('orders.show', $order->order_number) }}"
                                            class="inline-flex h-8 min-w-[2rem] items-center justify-center rounded-xl border border-brand-200 bg-white px-2.5 text-xs font-semibold text-brand-700 shadow-sm transition hover:border-brand-400 hover:bg-brand-50 hover:shadow-md"
                                            title="Lihat detail"
                                        >
                                            <i class="fas fa-eye" aria-hidden="true"></i>
                                        </a>
                                        <a
                                            href="{{ route('orders.invoice', $order->order_number) }}"
                                            class="inline-flex h-8 min-w-[2rem] items-center justify-center rounded-xl border border-brand-200 bg-white px-2.5 text-xs font-semibold text-brand-700 shadow-sm transition hover:border-brand-400 hover:bg-brand-50 hover:shadow-md"
                                            title="Download invoice"
                                            target="_blank"
                                        >
                                            <i class="fas fa-file-pdf" aria-hidden="true"></i>
                                        </a>
                                        @if($order->status === 'shipped')
                                            <a
                                                href="{{ route('orders.show', $order->order_number) }}#tracking"
                                                class="inline-flex h-8 min-w-[2rem] items-center justify-center rounded-xl border border-purple-200 bg-purple-50 px-2.5 text-xs font-semibold text-purple-700 shadow-sm transition hover:border-purple-400 hover:bg-purple-100 hover:shadow-md"
                                                title="Lacak pengiriman"
                                            >
                                                <i class="fas fa-truck" aria-hidden="true"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="md:hidden">
                @foreach($orders as $order)
                    @php
                        $statusConfig = [
                            'pending_payment' => ['bg-amber-50 border-amber-200 text-amber-700', 'bg-amber-500'],
                            'processing' => ['bg-sky-50 border-sky-200 text-sky-700', 'bg-sky-500'],
                            'confirmed' => ['bg-blue-50 border-blue-200 text-blue-700', 'bg-blue-500'],
                            'shipped' => ['bg-purple-50 border-purple-200 text-purple-700', 'bg-purple-500'],
                            'completed' => ['bg-emerald-50 border-emerald-200 text-emerald-700', 'bg-emerald-500'],
                            'cancelled' => ['bg-red-50 border-red-200 text-red-700', 'bg-red-500'],
                        ];
                        $config = $statusConfig[$order->status] ?? ['bg-neutral-50 border-neutral-200 text-neutral-600', 'bg-neutral-400'];
                    @endphp
                    <div class="orders-card rounded-2xl border border-brand-100 bg-white p-4 shadow-sm">
                        <div class="mb-3 flex items-start justify-between gap-2">
                            <div>
                                <div class="font-mono text-sm font-bold text-brand-800">{{ $order->order_number }}</div>
                                <div class="mt-0.5 text-xs text-neutral-400">
                                    {{ $order->created_at->translatedFormat('d M Y, H:i') }} WIB
                                </div>
                            </div>
                            <span class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-xs font-semibold {{ $config[0] }}">
                                <span class="orders-status-dot {{ $config[1] }}"></span>
                                {{ $order->statusLabel() }}
                            </span>
                        </div>

                        <div class="mb-3 flex items-center justify-between">
                            <div>
                                <div class="text-xs text-neutral-500">{{ $order->items_count }} item(s)</div>
                                <div class="mt-0.5 text-sm text-neutral-500">{{ $order->paymentMethodLabel() }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold text-brand-900">{{ $order->formatted_total }}</div>
                                <span class="inline-flex items-center rounded-full border px-2 py-0.5 text-xs font-semibold {{ $order->payment_status === 'paid' ? 'border-emerald-200 bg-emerald-50 text-emerald-600' : 'border-amber-200 bg-amber-50 text-amber-600' }}">
                                    {{ $order->payment_status === 'paid' ? 'Lunas' : 'Menunggu' }}
                                </span>
                            </div>
                        </div>

                        @if($order->items_count > 0)
                            <div class="mb-3 flex gap-2 overflow-x-auto pb-1">
                                @foreach($order->items->take(4) as $item)
                                    @if($item->product && $item->product->image)
                                        <img
                                            src="{{ Storage::url($item->product->image) }}"
                                            alt="{{ $item->product_name }}"
                                            class="h-12 w-12 shrink-0 rounded-xl border border-brand-100 object-contain bg-white"
                                            loading="lazy"
                                        >
                                    @endif
                                @endforeach
                                @if($order->items_count > 4)
                                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl border border-brand-100 bg-brand-50 text-xs font-bold text-brand-700">
                                        +{{ $order->items_count - 4 }}
                                    </div>
                                @endif
                            </div>
                        @endif

                        <div class="flex gap-2">
                            <a
                                href="{{ route('orders.show', $order->order_number) }}"
                                class="flex-1 rounded-xl border border-brand-200 bg-white py-2.5 text-center text-xs font-semibold text-brand-700 transition hover:border-brand-400 hover:bg-brand-50"
                            >
                                <i class="fas fa-eye me-1.5" aria-hidden="true"></i> Detail
                            </a>
                            <a
                                href="{{ route('orders.invoice', $order->order_number) }}"
                                class="flex-1 rounded-xl border border-brand-200 bg-white py-2.5 text-center text-xs font-semibold text-brand-700 transition hover:border-brand-400 hover:bg-brand-50"
                                target="_blank"
                            >
                                <i class="fas fa-file-pdf me-1.5" aria-hidden="true"></i> Invoice
                            </a>
                            @if($order->status === 'shipped')
                                <a
                                    href="{{ route('orders.show', $order->order_number) }}#tracking"
                                    class="flex-1 rounded-xl border border-purple-200 bg-purple-50 py-2.5 text-center text-xs font-semibold text-purple-700 transition hover:border-purple-400 hover:bg-purple-100"
                                >
                                    <i class="fas fa-truck me-1.5" aria-hidden="true"></i> Lacak
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mt-6">
            @if($orders->hasPages())
                {{ $orders->appends(request()->query())->links('pagination.tailwind-pink') }}
            @endif
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function ordersPage() {
    return {
        loading: false,
        searchLocal: '{{ $search }}',
        dateFromLocal: '{{ $dateFrom }}',
        dateToLocal: '{{ $dateTo }}',
        sortBy: '{{ $sortBy }}',
        sortDir: '{{ $sortDir }}',
        perPage: {{ $perPage }},
        toastVisible: false,
        toastMessage: '',
        pollTimer: null,
        lastEtag: null,

        init() {
            this.startPolling();
            this.$watch('loading', (val) => {
                if (!val) {
                    this.$el.querySelectorAll('.orders-table tbody tr').forEach((row, i) => {
                        row.style.opacity = '0';
                        row.style.transform = 'translateY(8px)';
                        setTimeout(() => {
                            row.style.transition = 'all 0.35s cubic-bezier(0.22, 1, 0.36, 1)';
                            row.style.opacity = '1';
                            row.style.transform = 'translateY(0)';
                        }, i * 40);
                    });
                }
            });
        },

        startPolling() {
            this.pollTimer = setInterval(() => this.poll(), 30000);
            window.addEventListener('beforeunload', () => clearInterval(this.pollTimer));
        },

        async poll() {
            try {
                const res = await fetch('{{ route('orders.poll') }}?last_id=0', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    }
                });
                if (res.status === 304) return;
                const data = await res.json();
                if (data.changed && data.orders.length > 0) {
                    const orderNumbers = data.orders.map(o => o.order_number).join(', ');
                    this.showToast('Status pesanan #' + orderNumbers + ' mungkin telah berubah. Refresh untuk lihat update.');
                }
            } catch (e) {}
        },

        showToast(message) {
            this.toastMessage = message;
            this.toastVisible = true;
            setTimeout(() => { this.toastVisible = false; }, 6000);
        },

        refresh() {
            this.loading = true;
            window.location.href = '{{ route('orders.index') }}?' + new URLSearchParams({
                q: this.searchLocal,
                status: '{{ $status }}',
                from: this.dateFromLocal,
                to: this.dateToLocal,
                sort: this.sortBy,
                dir: this.sortDir,
                per_page: this.perPage,
            }).toString();
        },

        applySearch() {
            clearTimeout(this._searchTimer);
            this._searchTimer = setTimeout(() => {
                this.$refs.filterForm.submit();
            }, 350);
        },

        toggleSort(field) {
            if (this.sortBy === field) {
                this.sortDir = this.sortDir === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortBy = field;
                this.sortDir = 'desc';
            }
            const form = this.$refs.filterForm;
            form.querySelector('input[name=sort]').value = this.sortBy;
            form.querySelector('input[name=dir]').value = this.sortDir;
            form.submit();
        },

        changePerPage(val) {
            this.perPage = val;
            const form = this.$refs.filterForm;
            form.querySelector('input[name=per_page]').value = val;
            form.submit();
        },
    }
}
</script>
@endpush
