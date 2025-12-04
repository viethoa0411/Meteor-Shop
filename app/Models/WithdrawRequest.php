<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * Model Yêu cầu rút tiền
 * 
 * Luồng hoạt động:
 * 1. Khách tạo yêu cầu rút tiền với thông tin ngân hàng -> status: pending
 * 2. Admin nhận được thông báo qua email
 * 3. Admin xác nhận và chuyển tiền cho khách -> status: completed
 * 4. Tiền được trừ từ ví khách
 */
class WithdrawRequest extends Model
{
    protected $fillable = [
        'user_id',
        'wallet_id',
        'amount',
        'confirmed_amount',
        'request_code',
        'bank_name',
        'account_number',
        'account_holder',
        'phone',
        'status',
        'note',
        'admin_note',
        'processed_by',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'confirmed_amount' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    // ==================== CONSTANTS ====================

    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_REJECTED = 'rejected';
    const STATUS_CANCELLED = 'cancelled';

    const STATUS_LABELS = [
        'pending' => 'Chờ xử lý',
        'processing' => 'Đang xử lý',
        'completed' => 'Hoàn thành',
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

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
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

    public function getBankInfoAttribute(): string
    {
        return "{$this->bank_name} - {$this->account_number} - {$this->account_holder}";
    }

    // ==================== METHODS ====================

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    // ==================== BOOT ====================

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->request_code)) {
                $model->request_code = 'WD_' . strtoupper(Str::random(10));
            }
        });
    }
}

