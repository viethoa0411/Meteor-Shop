<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'color_name',
        'color_code',
        'length',
        'width',
        'height',
        'price',
        'stock',
        'sku',
    ];

    protected $casts = [
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'price' => 'decimal:2',
        'stock' => 'integer',
    ];

    /**
     * Quan hệ với sản phẩm
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
