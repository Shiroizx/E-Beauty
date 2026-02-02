<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'description',
        'parent_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get products in this category
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get the parent category
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Get child categories (subcategories)
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Scope to get only active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get only root categories (no parent)
     */
    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope to get categories with children
     */
    public function scopeWithChildren($query)
    {
        return $query->with('children');
    }

    /**
     * Get the full URL for the category icon
     */
    public function getIconUrlAttribute()
    {
        if ($this->icon) {
            return Storage::url($this->icon);
        }
        return asset('images/default-category.png');
    }

    /**
     * Get hierarchical name (Parent > Child)
     */
    public function getFullNameAttribute()
    {
        if ($this->parent) {
            return $this->parent->name . ' > ' . $this->name;
        }
        return $this->name;
    }

    /**
     * Check if category has parent
     */
    public function getIsParentAttribute()
    {
        return is_null($this->parent_id);
    }

    /**
     * Get product count for this category
     */
    public function getProductCountAttribute()
    {
        return $this->products()->count();
    }
}
