<?php

namespace App\Services;

use App\Models\Promo;
use Carbon\Carbon;

class PromoService
{
    /**
     * Validate promo code
     */
    public function validatePromoCode(string $code, int $userId, float $subtotal, array $productIds = [])
    {
        $promo = Promo::where('code', $code)
            ->available()
            ->first();

        if (!$promo) {
            return [
                'valid' => false,
                'message' => 'Kode promo tidak valid atau sudah expired',
            ];
        }

        // Check minimum purchase
        if ($promo->min_purchase && $subtotal < $promo->min_purchase) {
            return [
                'valid' => false,
                'message' => 'Minimum pembelian untuk promo ini adalah Rp ' . number_format($promo->min_purchase, 0, ',', '.'),
            ];
        }

        // Check if promo is product-specific
        if ($promo->products()->count() > 0 && !empty($productIds)) {
            $validProducts = $promo->products()->whereIn('products.id', $productIds)->count();
            
            if ($validProducts === 0) {
                return [
                    'valid' => false,
                    'message' => 'Promo ini tidak berlaku untuk produk yang dipilih',
                ];
            }
        }

        // Check user-specific usage limit
        // Note: In a real app, you'd check a promo_usages table
        if (!$promo->canBeUsedBy($userId)) {
            return [
                'valid' => false,
                'message' => 'Anda sudah mencapai batas penggunaan promo ini',
            ];
        }

        $discount = $promo->calculateDiscount($subtotal);

        return [
            'valid' => true,
            'promo' => $promo,
            'discount_amount' => $discount,
            'final_total' => max(0, $subtotal - $discount),
            'message' => 'Promo berhasil diterapkan!',
        ];
    }

    /**
     * Calculate discount for cart/order
     */
    public function calculateDiscount(Promo $promo, float $subtotal, array $productIds = [])
    {
        // If promo is product-specific, only calculate for applicable products
        if ($promo->products()->count() > 0) {
            // In a real application, you'd recalculate subtotal for only applicable products
            // For now, we'll use the provided subtotal
        }

        return $promo->calculateDiscount($subtotal);
    }

    /**
     * Apply promo to cart
     */
    public function applyPromo(string $code, int $userId, array $cartItems)
    {
        $subtotal = collect($cartItems)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });

        $productIds = collect($cartItems)->pluck('product_id')->toArray();

        $validation = $this->validatePromoCode($code, $userId, $subtotal, $productIds);

        if (!$validation['valid']) {
            return $validation;
        }

        return $validation;
    }

    /**
     * Get available promos for user
     */
    public function getAvailablePromos(int $userId = null)
    {
        $query = Promo::available()
            ->orderByDesc('discount_value');

        return $query->get();
    }

    /**
     * Get promo details by code
     */
    public function getPromoByCode(string $code)
    {
        return Promo::where('code', $code)->first();
    }

    /**
     * Mark promo as used
     */
    public function markAsUsed(Promo $promo, int $userId)
    {
        $promo->incrementUsage();

        // In a real application, you'd also create a record in promo_usages table
        // to track which user used the promo

        return true;
    }

    /**
     * Get promo statistics
     */
    public function getStatistics()
    {
        return [
            'total_promos' => Promo::count(),
            'active_promos' => Promo::available()->count(),
            'expired_promos' => Promo::where('end_date', '<', Carbon::now())->count(),
            'most_used' => Promo::orderByDesc('used_count')->first(),
        ];
    }
}
