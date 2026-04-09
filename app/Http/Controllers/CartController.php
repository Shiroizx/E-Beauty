<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {}

    public function index()
    {
        $lines = $this->cartService->getLineItems();
        $subtotal = $lines->filter(fn ($line) => $line->is_selected)->sum(fn ($line) => $line->product->final_price * $line->quantity);

        return view('cart.index', compact('lines', 'subtotal'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:999'],
        ]);

        $product = Product::query()->with('stock')->active()->findOrFail($data['product_id']);

        if ($product->stock_quantity < 1) {
            return back()->with('error', 'Produk sedang tidak tersedia.');
        }

        $qty = (int) ($data['quantity'] ?? 1);
        $this->cartService->add($product, $qty);

        return back()->with('success', 'Produk ditambahkan ke keranjang.');
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:0', 'max:999'],
        ]);

        $product->load('stock');
        if (! $product->is_active) {
            return back()->with('error', 'Produk tidak tersedia.');
        }

        $this->cartService->updateQuantity($product, (int) $data['quantity']);

        return back()->with('success', 'Keranjang diperbarui.');
    }

    public function destroy(Product $product)
    {
        $this->cartService->remove($product);

        return back()->with('success', 'Item dihapus dari keranjang.');
    }

    public function toggle(Request $request, Product $product)
    {
        $isSelected = $request->boolean('is_selected');
        $this->cartService->toggleSelection($product, $isSelected);
        
        return response()->json(['success' => true]);
    }

    public function toggleAll(Request $request)
    {
        $isSelected = $request->boolean('is_selected');
        $this->cartService->toggleAllSelection($isSelected);
        
        return response()->json(['success' => true]);
    }
}
