<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'promotion_id',
        'order_code',
        'total_price',
        'discount_amount',
        'final_total',
        'payment_method',
        'payment_status',
        'order_status',
        'shipping_address',
        'shipping_phone',
        'shipping_fee',
        'notes',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_total' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function promotion(): BelongsTo
    {
        return $this->belongsTo(Promotion::class);
    }

    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('order_status', $status);
    }

    public function scopeByPaymentStatus($query, $status)
    {
        return $query->where('payment_status', $status);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function scopeByTotalRange($query, $minTotal, $maxTotal)
    {
        return $query->whereBetween('final_total', [$minTotal, $maxTotal]);
    }

    // Accessors
    public function getFormattedTotalPriceAttribute()
    {
        return number_format($this->total_price, 0, ',', '.') . ' VNĐ';
    }

    public function getFormattedFinalTotalAttribute()
    {
        return number_format($this->final_total, 0, ',', '.') . ' VNĐ';
    }

    public function getFormattedDiscountAmountAttribute()
    {
        return number_format($this->discount_amount, 0, ',', '.') . ' VNĐ';
    }

    public function getFormattedShippingFeeAttribute()
    {
        return number_format($this->shipping_fee ?? 0, 0, ',', '.') . ' VNĐ';
    }

    public function getStatusBadgeClassAttribute()
    {
        return match($this->order_status) {
            'pending' => 'bg-warning',
            'processing' => 'bg-info',
            'completed' => 'bg-success',
            'cancelled' => 'bg-danger',
            'refunded' => 'bg-secondary',
            default => 'bg-light'
        };
    }

    public function getPaymentStatusBadgeClassAttribute()
    {
        return match($this->payment_status) {
            'pending' => 'bg-warning',
            'paid' => 'bg-success',
            'failed' => 'bg-danger',
            default => 'bg-light'
        };
    }

    public function getStatusTextAttribute()
    {
        return match($this->order_status) {
            'pending' => 'Chờ xử lý',
            'processing' => 'Đang xử lý',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Đã hủy',
            'refunded' => 'Đã hoàn tiền',
            default => 'Không xác định'
        };
    }

    public function getPaymentStatusTextAttribute()
    {
        return match($this->payment_status) {
            'pending' => 'Chờ thanh toán',
            'paid' => 'Đã thanh toán',
            'failed' => 'Thanh toán thất bại',
            default => 'Không xác định'
        };
    }

    public function getPaymentMethodTextAttribute()
    {
        return match($this->payment_method) {
            'cash' => 'Tiền mặt',
            'bank' => 'Chuyển khoản',
            'momo' => 'MoMo',
            'paypal' => 'PayPal',
            default => 'Không xác định'
        };
    }
}
