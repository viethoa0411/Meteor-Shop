<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'type',
        'note',
        'is_pinned',
        'attachments',
        'created_by',
        'tagged_user_id',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'attachments' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function taggedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tagged_user_id');
    }
}

