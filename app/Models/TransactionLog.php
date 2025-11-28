<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionLog extends Model
{
    protected $fillable = [
        'transaction_id',
        'user_id',
        'action',
        'description',
        'old_data',
        'new_data',
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
    ];

    /**
     * Relationship: Log thuộc về một giao dịch
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Relationship: Log thuộc về một người dùng
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Lấy label hành động
     */
    public function getActionLabelAttribute(): string
    {
        return match($this->action) {
            'confirm' => 'Xác nhận giao dịch',
            'cancel' => 'Hủy giao dịch',
            'refund' => 'Hoàn tiền',
            'withdraw' => 'Rút tiền',
            'mark_received' => 'Đánh dấu đã nhận',
            'settle_received' => 'Chốt giao dịch',
            'unmark_received' => 'Hoàn tác đánh dấu',
            'update' => 'Cập nhật',
            default => 'Không xác định',
        };
    }
}

