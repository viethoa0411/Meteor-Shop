<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';
    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'parent_id',
        'status'
    ];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')->with('children');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    protected static function booted()
    {
        static::created(function ($item) {
            \DB::transaction(function () use ($item) {
                \DB::table('categories')
                    ->where('id', '!=', $item->id)
                    ->increment('sort_order');

                $item->sort_order = 1;
                $item->saveQuietly();
            });
        });
        static::deleted(function ($item) {
            \DB::table('categories')
                ->where('sort_order', '>', $item->sort_order)
                ->decrement('sort_order');
        });
    }
}
