<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceWatch extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','email','product_id','target_price','last_notified_at'];

    protected $casts = [
        'last_notified_at' => 'datetime',
    ];
}


