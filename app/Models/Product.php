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
        'weight',
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
     * Kiểm tra còn hàng
     */

    public function orderDetails()
    {
        return $this->hasMany(\App\Models\OrderDetail::class, 'product_id');
    }

    public function hasOrders()
    {
        return $this->orderDetails()->exists();
    }

    /**
     * Tính giá hiển thị (ưu tiên sale_price nếu có)
     */

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
    
    protected static function booted()
    {
        static::created(function ($product) {
            // Khi thêm sản phẩm mới → đặt sort_order = 1 (lên đầu)
            // Các sản phẩm cũ tăng sort_order lên 1
            \DB::transaction(function () use ($product) {
                \DB::table('products')
                    ->where('id', '!=', $product->id)
                    ->increment('sort_order');

                $product->sort_order = 1;
                $product->saveQuietly();
            });
        });

        static::deleted(function ($product) {
            // Khi xóa → giảm sort_order của các sản phẩm có sort_order lớn hơn
            \DB::table('products')
                ->where('sort_order', '>', $product->sort_order)
                ->decrement('sort_order');
        });
    }
}
