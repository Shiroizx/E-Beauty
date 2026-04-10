<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'discount_type',
        'discount_value',
        'min_purchase',
        'max_discount',
        'usage_limit',
        'usage_per_user',
        'used_count',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'min_purchase' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get products this promo applies to
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'promo_products');
    }

    /**
     * Scope to get only active promos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get available promos (within date range and usage limit)
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_active', true)
            ->where('start_date', '<=', Carbon::now())
            ->where('end_date', '>=', Carbon::now())
            ->where(function ($q) {
                $q->whereNull('usage_limit')
                    ->orWhereColumn('used_count', '<', 'usage_limit');
            });
    }

    /**
     * Check if promo is currently active
     */
    public function getIsCurrentlyActiveAttribute()
    {
        return $this->is_active && 
               $this->start_date <= Carbon::now() && 
               $this->end_date >= Carbon::now();
    }

    /**
     * Check if promo is available
     */
    public function getIsAvailableAttribute()
    {
        if (!$this->is_currently_active) {
            return false;
        }

        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    /**
     * Calculate discount amount for given subtotal
     */
    public function calculateDiscount($subtotal)
    {
        // Check minimum purchase requirement
        if ($this->min_purchase && $subtotal < $this->min_purchase) {
            return 0;
        }

        $discount = 0;

        if ($this->discount_type === 'percentage') {
            $discount = ($subtotal * $this->discount_value) / 100;
            
            // Apply max discount limit if set
            if ($this->max_discount && $discount > $this->max_discount) {
                $discount = $this->max_discount;
            }
        } else {
            // Fixed discount
            $discount = $this->discount_value;
        }

        // Make sure discount doesn't exceed subtotal
        return min($discount, $subtotal);
    }

    /**
     * Jumlah pesanan (bukan dibatalkan) milik user yang sudah memakai kode promo ini.
     */
    public function userUsageCount(int $userId): int
    {
        return Order::query()
            ->where('user_id', $userId)
            ->whereNotNull('promo_code')
            ->where('promo_code', '!=', '')
            ->whereRaw('UPPER(TRIM(promo_code)) = ?', [strtoupper(trim((string) $this->code))])
            ->where('status', '!=', 'cancelled')
            ->count();
    }

    /**
     * Check if user can use this promo (global + batas per user lewat usage_per_user).
     */
    public function canBeUsedBy($userId): bool
    {
        if (! $this->is_available) {
            return false;
        }

        $perUser = max(1, (int) $this->usage_per_user);

        return $this->userUsageCount((int) $userId) < $perUser;
    }

    /**
     * Increment usage count
     */
    public function incrementUsage()
    {
        $this->used_count++;
        $this->save();
    }

    /**
     * Get formatted discount value
     */
    public function getFormattedDiscountAttribute()
    {
        if ($this->discount_type === 'percentage') {
            return $this->discount_value . '%';
        }
        return 'Rp ' . number_format($this->discount_value, 0, ',', '.');
    }

    /**
     * Get remaining usage
     */
    public function getRemainingUsageAttribute()
    {
        if (!$this->usage_limit) {
            return null; // Unlimited
        }
        return max(0, $this->usage_limit - $this->used_count);
    }
}
