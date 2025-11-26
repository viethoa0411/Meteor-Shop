<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Refund extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'refund_type',
        'cancel_reason',
        'reason_description',
        'refund_amount',
        'bank_name',
        'bank_account',
        'account_holder',
        'status',
        'admin_note',
        'processed_by',
        'processed_at',
    ];

    protected $casts = [
        'refund_amount' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    /**
     * Relationship: Hoàn tiền thuộc về một đơn hàng
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relationship: Hoàn tiền thuộc về một khách hàng
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: Admin xử lý hoàn tiền
     */
    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Format số tiền hoàn
     */
    public function getFormattedAmountAttribute()
    {
        return number_format($this->refund_amount, 0, ',', '.') . ' VNĐ';
    }

    /**
     * Lấy label trạng thái
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Chờ xử lý',
            'approved' => 'Đã duyệt',
            'rejected' => 'Từ chối',
            'completed' => 'Hoàn thành',
            default => 'Không xác định',
        };
    }

    /**
     * Lấy label loại hoàn tiền
     */
    public function getRefundTypeLabelAttribute(): string
    {
        return match($this->refund_type) {
            'cancel' => 'Hủy đơn hàng',
            'return' => 'Trả hàng',
            default => 'Không xác định',
        };
    }
}

