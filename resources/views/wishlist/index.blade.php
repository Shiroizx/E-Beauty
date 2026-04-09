@extends('layouts.app')

@section('title', 'Wishlist — Skinbae.ID')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    <h1 class="mb-8 text-3xl font-bold text-brand-900">Wishlist</h1>

    @if($items->isEmpty())
        <div class="rounded-2xl border border-brand-100 bg-white py-16 text-center shadow-md">
            <i class="far fa-heart mb-4 text-5xl text-brand-200" aria-hidden="true"></i>
            <p class="mb-6 text-neutral-600">Belum ada produk favorit.</p>
            <a href="{{ route('catalog') }}" class="btn-brand inline-flex px-8 py-3">Jelajahi katalog</a>
        </div>
    @else
        <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4">
            @foreach($items as $product)
                <article class="group relative flex flex-col overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-md transition hover:shadow-pink-soft">
                    @if($product->has_discount)
                        <span class="absolute left-2 top-2 z-10 rounded-full bg-gradient-to-r from-brand-500 to-brand-600 px-2 py-0.5 text-[0.65rem] font-bold text-white">-{{ $product->discount_percentage }}%</span>
                    @endif
                    <a href="{{ route('products.show', $product->slug) }}" class="block aspect-square overflow-hidden bg-brand-50">
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition group-hover:scale-105">
                    </a>
                    <div class="flex flex-1 flex-col p-3">
                        <p class="text-xs text-brand-500">{{ $product->brand->name }}</p>
                        <h2 class="mt-1 flex-1 text-sm font-semibold text-neutral-900">
                            <a href="{{ route('products.show', $product->slug) }}" class="hover:text-brand-600">{{ Str::limit($product->name, 40) }}</a>
                        </h2>
                        <div class="mt-2">
                            @if($product->has_discount)
                                <p class="text-xs text-neutral-400 line-through">{{ $product->formatted_price }}</p>
                                <p class="font-bold text-gradient-brand">{{ $product->formatted_final_price }}</p>
                            @else
                                <p class="font-bold text-gradient-brand">{{ $product->formatted_price }}</p>
                            @endif
                        </div>
                        <div class="relative z-[1] mt-3 flex flex-col gap-2">
                            @if($product->stock_quantity > 0)
                                <form action="{{ route('cart.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn-brand w-full py-2 text-xs">+ Keranjang</button>
                                </form>
                            @else
                                <button type="button" class="w-full cursor-not-allowed rounded-full bg-neutral-200 py-2 text-xs font-semibold text-neutral-500" disabled>Habis</button>
                            @endif
                            <form action="{{ route('wishlist.destroy', $product) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full rounded-full border border-red-200 py-2 text-xs font-medium text-red-600 hover:bg-red-50">Hapus</button>
                            </form>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</div>
@endsection
