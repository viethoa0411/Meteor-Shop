<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'rating',
        'content',
        'comment', // Keep for backward compatibility
        'images',
        'is_verified_purchase',
        'status',
        'reported_count',
    ];

    protected $casts = [
        'images' => 'array',
        'is_verified_purchase' => 'boolean',
        'rating' => 'integer',
        'reported_count' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reports()
    {
        return $this->hasMany(ReviewReport::class);
    }

    public function replies()
    {
        return $this->hasMany(ReviewReply::class);
    }

    public function helpfulVotes()
    {
        return $this->hasMany(ReviewHelpful::class);
    }

    // Accessor for backward compatibility
    public function getCommentAttribute()
    {
        return $this->content;
    }

    public function setCommentAttribute($value)
    {
        $this->attributes['content'] = $value;
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeHidden($query)
    {
        return $query->where('status', 'hidden');
    }

    public function scopeReported($query)
    {
        return $query->where('reported_count', '>', 0);
    }

    public function scopeWithImages($query)
    {
        return $query->whereNotNull('images')->where('images', '!=', '[]');
    }
}
