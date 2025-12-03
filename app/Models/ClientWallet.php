<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model Ví của Client
 * 
 * Luồng hoạt động:
 * 1. Mỗi user client có 1 ví duy nhất
 * 2. Ví lưu số dư hiện tại
 * 3. Có thể nạp/rút tiền từ ví
 * 4. Dùng ví để thanh toán online
 */
class ClientWallet extends Model
{
    protected $fillable = [
        'user_id',
        'balance',
        'status',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
    ];

    // ==================== RELATIONSHIPS ====================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class, 'wallet_id');
    }

    public function depositRequests(): HasMany
    {
        return $this->hasMany(DepositRequest::class, 'wallet_id');
    }

    public function withdrawRequests(): HasMany
    {
        return $this->hasMany(WithdrawRequest::class, 'wallet_id');
    }

    // ==================== METHODS ====================

    /**
     * Cộng tiền vào ví
     */
    public function addBalance(float $amount): bool
    {
        if ($amount <= 0) return false;
        $this->balance += $amount;
        return $this->save();
    }

    /**
     * Trừ tiền từ ví
     */
    public function subtractBalance(float $amount): bool
    {
        if ($amount <= 0 || $amount > $this->balance) return false;
        $this->balance -= $amount;
        return $this->save();
    }

    /**
     * Kiểm tra có đủ tiền không
     */
    public function hasEnoughBalance(float $amount): bool
    {
        return $this->balance >= $amount;
    }

    /**
     * Lấy số dư formatted
     */
    public function getFormattedBalanceAttribute(): string
    {
        return number_format($this->balance, 0, ',', '.') . ' đ';
    }

    /**
     * Kiểm tra ví có active không
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    // ==================== STATIC METHODS ====================

    /**
     * Lấy hoặc tạo ví cho user
     */
    public static function getOrCreateForUser(int $userId): self
    {
        return self::firstOrCreate(
            ['user_id' => $userId],
            ['balance' => 0, 'status' => 'active']
        );
    }
}

