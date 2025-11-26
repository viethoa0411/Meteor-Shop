<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
<<<<<<< HEAD
        return $this->belongsTo(Category::class, 'category_id');
    }
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
=======
        return $this->belongsTo(Category::class);
    }
>>>>>>> eca3fb6387947a26f91d698ae62b346887ad3fab

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }
<<<<<<< HEAD
=======

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }
>>>>>>> eca3fb6387947a26f91d698ae62b346887ad3fab
}
