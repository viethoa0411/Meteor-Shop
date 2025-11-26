<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    protected $fillable = [
        'order_id',
        'wallet_id',
        'refund_id',
        'amount',
        'type',
        'status',
        'payment_method',
        'transaction_code',
        'qr_code_url',
        'description',
        'completed_at',
        'processed_by',
        'marked_as_received_by',
        'marked_as_received_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'completed_at' => 'datetime',
        'marked_as_received_at' => 'datetime',
    ];

    /**
     * Relationship: Giao dịch thuộc về một đơn hàng
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relationship: Giao dịch thuộc về một ví
     */
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * Relationship: Giao dịch thuộc về một yêu cầu hoàn tiền
     */
    public function refund(): BelongsTo
    {
        return $this->belongsTo(Refund::class);
    }

    /**
     * Relationship: Admin xử lý giao dịch
     */
    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Relationship: Người đánh dấu đã nhận
     */
    public function marker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'marked_as_received_by');
    }

    /**
     * Relationship: Lịch sử hành động trên giao dịch
     */
    public function logs(): HasMany
    {
        return $this->hasMany(TransactionLog::class)->orderBy('created_at', 'desc');
    }

    /**
     * Format số tiền
     */
    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 0, ',', '.') . ' VNĐ';
    }

    /**
     * Scope: Lọc theo trạng thái
     */
    public function scopeStatus($query, $status)
    {
        if ($status && $status !== 'all') {
            return $query->where('status', $status);
        }
        return $query;
    }

    /**
     * Scope: Lọc theo loại giao dịch
     */
    public function scopeType($query, $type)
    {
        if ($type && $type !== 'all') {
            return $query->where('type', $type);
        }
        return $query;
    }

    /**
     * Đánh dấu giao dịch hoàn thành
     */
    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }
}
