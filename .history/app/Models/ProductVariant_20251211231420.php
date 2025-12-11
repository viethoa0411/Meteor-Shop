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

     public function setWeightAttribute($value)
    {
        $unit = $this->attributes['weight_unit'] ?? 'kg';
        if (is_null($value)) {
            $this->attributes['weight'] = null;
            return;
        }

        // Nếu muốn chuẩn hoá: từ g -> kg, lb -> kg
        if ($unit === 'g') {
            $this->attributes['weight'] = $value / 1000;
        } elseif ($unit === 'lb') {
            $this->attributes['weight'] = $value * 0.45359237;
            $this->attributes['weight_unit'] = 'kg'; // lưu nội bộ bằng kg
        } else {
            $this->attributes['weight'] = $value;
        }
    }

}
