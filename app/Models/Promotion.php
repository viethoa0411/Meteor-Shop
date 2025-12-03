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
                        $q->whereNull('usage_limit')
                          ->orWhereRaw('used_count < usage_limit');
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
}
