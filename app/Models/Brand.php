<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'logo',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get products belonging to this brand
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Scope to get only active brands
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the full URL for the brand logo
     */
    public function getLogoUrlAttribute()
    {
        if ($this->logo) {
            return Storage::url($this->logo);
        }
        return asset('images/default-brand.png');
    }

    /**
     * Get the count of products for this brand
     */
    public function getProductCountAttribute()
    {
        return $this->products()->count();
    }

    /**
     * Get active products count
     */
    public function getActiveProductCountAttribute()
    {
        return $this->products()->where('is_active', true)->count();
    }
}
