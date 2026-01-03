<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactInfo extends Model
{
    protected $fillable = [
        'zalo_link',
        'messenger_link',
        'phone_number',
        'show_zalo',
        'show_messenger',
        'show_phone',
    ];

    protected $casts = [
        'show_zalo' => 'boolean',
        'show_messenger' => 'boolean',
        'show_phone' => 'boolean',
    ];

    public static function getActive()
    {
        return self::first() ?? new self();
    }
}