<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'ingredients',
        'how_to_use',
        'price',
        'discount_price',
        'sku',
        'image',
        'gallery',
        'weight',
        'size',
        'brand_id',
        'category_id',
        'is_active',
        'is_featured',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'weight' => 'decimal:2',
        'gallery' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    /**
     * Get the brand that owns the product
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get the category that owns the product
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get skin types suitable for this product
     */
    public function skinTypes()
    {
        return $this->belongsToMany(SkinType::class, 'product_skin_types');
    }

    /**
     * Get the stock information for this product
     */
    public function stock()
    {
        return $this->hasOne(Stock::class);
    }

    /**
     * Get reviews for this product
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get promos applicable to this product
     */
    public function promos()
    {
        return $this->belongsToMany(Promo::class, 'promo_products');
    }

    /**
     * Scope to get only active products
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get only featured products
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope to get products in stock
     */
    public function scopeInStock($query)
    {
        return $query->whereHas('stock', function ($q) {
            $q->where('quantity', '>', 0);
        });
    }

    /**
     * Scope to filter by skin type
     */
    public function scopeForSkinType($query, $skinTypeId)
    {
        return $query->whereHas('skinTypes', function ($q) use ($skinTypeId) {
            $q->where('skin_types.id', $skinTypeId);
        });
    }

    /**
     * Scope to filter by category
     */
    public function scopeInCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope to filter by brand
     */
    public function scopeOfBrand($query, $brandId)
    {
        return $query->where('brand_id', $brandId);
    }

    /**
     * Scope to filter by price range
     */
    public function scopePriceRange($query, $min, $max)
    {
        return $query->whereBetween('price', [$min, $max]);
    }

    /**
     * Scope to filter by minimum rating
     */
    public function scopeMinRating($query, $rating)
    {
        return $query->whereHas('reviews', function ($q) use ($rating) {
            $q->selectRaw('AVG(rating) as avg_rating')
                ->groupBy('product_id')
                ->having('avg_rating', '>=', $rating);
        });
    }

    /**
     * Get the final price (considering discount)
     */
    public function getFinalPriceAttribute()
    {
        return $this->discount_price ?? $this->price;
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    /**
     * Get formatted final price
     */
    public function getFormattedFinalPriceAttribute()
    {
        return 'Rp ' . number_format($this->final_price, 0, ',', '.');
    }

    /**
     * Check if product has discount
     */
    public function getHasDiscountAttribute()
    {
        return !is_null($this->discount_price) && $this->discount_price < $this->price;
    }

    /**
     * Get discount percentage
     */
    public function getDiscountPercentageAttribute()
    {
        if (!$this->has_discount) {
            return 0;
        }
        return round((($this->price - $this->discount_price) / $this->price) * 100);
    }

    /**
     * Get average rating
     */
    public function getAverageRatingAttribute()
    {
        return $this->reviews()->approved()->avg('rating') ?? 0;
    }

    /**
     * Get review count
     */
    public function getReviewCountAttribute()
    {
        return $this->reviews()->approved()->count();
    }

    /**
     * Check if product is in stock
     */
    public function getIsInStockAttribute()
    {
        return $this->stock && $this->stock->quantity > 0;
    }

    /**
     * Get stock quantity
     */
    public function getStockQuantityAttribute()
    {
        return $this->stock ? $this->stock->quantity : 0;
    }

    /**
     * Get the main image URL
     */
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return Storage::url($this->image);
        }
        return asset('images/default-product.png');
    }

    /**
     * Get gallery image URLs
     */
    public function getGalleryUrlsAttribute()
    {
        if (!$this->gallery) {
            return [];
        }

        return collect($this->gallery)->map(function ($image) {
            return Storage::url($image);
        })->toArray();
    }

    /**
     * Apply promo code to product
     */
    public function applyPromo($promoCode)
    {
        $promo = Promo::where('code', $promoCode)
            ->active()
            ->available()
            ->first();

        if (!$promo) {
            return $this->final_price;
        }

        // Check if promo is product-specific
        if ($promo->products()->count() > 0) {
            if (!$promo->products->contains($this->id)) {
                return $this->final_price;
            }
        }

        return $promo->calculateDiscount($this->final_price);
    }
}
