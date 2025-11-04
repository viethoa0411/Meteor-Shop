<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title','slug','image','excerpt','content','status','published_at',
        'category_id','is_featured','meta_title','meta_description','og_image'
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_featured' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(PostCategory::class, 'category_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'post_tag');
    }

    public function scopePublished($q)
    {
        return $q->where('status', 'published')->where(function($w){ $w->whereNull('published_at')->orWhere('published_at','<=', now()); });
    }

    public function scopeFeatured($q)
    {
        return $q->where('is_featured', true);
    }
}


