<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPromotionUsage extends Model
{
    protected $table = 'user_promotion_usages';

    protected $fillable = [
        'user_id',
        'promotion_id',
        'used_count',
        'last_used_at',
    ];
}

