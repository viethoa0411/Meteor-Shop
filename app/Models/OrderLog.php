<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'status',
        'updated_by',
        'role',
        'created_at',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}

