<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * Model Lịch sử giao dịch ví
 * 
 * Các loại giao dịch:
 * - deposit: Nạp tiền vào ví
 * - withdraw: Rút tiền từ ví
 * - payment: Thanh toán đơn hàng
 * - refund: Hoàn tiền khi hủy đơn
 * - cashback: Tiền về từ đơn hoàn thành
 */
class WalletTransaction extends Model
{
    protected $fillable = [
        'wallet_id',
        'user_id',
        'type',
        'amount',
        'balance_before',
        'balance_after',
        'transaction_code',
        'description',
        'order_id',
        'deposit_request_id',
        'withdraw_request_id',
        'processed_by',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    // ==================== CONSTANTS ====================

    const TYPE_DEPOSIT = 'deposit';
    const TYPE_WITHDRAW = 'withdraw';
    const TYPE_PAYMENT = 'payment';
    const TYPE_REFUND = 'refund';
    const TYPE_CASHBACK = 'cashback';

    const TYPE_LABELS = [
        'deposit' => 'Nạp tiền',
        'withdraw' => 'Rút tiền',
        'payment' => 'Thanh toán',
        'refund' => 'Hoàn tiền',
        'cashback' => 'Hoàn thành đơn',
    ];

    // ==================== RELATIONSHIPS ====================

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(ClientWallet::class, 'wallet_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function depositRequest(): BelongsTo
    {
        return $this->belongsTo(DepositRequest::class);
    }

    public function withdrawRequest(): BelongsTo
    {
        return $this->belongsTo(WithdrawRequest::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    // ==================== ACCESSORS ====================

    public function getTypeLabelAttribute(): string
    {
        return self::TYPE_LABELS[$this->type] ?? $this->type;
    }

    public function getFormattedAmountAttribute(): string
    {
        $prefix = $this->isCredit() ? '+' : '-';
        return $prefix . number_format($this->amount, 0, ',', '.') . ' đ';
    }

    public function getAmountColorAttribute(): string
    {
        return $this->isCredit() ? 'text-success' : 'text-danger';
    }

    // ==================== METHODS ====================

    /**
     * Giao dịch cộng tiền (deposit, refund, cashback)
     */
    public function isCredit(): bool
    {
        return in_array($this->type, [self::TYPE_DEPOSIT, self::TYPE_REFUND, self::TYPE_CASHBACK]);
    }

    /**
     * Giao dịch trừ tiền (withdraw, payment)
     */
    public function isDebit(): bool
    {
        return in_array($this->type, [self::TYPE_WITHDRAW, self::TYPE_PAYMENT]);
    }

    // ==================== BOOT ====================

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->transaction_code)) {
                $model->transaction_code = 'TXN_' . strtoupper(Str::random(12));
            }
        });
    }
}

