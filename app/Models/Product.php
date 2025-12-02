<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Wishlist; 

class Product extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'image',
        'length',
        'width',
        'height',
        'color_code',
        'category_id',
        'brand_id',
        'status'
    ];

    // Quan hệ
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }
     public function wishlists()
    {
        return $this->hasMany(Wishlist::class, 'product_id');
    }
}
