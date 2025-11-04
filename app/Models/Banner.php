<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'image',
        'link',
        'position',
        'status',
        'start_at',
        'end_at',
    ];

    protected $casts = [
        'position' => 'integer',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    /**
     * Chỉ lấy banner đang hoạt động
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeAvailable(Builder $query): Builder
    {
        $now = now();
        return $query->where(function ($q) use ($now) {
            $q->whereNull('start_at')->orWhere('start_at', '<=', $now);
        })->where(function ($q) use ($now) {
            $q->whereNull('end_at')->orWhere('end_at', '>=', $now);
        });
    }
}
