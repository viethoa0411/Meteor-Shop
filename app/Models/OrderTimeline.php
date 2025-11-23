<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderTimeline extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'event_type',
        'title',
        'description',
        'old_value',
        'new_value',
        'metadata',
        'user_id',
        'user_type',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public const EVENT_TYPES = [
        'status_changed' => 'Thay đổi trạng thái',
        'payment_received' => 'Nhận thanh toán',
        'payment_failed' => 'Thanh toán thất bại',
        'shipment_created' => 'Tạo đơn vận chuyển',
        'shipment_updated' => 'Cập nhật đơn vận chuyển',
        'refund_created' => 'Tạo yêu cầu hoàn tiền',
        'refund_completed' => 'Hoàn tiền thành công',
        'return_requested' => 'Yêu cầu trả hàng',
        'return_approved' => 'Duyệt trả hàng',
        'note_added' => 'Thêm ghi chú',
        'order_edited' => 'Chỉnh sửa đơn hàng',
        'order_created' => 'Tạo đơn hàng',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getEventTypeLabelAttribute(): string
    {
        return self::EVENT_TYPES[$this->event_type] ?? $this->event_type;
    }
}

