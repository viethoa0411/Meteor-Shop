<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'discount_type',
        'discount_value',
        'max_discount',
        'min_amount',
        'min_orders',
        'start_date',
        'end_date',
        'limit_per_user',
        'limit_global',
        'used_count',
        'status',
        'scope',

        'start_date',
        'end_date',
        'usage_limit',
        'used_count',
        'status',
        'apply_for_returning_customers',
        'min_orders_for_applying',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'min_amount' => 'decimal:2',

        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'apply_for_returning_customers' => 'boolean',
        'min_orders_for_applying' => 'integer',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
    }

    public function scopeAvailable($query)
    {
        return $query->active()
                    ->where(function($q) {
                        $q->whereNull('limit_global')
                          ->orWhereRaw('used_count < limit_global');

                    });
    }

    /**
     * Lấy promotion áp dụng cho khách quay lại (đơn hàng thứ 2 trở lên).
     */
    public function scopeForReturningCustomers($query, int $orderCount)
    {
        return $query->available()
            ->where('apply_for_returning_customers', true)
            ->where('min_orders_for_applying', '<=', $orderCount)
            ->orderByDesc('discount_value');
    }

    // Accessors
    public function getFormattedDiscountValueAttribute()
    {
        if ($this->discount_type === 'percent') {
            return $this->discount_value . '%';
        }
        return number_format($this->discount_value, 0, ',', '.') . ' VNĐ';
    }

    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'active' => 'bg-success',
            'expired' => 'bg-danger',
            'inactive' => 'bg-secondary',
            default => 'bg-light'
        };
    }

    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'active' => 'Hoạt động',
            'expired' => 'Hết hạn',
            'inactive' => 'Không hoạt động',
            default => 'Không xác định'
        };
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'promotion_categories', 'promotion_id', 'category_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'promotion_products', 'promotion_id', 'product_id');
    }

    public function userUsages()
    {
        return $this->hasMany(UserPromotionUsage::class, 'promotion_id');
    }

}
