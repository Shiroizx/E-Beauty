@extends('layouts.admin')

@section('title', 'Moderasi Review — Admin Skinbae.ID')
@section('page_title', 'Review')

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-bold tracking-tight text-gray-900">Moderasi Review</h2>
            <p class="mt-0.5 text-sm text-gray-400">Setujui atau hapus review dari pelanggan</p>
        </div>
        <div class="flex items-center rounded-xl bg-white p-1 shadow-card ring-1 ring-stone-100">
            <a href="{{ route('admin.reviews.index', ['status' => 'all']) }}"
               class="rounded-lg px-3.5 py-1.5 text-xs font-semibold transition {{ $status == 'all' ? 'bg-gray-800 text-white shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                Semua
            </a>
            <a href="{{ route('admin.reviews.index', ['status' => 'pending']) }}"
               class="rounded-lg px-3.5 py-1.5 text-xs font-semibold transition {{ $status == 'pending' ? 'bg-amber-500 text-white shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                Menunggu
            </a>
            <a href="{{ route('admin.reviews.index', ['status' => 'approved']) }}"
               class="rounded-lg px-3.5 py-1.5 text-xs font-semibold transition {{ $status == 'approved' ? 'bg-emerald-500 text-white shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                Disetujui
            </a>
        </div>
    </div>

    {{-- Reviews Table --}}
    <div class="overflow-hidden rounded-2xl bg-white shadow-card">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-stone-100 text-left">
                        <th class="py-3 pl-5 pr-3 text-[11px] font-semibold uppercase tracking-wider text-gray-400" style="width:28%">Produk</th>
                        <th class="px-3 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-400" style="width:14%">User</th>
                        <th class="px-3 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-400" style="width:12%">Rating</th>
                        <th class="px-3 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-400" style="width:32%">Komentar</th>
                        <th class="py-3 pl-3 pr-5 text-right text-[11px] font-semibold uppercase tracking-wider text-gray-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-50">
                    @forelse($reviews as $review)
                        <tr class="transition hover:bg-stone-50/60">
                            <td class="py-3 pl-5 pr-3">
                                <p class="font-semibold text-gray-800">{{ Str::limit($review->product->name, 40) }}</p>
                                <p class="text-[11px] text-gray-400">{{ $review->created_at->format('d M Y H:i') }}</p>
                            </td>
                            <td class="px-3 py-3">
                                <p class="font-medium text-gray-700">{{ $review->user->name }}</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5">
                                    @if($review->is_verified_purchase)
                                        <span class="inline-flex items-center rounded-full bg-sky-50 px-1.5 py-0.5 text-[9px] font-bold text-sky-600 ring-1 ring-inset ring-sky-200/60">Verified</span>
                                    @endif
                                    @if($review->order_id && $review->order)
                                        <a href="{{ route('admin.orders.show', $review->order_id) }}" class="text-[10px] font-semibold text-brand-600 hover:text-brand-800">{{ $review->order->order_number }}</a>
                                    @endif
                                </div>
                            </td>
                            <td class="px-3 py-3">
                                <div class="flex items-center gap-0.5 text-amber-400">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $review->rating)
                                            <i class="fas fa-star text-[10px]"></i>
                                        @else
                                            <i class="far fa-star text-[10px] text-gray-200"></i>
                                        @endif
                                    @endfor
                                </div>
                            </td>
                            <td class="px-3 py-3">
                                <p class="text-sm italic text-gray-500 leading-relaxed">"{{ Str::limit($review->comment, 100) }}"</p>
                            </td>
                            <td class="py-3 pl-3 pr-5">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.reviews.show', ['review' => $review, 'from_status' => $status]) }}" class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 transition hover:bg-stone-100 hover:text-brand-600" title="Detail">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    @if(!$review->is_approved)
                                        <form action="{{ route('admin.reviews.approve', $review->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="flex h-8 w-8 items-center justify-center rounded-lg text-emerald-500 transition hover:bg-emerald-50" title="Approve">
                                                <i class="fas fa-check text-xs"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST" onsubmit="return confirm('Hapus review ini?');">
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
                                    <i class="fas fa-star text-stone-300 text-lg"></i>
                                </div>
                                <p class="text-sm text-gray-400">Tidak ada review ditemukan</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reviews->hasPages())
            <div class="border-t border-stone-100 px-5 py-3">
                {{ $reviews->links('pagination::tailwind') }}
            </div>
        @endif
    </div>
</div>
@endsection
