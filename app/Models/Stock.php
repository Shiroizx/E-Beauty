<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'quantity',
        'min_quantity',
        'warehouse_location',
        'batch_number',
        'expiry_date',
    ];

    protected $casts = [
        'expiry_date' => 'date',
    ];

    /**
     * Get the product that owns the stock
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope to get low stock items
     */
    public function scopeLowStock($query)
    {
        return $query->whereColumn('quantity', '<=', 'min_quantity');
    }

    /**
     * Scope to get expiring soon items
     */
    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('expiry_date', '<=', Carbon::now()->addDays($days))
            ->where('expiry_date', '>=', Carbon::now());
    }

    /**
     * Check if stock is low
     */
    public function getIsLowStockAttribute()
    {
        return $this->quantity <= $this->min_quantity;
    }

    /**
     * Check if product is expired
     */
    public function getIsExpiredAttribute()
    {
        if (!$this->expiry_date) {
            return false;
        }
        return $this->expiry_date->isPast();
    }

    /**
     * Check if expiring soon
     */
    public function getIsExpiringSoonAttribute($days = 30)
    {
        if (!$this->expiry_date) {
            return false;
        }
        
        return $this->expiry_date->isFuture() && 
               $this->expiry_date->diffInDays(Carbon::now()) <= $days;
    }

    /**
     * Update stock quantity
     */
    public function updateQuantity($amount, $type = 'add')
    {
        if ($type === 'add') {
            $this->quantity += $amount;
        } else {
            $this->quantity -= $amount;
        }

        $this->save();
        return $this->quantity;
    }

    /**
     * Reduce stock (for sales/orders)
     */
    public function reduce($amount)
    {
        return $this->updateQuantity($amount, 'reduce');
    }

    /**
     * Add stock (for restocking)
     */
    public function add($amount)
    {
        return $this->updateQuantity($amount, 'add');
    }
}
