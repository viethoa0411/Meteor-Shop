<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * Model Yêu cầu nạp tiền
 * 
 * Luồng hoạt động:
 * 1. Khách tạo yêu cầu nạp tiền -> status: pending
 * 2. Khách chuyển khoản theo thông tin QR
 * 3. Admin kiểm tra và xác nhận -> status: confirmed
 * 4. Tiền được cộng vào ví khách
 */
class DepositRequest extends Model
{
    protected $fillable = [
        'user_id',
        'wallet_id',
        'amount',
        'confirmed_amount',
        'request_code',
        'status',
        'note',
        'admin_note',
        'confirmed_by',
        'confirmed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'confirmed_amount' => 'decimal:2',
        'confirmed_at' => 'datetime',
    ];

    // ==================== CONSTANTS ====================

    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_REJECTED = 'rejected';
    const STATUS_CANCELLED = 'cancelled';

    const STATUS_LABELS = [
        'pending' => 'Chờ xác nhận',
        'confirmed' => 'Đã xác nhận',
        'rejected' => 'Từ chối',
        'cancelled' => 'Đã hủy',
    ];

    // ==================== RELATIONSHIPS ====================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(ClientWallet::class, 'wallet_id');
    }

    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    // ==================== ACCESSORS ====================

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 0, ',', '.') . ' đ';
    }

    public function getFormattedConfirmedAmountAttribute(): string
    {
        return $this->confirmed_amount 
            ? number_format($this->confirmed_amount, 0, ',', '.') . ' đ' 
            : '-';
    }

    // ==================== METHODS ====================

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isConfirmed(): bool
    {
        return $this->status === self::STATUS_CONFIRMED;
    }

    // ==================== BOOT ====================

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->request_code)) {
                $model->request_code = 'DEP_' . strtoupper(Str::random(10));
            }
        });
    }
}

