@extends('layouts.app')

@section('title', 'Keranjang — Skinbae.ID')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8" x-data="cartManager({{ \Illuminate\Support\Js::from($lines->map(fn($l) => ['id' => $l->product->id, 'price' => $l->product->final_price, 'quantity' => $l->quantity, 'is_selected' => $l->is_selected])) }})">
    <h1 class="mb-8 text-3xl font-bold text-brand-900">Keranjang belanja</h1>

    @if($lines->isEmpty())
        <div class="rounded-2xl border border-brand-100 bg-white py-16 text-center shadow-md shadow-brand-200/20">
            <i class="fas fa-shopping-bag mb-4 text-5xl text-brand-200" aria-hidden="true"></i>
            <p class="mb-6 text-neutral-600">Keranjang Anda masih kosong.</p>
            <a href="{{ route('catalog') }}" class="btn-brand inline-flex px-8 py-3">Jelajahi katalog</a>
        </div>
    @else
        <div class="flex flex-col gap-8 lg:flex-row lg:items-start">
            <div class="min-w-0 flex-1 space-y-4">
                <!-- Select All -->
                <div class="flex items-center gap-3 rounded-2xl border border-brand-100 bg-white p-4 shadow-sm sm:px-5 sm:py-3">
                    <input type="checkbox" id="selectAll" class="h-5 w-5 rounded border-brand-300 text-brand-500 focus:ring-brand-400" x-model="allSelected" @change="toggleAll()">
                    <label for="selectAll" class="text-sm font-bold text-neutral-800">Pilih Semua Produk</label>
                </div>

                @foreach($lines as $line)
                    @php $p = $line->product; @endphp
                    <div class="rounded-2xl border border-brand-100 bg-white p-4 shadow-md shadow-brand-200/15 sm:p-5 transition" :class="{'opacity-75': !items[{{ $p->id }}].is_selected}">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                            <!-- Checkbox -->
                            <div class="flex items-center pt-2 sm:pt-0">
                                <input type="checkbox" class="h-5 w-5 rounded border-brand-300 text-brand-500 focus:ring-brand-400" x-model="items[{{ $p->id }}].is_selected" @change="toggleItem({{ $p->id }})">
                            </div>
                            
                            <div class="shrink-0 sm:w-24">
                                <img src="{{ $p->image_url }}" alt="{{ $p->name }}" class="mx-auto h-24 w-24 rounded-xl object-contain sm:mx-0">
                            </div>
                            <div class="min-w-0 flex-1">
                                <h2 class="font-bold text-neutral-900">
                                    <a href="{{ route('products.show', $p->slug) }}" class="hover:text-brand-600">{{ Str::limit($p->name, 48) }}</a>
                                </h2>
                                <p class="text-sm text-brand-500">{{ $p->brand->name }}</p>
                                <p class="mt-1 text-sm font-semibold text-gradient-brand">{{ $p->formatted_final_price }}</p>
                            </div>
                            <div class="flex flex-col gap-2 sm:items-end">
                                <form action="{{ route('cart.update', $p) }}" method="POST" class="flex flex-wrap items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <label class="text-xs text-neutral-500" for="qty-{{ $p->id }}">Qty</label>
                                    <input type="number" name="quantity" id="qty-{{ $p->id }}" class="input-brand w-20 rounded-xl py-1.5 text-center text-sm" min="0" max="{{ $p->stock_quantity }}" value="{{ $line->quantity }}">
                                    <button type="submit" class="rounded-full border border-brand-300 px-3 py-1.5 text-xs font-semibold text-brand-700 hover:bg-brand-50">Ubah</button>
                                </form>
                                <p class="text-xs text-neutral-400">Maks. stok: {{ $p->stock_quantity }}</p>
                            </div>
                            <div class="text-end sm:min-w-[7rem]">
                                <p class="text-lg font-bold text-brand-900">Rp {{ number_format($p->final_price * $line->quantity, 0, ',', '.') }}</p>
                                <form action="{{ route('cart.destroy', $p) }}" method="POST" class="mt-2" onsubmit="return confirm('Hapus dari keranjang?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm font-medium text-red-600 hover:underline">Hapus</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="w-full shrink-0 lg:sticky lg:top-24 lg:w-80">
                <div class="rounded-2xl border border-brand-100 bg-gradient-to-b from-white to-brand-50/50 p-6 shadow-lg shadow-brand-200/30">
                    <h2 class="mb-4 text-lg font-bold text-brand-900">Ringkasan</h2>
                    <div class="flex justify-between text-sm">
                        <span class="text-neutral-600">Subtotal <span x-text="'(' + selectedCount + ' barang)'"></span></span>
                        <span class="font-bold text-brand-900" x-text="formatRupiah(subtotal)"></span>
                    </div>
                    <p class="mt-3 text-xs text-neutral-500">Gratis ongkir di atas Rp 500.000 (dihitung saat checkout).</p>
                    <a href="{{ route('checkout.index') }}" class="btn-brand mt-6 flex w-full justify-center py-3 transition" :class="{'opacity-50 cursor-not-allowed': selectedCount === 0}" :aria-disabled="selectedCount === 0" @click="if(selectedCount === 0) $event.preventDefault()">Lanjut ke checkout <i class="fas fa-arrow-right ms-2" aria-hidden="true"></i></a>
                    <a href="{{ route('catalog') }}" class="btn-brand-outline mt-3 flex w-full justify-center py-3">Lanjut belanja</a>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
function cartManager(initialItems) {
    let itemsObj = {};
    initialItems.forEach(i => {
        itemsObj[i.id] = i;
    });

    return {
        items: itemsObj,
        isUpdating: false,
        
        get allSelected() {
            const arr = Object.values(this.items);
            return arr.length > 0 && arr.every(i => i.is_selected);
        },
        
        set allSelected(value) {
            // setter needed for x-model but we handle logic in toggleAll
        },

        get subtotal() {
            return Object.values(this.items)
                .filter(i => i.is_selected)
                .reduce((sum, i) => sum + (i.price * i.quantity), 0);
        },

        get selectedCount() {
            return Object.values(this.items)
                .filter(i => i.is_selected)
                .reduce((count, i) => count + i.quantity, 0);
        },

        formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
        },

        async toggleItem(productId) {
            const isSelected = this.items[productId].is_selected;
            this.isUpdating = true;
            
            try {
                await fetch(`/cart/${productId}/toggle`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ is_selected: isSelected })
                });
            } catch (e) {
                console.error(e);
                // Revert on failure
                this.items[productId].is_selected = !isSelected;
            } finally {
                this.isUpdating = false;
            }
        },

        async toggleAll() {
            const willSelect = !this.allSelected;
            this.isUpdating = true;
            
            // Update local state first
            Object.keys(this.items).forEach(key => {
                this.items[key].is_selected = willSelect;
            });

            try {
                await fetch(`/cart/toggle-all`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ is_selected: willSelect })
                });
            } catch (e) {
                console.error(e);
                // Hard reload to sync if failed
                window.location.reload();
            } finally {
                this.isUpdating = false;
            }
        }
    }
}
</script>
@endsection
