<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Collection extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'status',
        'sort_order',
    ];

    /**
     * Scope để lấy collections active
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Lấy URL hình ảnh
     */
    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return asset('images/placeholder.jpg');
        }

        $imagePath = ltrim($this->image, '/');
        return asset('storage/' . $imagePath);
    }

    /**
     * Quan hệ với Products (many-to-many)
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'collection_product', 'collection_id', 'product_id')
            ->withTimestamps();
    }
}
