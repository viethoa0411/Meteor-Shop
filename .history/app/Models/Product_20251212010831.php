<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Wishlist; 



class Product extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'sku',
        'description',
        'short_description',
        'price',
        'sale_price',
        'description',
        'price',
        'stock',
        'image',
        'length',
        'width',
        'height',
        'color_code',
        'category_id',
        'status',
        'rating_avg',
        'total_sold',
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
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Tính giá hiển thị (ưu tiên sale_price nếu có)
     */
    public function getDisplayPriceAttribute()
    {
        return $this->sale_price ?? $this->price;
    }

    /**
     * Tính % giảm giá
     */
    public function getDiscountPercentAttribute()
    {
        if (!$this->sale_price || $this->sale_price >= $this->price) {
            return 0;
        }
        return round((($this->price - $this->sale_price) / $this->price) * 100);
    }

    /**
     * Kiểm tra còn hàng
     */
    public function getInStockAttribute()
    {
        if ($this->variants->count() > 0) {
            return $this->variants->sum('stock') > 0;
        }
        return ($this->stock ?? 0) > 0;
    }

    public function orderDetails()
    {
        return $this->hasMany(\App\Models\OrderDetail::class, 'product_id');
    }

    public function hasOrders()
    {
        return $this->orderDetails()->exists();
    }

}
