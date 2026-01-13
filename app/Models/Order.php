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

    // Khai báo các cột được phép gán dữ liệu hàng loạt (Mass Assignment)
    // Giúp bảo mật, tránh người dùng hack thêm các trường nhạy cảm
    protected $fillable = [
        'user_id',            // ID người mua
        'promotion_id',       // ID khuyến mãi (nếu có)
        'order_code',         // Mã đơn hàng (VD: ORD123456)
        'total_price',        // Tổng tiền hàng chưa giảm
        'discount_amount',    // Số tiền được giảm giá
        'final_total',        // Tổng tiền thực tế khách phải trả
        'sub_total',          // Tạm tính
        'payment_method',     // Phương thức thanh toán (cash, bank, momo...)
        'payment_status',     // Trạng thái thanh toán (unpaid, paid...)
        'order_status',       // Trạng thái đơn hàng (pending, shipping...)
        'shipping_address',   // Địa chỉ giao hàng
        'shipping_phone',     // Số điện thoại nhận hàng
        'shipping_method',    // Phương thức vận chuyển
        'shipping_fee',       // Phí ship
        'installation_fee',   // Phí lắp đặt (nếu có)
        'voucher_code',       // Mã giảm giá sử dụng
        'shipping_city',      // Thành phố
        'shipping_district',  // Quận/Huyện
        'shipping_ward',      // Phường/Xã
        'customer_name',      // Tên người nhận
        'customer_phone',     // SĐT người nhận
        'customer_email',     // Email nhận thông báo
        'tracking_code',      // Mã vận đơn của bên vận chuyển
        'tracking_url',       // Link theo dõi đơn hàng
        'shipping_provider',  // Đơn vị vận chuyển (GHTK, GHN...)
        'cancel_reason',      // Lý do hủy đơn (nếu có)
        'notes',              // Ghi chú của khách hàng
        'return_status',      // Trạng thái trả hàng (requested, approved...)
        'return_reason',      // Lý do trả hàng
        'return_note',        // Ghi chú thêm khi trả hàng
        'return_attachments', // Hình ảnh bằng chứng trả hàng (lưu dạng JSON/Array)
        'order_date',         // Ngày đặt hàng
        'confirmed_at',       // Thời điểm xác nhận đơn
        'packed_at',          // Thời điểm đóng gói xong
        'shipped_at',         // Thời điểm giao cho shipper
        'delivered_at',       // Thời điểm giao thành công
        'completed_at',       // Thời điểm hoàn tất đơn hàng
        'cancelled_at',       // Thời điểm hủy đơn
        'refunded_at',        // Thời điểm hoàn tiền
    ];

    // Ép kiểu dữ liệu (Casting)
    // Giúp tự động chuyển đổi dữ liệu từ DB sang kiểu PHP mong muốn
    protected $casts = [
        'total_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_total' => 'decimal:2',
        'sub_total' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'return_attachments' => 'array', // Tự động chuyển JSON trong DB thành mảng PHP
        'order_date' => 'datetime',      // Tự động chuyển chuỗi ngày tháng thành đối tượng Carbon
        'confirmed_at' => 'datetime',
        'packed_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    // Cấu hình hiển thị trạng thái (Metadata)
    // Dùng để hiển thị badge màu sắc và icon trên giao diện
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

    // Nhãn hiển thị phương thức thanh toán
    public const PAYMENT_LABELS = [
        'cash' => 'Thanh toán COD',
        'bank' => 'Chuyển khoản ngân hàng',
        'momo' => 'Ví Momo',
        'paypal' => 'Paypal',
    ];

    // Relationship: Đơn hàng thuộc về 1 User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relationship: Đơn hàng có nhiều chi tiết sản phẩm (Order Details)
    public function items(): HasMany
    {
        return $this->hasMany(OrderDetail::class, 'order_id')->orderBy('id');
    }

    // Scope: Lọc đơn hàng của một user cụ thể (dùng cho trang lịch sử mua hàng)
    public function scopeOwnedBy($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Scope: Lọc theo trạng thái đơn hàng
    // Có logic đặc biệt cho trạng thái 'returned' để bao gồm cả 'return_requested'
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

    // Accessor: Lấy metadata trạng thái (label, badge, icon)
    // Có logic xử lý riêng cho Momo chưa thanh toán -> Hiển thị "Chờ thanh toán" thay vì "Chờ xác nhận"
    public function getStatusMetaAttribute(): array
    {
        $meta = self::STATUS_META[$this->order_status] ?? ['label' => ucfirst($this->order_status), 'badge' => 'secondary', 'icon' => 'bi-question-circle'];

        if ($this->payment_method === 'momo' && $this->payment_status !== 'paid' && $this->order_status === 'pending') {
            $meta['label'] = 'Chờ thanh toán';
            $meta['badge'] = 'warning';
            $meta['icon'] = 'bi-credit-card';
        }

        return $meta;
    }

    // Helper Attributes để lấy nhanh label, badge, icon
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

    // Relationship: Các giao dịch hoàn tiền liên quan
    public function refunds(): HasMany
    {
        return $this->hasMany(OrderRefund::class, 'order_id');
    }

    // Accessor: Lấy tên phương thức thanh toán dạng chữ đẹp
    public function getPaymentLabelAttribute(): string
    {
        return self::PAYMENT_LABELS[$this->payment_method] ?? strtoupper($this->payment_method);
    }

    // Accessor: Lấy ngày hiển thị (ưu tiên order_date, nếu không có thì lấy created_at)
    public function getDisplayOrderDateAttribute()
    {
        return $this->order_date ?? $this->created_at;
    }

    // Logic kiểm tra: Đơn hàng có thể hủy được không?
    // Chỉ hủy được khi còn ở trạng thái "Chờ xác nhận"
    public function canCancel(): bool
    {
        return $this->order_status === 'pending';
    }

    // Logic kiểm tra: Có thể theo dõi vận chuyển không?
    public function canTrack(): bool
    {
        return in_array($this->order_status, ['shipping', 'processing']);
    }

    // Logic kiểm tra: Có thể bấm "Đã nhận hàng" không?
    public function canReceive(): bool
    {
        return $this->order_status === 'delivered';
    }

    // Logic tính toán: Thời gian còn lại để đổi trả (tính bằng ngày)
    public function getReturnDaysRemaining(): ?int
    {
        if ($this->order_status !== 'completed') {
            return null;
        }

        // Lấy ngày hoàn thành, nếu không có thì lấy ngày cập nhật hoặc ngày giao hàng
        $completedAt = $this->completed_at ?? $this->updated_at ?? $this->delivered_at;

        if (!$completedAt) {
            return 0;
        }

        // Đảm bảo là Carbon instance (thư viện xử lý ngày giờ)
        $completedAt = \Carbon\Carbon::parse($completedAt);

        // Quy định: Cho phép đổi trả trong vòng 7 ngày
        $deadline = $completedAt->copy()->addDays(7);

        // Tính số ngày còn lại
        $remaining = now()->diffInDays($deadline, false);

        return $remaining > 0 ? (int)$remaining : 0;
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
        return $this->order_status === 'completed'
            && $this->return_status === 'none'
            && $this->getReturnDaysRemaining() > 0;
    }

    public function isReturnExpired(): bool
    {
        return $this->getReturnDaysRemaining() === 0;
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
