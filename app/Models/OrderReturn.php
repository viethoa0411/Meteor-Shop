<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'return_code',
        'type',
        'status',
        'reason',
        'description',
        'product_condition',
        'attachments',
        'admin_notes',
        'resolution',
        'exchange_product_id',
        'requested_at',
        'approved_at',
        'rejected_at',
        'received_at',
        'processed_at',
        'processed_by',
    ];

    protected $casts = [
        'attachments' => 'array',
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'received_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    public const STATUS_META = [
        'requested' => ['label' => 'Đã yêu cầu', 'badge' => 'warning'],
        'approved' => ['label' => 'Đã duyệt', 'badge' => 'success'],
        'rejected' => ['label' => 'Đã từ chối', 'badge' => 'danger'],
        'in_transit' => ['label' => 'Đang vận chuyển', 'badge' => 'info'],
        'received' => ['label' => 'Đã nhận', 'badge' => 'primary'],
        'processed' => ['label' => 'Đã xử lý', 'badge' => 'info'],
        'completed' => ['label' => 'Hoàn thành', 'badge' => 'success'],
        'cancelled' => ['label' => 'Đã hủy', 'badge' => 'secondary'],
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderReturnItem::class, 'return_id');
    }

    public function exchangeProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'exchange_product_id');
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

