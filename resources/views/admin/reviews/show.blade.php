@extends('layouts.admin')

@section('title', 'Detail Review — Admin Skinbae.ID')
@section('page_title', 'Detail review')

@section('content')
<div class="mx-auto max-w-3xl space-y-5">
    <a href="{{ route('admin.reviews.index', ['status' => request('from_status', 'all')]) }}" class="inline-flex items-center gap-2 text-xs font-semibold text-gray-500 hover:text-brand-600">
        <i class="fas fa-arrow-left text-[10px]"></i> Kembali ke daftar
    </a>

    <div class="overflow-hidden rounded-2xl bg-white shadow-card ring-1 ring-stone-100">
        <div class="border-b border-stone-100 px-5 py-4">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Review produk</h2>
                    <p class="mt-0.5 text-sm text-gray-400">{{ $review->created_at->translatedFormat('d F Y, H:i') }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    @if($review->is_approved)
                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-200/60">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Disetujui
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-800 ring-1 ring-inset ring-amber-200/60">
                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span> Menunggu moderasi
                        </span>
                    @endif
                    @if($review->is_verified_purchase)
                        <span class="inline-flex items-center rounded-full bg-sky-50 px-2.5 py-1 text-xs font-semibold text-sky-700 ring-1 ring-inset ring-sky-200/60">Pembelian terverifikasi</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="space-y-6 px-5 py-5">
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="rounded-xl border border-stone-100 bg-stone-50/50 p-4">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Produk</p>
                    <p class="mt-1 font-semibold text-gray-900">{{ $review->product->name }}</p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        <a href="{{ route('products.show', $review->product->slug) }}" target="_blank" rel="noopener" class="text-xs font-semibold text-brand-600 hover:text-brand-800">Lihat di toko</a>
                        <span class="text-gray-300">·</span>
                        <a href="{{ route('admin.products.edit', $review->product) }}" class="text-xs font-semibold text-gray-600 hover:text-gray-900">Edit di admin</a>
                    </div>
                </div>
                <div class="rounded-xl border border-stone-100 bg-stone-50/50 p-4">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Pelanggan</p>
                    <p class="mt-1 font-semibold text-gray-900">{{ $review->user->name }}</p>
                    <p class="mt-0.5 text-sm text-gray-500">{{ $review->user->email }}</p>
                    @if($review->order_id && $review->order)
                        <p class="mt-2 text-xs">
                            <span class="text-gray-400">Pesanan:</span>
                            <a href="{{ route('admin.orders.show', $review->order) }}" class="font-semibold text-brand-600 hover:text-brand-800">{{ $review->order->order_number }}</a>
                        </p>
                    @endif
                </div>
            </div>

            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Rating</p>
                <div class="mt-2 flex items-center gap-2">
                    <div class="flex items-center gap-0.5 text-amber-400">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $review->rating)
                                <i class="fas fa-star text-base"></i>
                            @else
                                <i class="far fa-star text-base text-gray-200"></i>
                            @endif
                        @endfor
                    </div>
                    <span class="text-sm font-bold text-gray-700">{{ $review->rating }} / 5</span>
                </div>
            </div>

            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Komentar</p>
                <div class="mt-2 rounded-xl border border-stone-100 bg-white px-4 py-3 text-sm leading-relaxed text-gray-700">
                    @if($review->comment)
                        {{ $review->comment }}
                    @else
                        <span class="text-gray-400 italic">(Tanpa teks)</span>
                    @endif
                </div>
            </div>

            @php $imageUrls = $review->image_urls; @endphp
            @if(count($imageUrls) > 0)
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Foto ulasan</p>
                    <div class="mt-3 flex flex-wrap gap-3">
                        @foreach($imageUrls as $url)
                            <a href="{{ $url }}" target="_blank" rel="noopener" class="block overflow-hidden rounded-xl ring-1 ring-stone-200 transition hover:ring-brand-300">
                                <img src="{{ $url }}" alt="" class="h-28 w-28 object-cover sm:h-32 sm:w-32">
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <div class="flex flex-wrap items-center justify-end gap-2 border-t border-stone-100 bg-stone-50/40 px-5 py-4">
            @if(!$review->is_approved)
                <form action="{{ route('admin.reviews.approve', $review) }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">
                        <i class="fas fa-check text-xs"></i> Setujui
                    </button>
                </form>
            @endif
            <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" onsubmit="return confirm('Hapus review ini secara permanen?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl border border-red-200 bg-white px-4 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50">
                    <i class="fas fa-trash-alt text-xs"></i> Hapus
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
