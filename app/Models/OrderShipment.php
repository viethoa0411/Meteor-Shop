<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderShipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'shipment_code',
        'carrier',
        'carrier_name',
        'tracking_number',
        'tracking_url',
        'status',
        'shipping_cost',
        'carrier_data',
        'picked_up_at',
        'in_transit_at',
        'out_for_delivery_at',
        'delivered_at',
        'failed_at',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'shipping_cost' => 'decimal:2',
        'carrier_data' => 'array',
        'picked_up_at' => 'datetime',
        'in_transit_at' => 'datetime',
        'out_for_delivery_at' => 'datetime',
        'delivered_at' => 'datetime',
        'failed_at' => 'datetime',
    ];

    public const STATUS_META = [
        'pending' => ['label' => 'Chờ xử lý', 'badge' => 'warning'],
        'label_created' => ['label' => 'Đã tạo nhãn', 'badge' => 'info'],
        'picked_up' => ['label' => 'Đã lấy hàng', 'badge' => 'primary'],
        'in_transit' => ['label' => 'Đang vận chuyển', 'badge' => 'info'],
        'out_for_delivery' => ['label' => 'Đang giao hàng', 'badge' => 'primary'],
        'delivered' => ['label' => 'Đã giao', 'badge' => 'success'],
        'failed' => ['label' => 'Thất bại', 'badge' => 'danger'],
        'returned' => ['label' => 'Đã trả lại', 'badge' => 'secondary'],
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getStatusMetaAttribute(): array
    {
        return self::STATUS_META[$this->status] ?? ['label' => ucfirst($this->status), 'badge' => 'secondary'];
    }
}

