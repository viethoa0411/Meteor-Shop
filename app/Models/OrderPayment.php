<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'transaction_id',
        'payment_method',
        'status',
        'amount',
        'refunded_amount',
        'currency',
        'payment_data',
        'notes',
        'paid_at',
        'failed_at',
        'processed_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'refunded_amount' => 'decimal:2',
        'payment_data' => 'array',
        'paid_at' => 'datetime',
        'failed_at' => 'datetime',
    ];

    public const STATUS_META = [
        'pending' => ['label' => 'Chờ xử lý', 'badge' => 'warning'],
        'processing' => ['label' => 'Đang xử lý', 'badge' => 'info'],
        'paid' => ['label' => 'Đã thanh toán', 'badge' => 'success'],
        'failed' => ['label' => 'Thất bại', 'badge' => 'danger'],
        'refunded' => ['label' => 'Đã hoàn tiền', 'badge' => 'secondary'],
        'partially_refunded' => ['label' => 'Hoàn tiền một phần', 'badge' => 'info'],
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(OrderRefund::class, 'order_payment_id');
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function getStatusMetaAttribute(): array
    {
        return self::STATUS_META[$this->status] ?? ['label' => ucfirst($this->status), 'badge' => 'secondary'];
    }

    public function canRefund(): bool
    {
        return $this->status === 'paid' && $this->refunded_amount < $this->amount;
    }

    public function getRefundableAmountAttribute(): float
    {
        return max(0, $this->amount - $this->refunded_amount);
    }
}

