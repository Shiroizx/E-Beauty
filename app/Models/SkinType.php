<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SkinType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
    ];

    /**
     * Get products suitable for this skin type
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_skin_types');
    }

    /**
     * Get the full URL for the skin type icon
     */
    public function getIconUrlAttribute()
    {
        if ($this->icon) {
            return Storage::url($this->icon);
        }
        return asset('images/default-skin-type.png');
    }

    /**
     * Get product count for this skin type
     */
    public function getProductCountAttribute()
    {
        return $this->products()->count();
    }
}
