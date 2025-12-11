<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatusHistory extends Model
{
    protected $table = 'order_status_history';

    protected $fillable = [
        'order_id',
        'admin_id',
        'old_status',
        'new_status',
        'note',
    ];

    /**
     * Quan hệ với Order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Quan hệ với User (Admin đã cập nhật)
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}


