<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;

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
        'pending' => ['label' => 'Chờ xử lý', 'badge' => 'warning', 'icon' => 'bi-hourglass-split', 'color' => '#ffc107'],
        'awaiting_payment' => ['label' => 'Chờ thanh toán', 'badge' => 'warning', 'icon' => 'bi-credit-card', 'color' => '#ff9800'],
        'paid' => ['label' => 'Đã thanh toán', 'badge' => 'success', 'icon' => 'bi-check-circle', 'color' => '#28a745'],
        'processing' => ['label' => 'Đang xử lý', 'badge' => 'info', 'icon' => 'bi-gear', 'color' => '#17a2b8'],
        'confirmed' => ['label' => 'Đã xác nhận', 'badge' => 'info', 'icon' => 'bi-check-circle', 'color' => '#17a2b8'],
        'packed' => ['label' => 'Đã đóng gói', 'badge' => 'info', 'icon' => 'bi-box', 'color' => '#17a2b8'],
        'shipping' => ['label' => 'Đang giao hàng', 'badge' => 'primary', 'icon' => 'bi-truck', 'color' => '#007bff'],
        'delivered' => ['label' => 'Giao thành công', 'badge' => 'success', 'icon' => 'bi-check-circle', 'color' => '#28a745'],
        'completed' => ['label' => 'Hoàn thành', 'badge' => 'success', 'icon' => 'bi-check-circle-fill', 'color' => '#28a745'],
        'cancelled' => ['label' => 'Đã hủy', 'badge' => 'danger', 'icon' => 'bi-x-circle', 'color' => '#dc3545'],
        'return_requested' => ['label' => 'Yêu cầu trả hàng', 'badge' => 'warning', 'icon' => 'bi-arrow-return-left', 'color' => '#ffc107'],
        'returned' => ['label' => 'Trả hàng', 'badge' => 'secondary', 'icon' => 'bi-arrow-counterclockwise', 'color' => '#6c757d'],
        'refunded' => ['label' => 'Đã hoàn tiền', 'badge' => 'warning', 'icon' => 'bi-arrow-counterclockwise', 'color' => '#ffc107'],
        'partial_refund' => ['label' => 'Hoàn tiền một phần', 'badge' => 'info', 'icon' => 'bi-currency-dollar', 'color' => '#17a2b8'],
    ];

    /**
     * 状态转换规则
     */
    public const STATUS_TRANSITIONS = [
        'pending' => ['awaiting_payment', 'paid', 'processing', 'cancelled'],
        'awaiting_payment' => ['paid', 'cancelled'],
        'paid' => ['processing', 'cancelled'],
        'processing' => ['packed', 'cancelled'],
        'confirmed' => ['packed', 'cancelled'],
        'packed' => ['shipping', 'cancelled'],
        'shipping' => ['delivered', 'return_requested', 'cancelled'],
        'delivered' => ['completed', 'return_requested'],
        'completed' => ['return_requested'],
        'return_requested' => ['returned', 'cancelled'],
        'returned' => ['refunded', 'partial_refund'],
        'cancelled' => [],
        'refunded' => [],
        'partial_refund' => [],
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

    public function payments(): HasMany
    {
        return $this->hasMany(OrderPayment::class);
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(OrderShipment::class);
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(OrderRefund::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(OrderNote::class)->orderBy('is_pinned', 'desc')->orderBy('created_at', 'desc');
    }

    public function timelines(): HasMany
    {
        return $this->hasMany(OrderTimeline::class)->orderBy('created_at', 'desc');
    }

    public function latestPayment(): HasOne
    {
        return $this->hasOne(OrderPayment::class)->latestOfMany();
    }

    public function latestShipment(): HasOne
    {
        return $this->hasOne(OrderShipment::class)->latestOfMany();
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
        return Attribute::get(fn () => $this->status_meta['badge']);
    }

    protected function statusIcon(): Attribute
    {
        return Attribute::get(fn () => $this->status_meta['icon']);
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
        return $this->order_status === 'shipping';
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
        // Kiểm tra trạng thái đơn hàng
        if ($this->order_status !== 'completed') {
            return false;
        }

        // Kiểm tra return_status
        if (!in_array($this->return_status, ['none', 'rejected'])) {
            return false;
        }

        // Kiểm tra đã nhận hàng chưa (delivered_at phải có giá trị)
        if (!$this->delivered_at) {
            return false;
        }

        // Kiểm tra trong vòng 7 ngày kể từ khi nhận hàng
        $daysSinceDelivery = now()->diffInDays($this->delivered_at);
        if ($daysSinceDelivery > 7) {
            return false;
        }

        return true;
    }

    /**
     * Kiểm tra xem đơn hàng có quá hạn để hoàn hàng không (quá 7 ngày)
     */
    public function isReturnExpired(): bool
    {
        if ($this->order_status !== 'completed' || !$this->delivered_at) {
            return false;
        }

        $daysSinceDelivery = now()->diffInDays($this->delivered_at);
        return $daysSinceDelivery > 7;
    }

    /**
     * Lấy số ngày còn lại để có thể hoàn hàng
     */
    public function getReturnDaysRemaining(): ?int
    {
        if ($this->order_status !== 'completed' || !$this->delivered_at) {
            return null;
        }

        $daysSinceDelivery = now()->diffInDays($this->delivered_at);
        $remaining = 7 - $daysSinceDelivery;

        return $remaining > 0 ? $remaining : 0;
    }

    /**
     * Get fulfillment status (packed, shipped, delivered)
     */
    public function getFulfillmentStatusAttribute(): string
    {

        if ($this->delivered_at) {
            return 'delivered';
        }
        if ($this->shipped_at) {
            return 'shipped';
        }
        if ($this->packed_at) {
            return 'packed';
        }
        if ($this->confirmed_at) {
            return 'confirmed';
        }
        return 'pending';

        // Kiểm tra trạng thái đơn hàng
        if ($this->order_status !== 'completed') {
            return false;
        }

        // Kiểm tra đã nhận hàng chưa (delivered_at phải có giá trị)
        if (!$this->delivered_at) {
            return false;
        }

        // Kiểm tra trong vòng 7 ngày kể từ khi nhận hàng
        $daysSinceDelivery = now()->diffInDays($this->delivered_at);
        if ($daysSinceDelivery > 7) {
            return false;
        }

        return true;
    }

    /**
     * Check if order can be confirmed
     */
    public function canConfirm(): bool
    {
        return in_array($this->order_status, ['pending', 'awaiting_payment', 'paid']);
    }

    /**
     * Check if order can be packed
     */
    public function canPack(): bool
    {
        return in_array($this->order_status, ['processing', 'confirmed', 'paid']);
    }

    /**
     * Check if order can be shipped
     */
    public function canShip(): bool
    {
        return $this->order_status === 'packed';
    }

    /**
     * Check if order can be cancelled
     */
    public function canCancelOrder(): bool
    {
        return in_array($this->order_status, ['pending', 'awaiting_payment', 'paid', 'processing', 'confirmed', 'packed']);
    }

    /**
     * Check if status transition is valid
     */
    public function canTransitionTo(string $newStatus): bool
    {
        $allowedTransitions = self::STATUS_TRANSITIONS[$this->order_status] ?? [];
        return in_array($newStatus, $allowedTransitions);
    }

    /**
     * Get allowed next statuses
     */
    public function getAllowedNextStatuses(): array
    {
        return self::STATUS_TRANSITIONS[$this->order_status] ?? [];
    }

    /**
     * Add timeline event
     */
    public function addTimeline(string $eventType, string $title, ?string $description = null, ?string $oldValue = null, ?string $newValue = null, ?array $metadata = null): OrderTimeline
    {
        return $this->timelines()->create([
            'event_type' => $eventType,
            'title' => $title,
            'description' => $description,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'metadata' => $metadata,
            'user_id' => Auth::check() ? Auth::id() : null,
            'user_type' => Auth::check() && Auth::user() ? Auth::user()->role : 'system',
        ]);
    }

    public function returns()
    {
        return $this->hasMany(OrderReturn::class, 'order_id');
    }
}
