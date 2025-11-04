<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsletterSubscription extends Model
{
    use HasFactory;

    protected $fillable = ['email','token','confirmed_at'];

    protected $casts = [
        'confirmed_at' => 'datetime',
    ];
}


