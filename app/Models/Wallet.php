<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    protected $fillable = [
        'user_id',
        'balance',
        'bank_name',
        'bank_account',
        'account_holder',
        'qr_code_template',
        'status',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
    ];

    /**
     * Relationship: Ví thuộc về một user (admin)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: Ví có nhiều giao dịch
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Relationship: Ví có nhiều yêu cầu rút tiền
     */
    public function withdrawals(): HasMany
    {
        return $this->hasMany(WalletWithdrawal::class);
    }

    /**
     * Thêm tiền vào ví
     */
    public function addBalance($amount)
    {
        $this->increment('balance', $amount);
        return $this;
    }

    /**
     * Trừ tiền khỏi ví
     */
    public function subtractBalance($amount)
    {
        if ($this->balance >= $amount) {
            $this->decrement('balance', $amount);
            return true;
        }
        return false;
    }

    /**
     * Format số dư
     */
    public function getFormattedBalanceAttribute()
    {
        return number_format($this->balance, 0, ',', '.') . ' VNĐ';
    }
}
