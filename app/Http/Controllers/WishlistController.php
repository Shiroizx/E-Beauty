<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\WishlistItem;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index()
    {
        $items = WishlistItem::query()
            ->where('user_id', auth()->id())
            ->with(['product.brand', 'product.stock'])
            ->latest()
            ->get()
            ->map(fn (WishlistItem $w) => $w->product)
            ->filter(fn ($p) => $p && $p->is_active)
            ->values();

        return view('wishlist.index', compact('items'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
        ]);

        $product = Product::query()->active()->findOrFail($data['product_id']);

        WishlistItem::query()->firstOrCreate([
            'user_id' => auth()->id(),
            'product_id' => $product->id,
        ]);

        return back()->with('success', 'Ditambahkan ke wishlist.');
    }

    public function destroy(Product $product)
    {
        WishlistItem::query()
            ->where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->delete();

        return back()->with('success', 'Dihapus dari wishlist.');
    }
}
