<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'product_version',
        'color_name',
        'color_code',
        'length',
        'width',
        'height',
        'price',
        'stock',
        'sku',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
    public function orderItems()
    {
        return $this->hasMany(OrderDetail::class, 'variant_id', 'id');
    }

    public function hasOrders()
    {
        return $this->orderItems()->exists();
    }

}
