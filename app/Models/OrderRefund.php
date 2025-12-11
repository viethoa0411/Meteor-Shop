<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderRefund extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'order_payment_id',
        'refund_code',
        'type',
        'status',
        'amount',
        'currency',
        'reason',
        'notes',
        'refund_data',
        'refund_transaction_id',
        'refunded_at',
        'processed_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'refund_data' => 'array',
        'refunded_at' => 'datetime',
    ];

    public const STATUS_META = [
        'pending' => ['label' => 'Chờ xử lý', 'badge' => 'warning'],
        'processing' => ['label' => 'Đang xử lý', 'badge' => 'info'],
        'completed' => ['label' => 'Hoàn thành', 'badge' => 'success'],
        'failed' => ['label' => 'Thất bại', 'badge' => 'danger'],
        'cancelled' => ['label' => 'Đã hủy', 'badge' => 'secondary'],
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(OrderPayment::class, 'order_payment_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderRefundItem::class, 'refund_id');
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function getStatusMetaAttribute(): array
    {
        return self::STATUS_META[$this->status] ?? ['label' => ucfirst($this->status), 'badge' => 'secondary'];
    }
}

