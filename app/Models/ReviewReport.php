<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewReport extends Model
{
    protected $fillable = [
        'review_id',
        'user_id',
        'reason',
        'description',
    ];

    public function review(): BelongsTo
    {
        return $this->belongsTo(Review::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getReasonLabelAttribute(): string
    {
        return match($this->reason) {
            'spam' => 'Spam',
            'offensive' => 'Xúc phạm',
            'false_info' => 'Thông tin sai sự thật',
            'inappropriate' => 'Nội dung không phù hợp',
            default => 'Khác',
        };
    }
}
