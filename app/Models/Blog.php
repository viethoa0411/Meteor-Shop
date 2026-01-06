<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Blog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'thumbnail',
        'status',
        'published_at',
        'view_count',
        'seo_title',
        'seo_description',
        'canonical_url',
        'noindex',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'noindex' => 'boolean',
        'view_count' => 'integer',
    ];

    /**
     * Quan hệ với User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: Lọc bài viết đã publish
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where(function($q) {
                $q->whereNull('published_at')
                  ->orWhere('published_at', '<=', now());
            });
    }

    /**
     * Scope: Lọc bài viết draft
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope: Lọc bài viết scheduled
     */
    public function scopeScheduled($query)
    {
        return $query->where('status', 'published')
            ->where('published_at', '>', now());
    }

    /**
     * Tăng view count
     */
    public function incrementViewCount()
    {
        $this->increment('view_count');
    }
}

