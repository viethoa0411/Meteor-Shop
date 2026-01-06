<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class PostCategory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'sort_order',
        'seo_title',
        'seo_description',
        'status',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    /**
     * Quan hệ với parent category
     */
    public function parent()
    {
        return $this->belongsTo(PostCategory::class, 'parent_id');
    }

    /**
     * Quan hệ với child categories
     */
    public function children()
    {
        return $this->hasMany(PostCategory::class, 'parent_id')->orderBy('sort_order');
    }

    /**
     * Auto generate slug từ name
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name') && empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    /**
     * Scope: Lọc categories active
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Lấy tất cả categories dạng tree
     */
    public static function getTree($parentId = null)
    {
        return static::where('parent_id', $parentId)
            ->orderBy('sort_order')
            ->with('children')
            ->get();
    }
}
