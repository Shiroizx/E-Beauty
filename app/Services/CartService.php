<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CartService
{
    public function getTotalQuantity(): int
    {
        if (! auth()->check()) {
            return 0;
        }

        return (int) CartItem::query()->where('user_id', auth()->id())->sum('quantity');
    }

    /**
     * @return Collection<int, object{product: Product, quantity: int, is_selected: bool}>
     */
    public function getLineItems(): Collection
    {
        if (! auth()->check()) {
            return collect();
        }

        return CartItem::query()
            ->where('user_id', auth()->id())
            ->with(['product.brand', 'product.stock'])
            ->get()
            ->map(function (CartItem $row) {
                return (object) [
                    'product' => $row->product,
                    'quantity' => (int) $row->quantity,
                    'is_selected' => (bool) $row->is_selected,
                ];
            })
            ->filter(fn ($line) => $line->product !== null && $line->product->is_active);
    }

    /**
     * @return Collection<int, object{product: Product, quantity: int, is_selected: bool}>
     */
    public function getSelectedLineItems(): Collection
    {
        return $this->getLineItems()->filter(fn ($line) => $line->is_selected);
    }

    public function add(Product $product, int $quantity): void
    {
        if (! auth()->check()) {
            return;
        }

        $quantity = max(1, $quantity);
        $quantity = min($quantity, max(0, $product->stock_quantity));

        if ($quantity < 1) {
            return;
        }

        DB::transaction(function () use ($product, $quantity) {
            $row = CartItem::query()
                ->where('user_id', auth()->id())
                ->where('product_id', $product->id)
                ->lockForUpdate()
                ->first();

            $current = $row ? $row->quantity : 0;
            $newQty = min($current + $quantity, $product->stock_quantity);

            if ($newQty < 1) {
                return;
            }

            CartItem::query()->updateOrCreate(
                [
                    'user_id' => auth()->id(),
                    'product_id' => $product->id,
                ],
                ['quantity' => $newQty]
            );
        });
    }

    public function updateQuantity(Product $product, int $quantity): void
    {
        if (! auth()->check()) {
            return;
        }

        $quantity = max(0, min($quantity, $product->stock_quantity));

        if ($quantity < 1) {
            CartItem::query()
                ->where('user_id', auth()->id())
                ->where('product_id', $product->id)
                ->delete();

            return;
        }

        CartItem::query()->updateOrCreate(
            [
                'user_id' => auth()->id(),
                'product_id' => $product->id,
            ],
            ['quantity' => $quantity]
        );
    }

    public function remove(Product $product): void
    {
        $this->updateQuantity($product, 0);
    }

    public function toggleSelection(Product $product, bool $isSelected): void
    {
        if (! auth()->check()) {
            return;
        }

        CartItem::query()
            ->where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->update(['is_selected' => $isSelected]);
    }

    public function toggleAllSelection(bool $isSelected): void
    {
        if (! auth()->check()) {
            return;
        }

        CartItem::query()
            ->where('user_id', auth()->id())
            ->update(['is_selected' => $isSelected]);
    }

    public function clearForUser(int $userId): void
    {
        CartItem::query()->where('user_id', $userId)->delete();
    }
}