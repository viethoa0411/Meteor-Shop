<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'user_id',
        'promotion_id',
        'order_code',
        'total_price',
        'discount_amount',
        'final_total',
        'sub_total',
        'payment_method',
        'payment_status',
        'order_status',
        'shipping_address',
        'shipping_phone',
        'shipping_method',
        'shipping_fee',
        'voucher_code',
        'shipping_city',
        'shipping_district',
        'shipping_ward',
        'customer_name',
        'customer_phone',
        'customer_email',
        'tracking_code',
        'tracking_url',
        'shipping_provider',
        'cancel_reason',
        'notes',
        'return_status',
        'return_reason',
        'return_note',
        'return_attachments',
        'order_date',
        'confirmed_at',
        'packed_at',
        'shipped_at',
        'delivered_at',
        'cancelled_at',
        'refunded_at',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_total' => 'decimal:2',
        'sub_total' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'return_attachments' => 'array',
        'order_date' => 'datetime',
        'confirmed_at' => 'datetime',
        'packed_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    public const STATUS_META = [
        'pending' => ['label' => 'Chờ xác nhận', 'badge' => 'warning', 'icon' => 'bi-hourglass-split'],
        'processing' => ['label' => 'Chuẩn bị hàng', 'badge' => 'info', 'icon' => 'bi-box'],
        'shipping' => ['label' => 'Đang giao', 'badge' => 'primary', 'icon' => 'bi-truck'],
        'delivered' => ['label' => 'Đã giao', 'badge' => 'success', 'icon' => 'bi-box-seam'],
        'completed' => ['label' => 'Hoàn thành', 'badge' => 'success', 'icon' => 'bi-check-circle'],
        'cancelled' => ['label' => 'Đã hủy', 'badge' => 'danger', 'icon' => 'bi-x-circle'],
        'return_requested' => ['label' => 'Yêu cầu đổi trả', 'badge' => 'secondary', 'icon' => 'bi-arrow-repeat'],
        'returned' => ['label' => 'Đã đổi trả', 'badge' => 'secondary', 'icon' => 'bi-arrow-counterclockwise'],
    ];

    public const PAYMENT_LABELS = [
        'cash' => 'Thanh toán COD',
        'bank' => 'Chuyển khoản ngân hàng',
        'momo' => 'Ví Momo',
        'paypal' => 'Paypal',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
public function items(): HasMany
    {
        return $this->hasMany(OrderDetail::class, 'order_id')->orderBy('id');
    }

    public function scopeOwnedBy($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeStatus($query, ?string $status)
    {
        if ($status && $status !== 'all') {
            if ($status === 'returned') {
                return $query->whereIn('order_status', ['return_requested', 'returned']);
            }

            return $query->where('order_status', $status);
        }

        return $query;
    }

    public function getStatusMetaAttribute(): array
    {
        return self::STATUS_META[$this->order_status] ?? ['label' => ucfirst($this->order_status), 'badge' => 'secondary', 'icon' => 'bi-question-circle'];
    }

    protected function statusLabel(): Attribute
    {
        return Attribute::get(fn () => $this->status_meta['label']);
    }

    protected function statusBadge(): Attribute
    {
        return Attribute::get(fn() => $this->status_meta['badge']);
    }

    protected function statusIcon(): Attribute
    {
        return Attribute::get(fn() => $this->status_meta['icon']);
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(OrderRefund::class, 'order_id');
    }

    public function getPaymentLabelAttribute(): string
    {
        return self::PAYMENT_LABELS[$this->payment_method] ?? strtoupper($this->payment_method);
    }

    public function getDisplayOrderDateAttribute()
    {
        return $this->order_date ?? $this->created_at;
    }

    public function canCancel(): bool
    {
        return $this->order_status === 'pending';
    }

    public function canTrack(): bool
    {
        return in_array($this->order_status, ['shipping', 'processing']);
    }
    public function canReceive(): bool
    {
        return $this->order_status === 'delivered';
    }

    public function getReturnDaysRemaining(): ?int
    {
        if ($this->order_status !== 'completed' || !$this->delivered_at) {
            return null;
        }

        $daysSinceDelivery = now()->diffInDays($this->delivered_at);
        $remaining = 7 - $daysSinceDelivery;

        return $remaining > 0 ? $remaining : 0;
    }


    public function canReorder(): bool
    {
        return in_array($this->order_status, ['completed', 'cancelled']);
    }

    public function canReview(): bool
    {
        return $this->order_status === 'completed';
    }

    public function canReturn(): bool
    {
        return $this->order_status === 'completed' && $this->return_status === 'none';
    }

    public function isReturnExpired(): bool
    {
        if ($this->order_status !== 'completed' || !$this->delivered_at) {
            return false;
        }

        return now()->diffInDays($this->delivered_at) >= 7;
    }

    public function canReturnRefund(): bool
    {
return $this->order_status === 'completed';
    }

    public function canCancelRefund(): bool
    {
        // Chỉ cho phép khi đơn hàng ở trạng thái pending hoặc processing
        if (!in_array($this->order_status, ['pending', 'processing'])) {
            return false;
        }

        // Chỉ áp dụng cho thanh toán online (bank, momo) - không cho hủy nếu đã xác nhận
        if (in_array($this->payment_method, ['bank', 'momo']) && $this->payment_status === 'paid') {
            return false;
        }

        // Thanh toán bằng wallet đã trừ tiền - không cho hủy nếu đã thanh toán
        if ($this->payment_method === 'wallet' && $this->payment_status === 'paid') {
            return false;
        }

        return true;
    }

    /**
     * Relationship với WalletTransaction (nếu thanh toán bằng ví)
     */
    public function walletTransactions()
    {
        return $this->hasMany(\App\Models\WalletTransaction::class);
    }
}
>>>>>>> fd7b4da683f7cef1efaff108fd509ead4ee20159
