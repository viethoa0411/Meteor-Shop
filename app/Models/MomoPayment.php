<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MomoPayment extends Model
{
    protected $fillable = [
        'order_id',
        'partner_code',
        'request_id',
        'order_id_momo',
        'trans_id',
        'pay_type',
        'amount',
        'result_code',
        'message',
        'response_time',
        'extra_data',
        'signature'
    ];
}
