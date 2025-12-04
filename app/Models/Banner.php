<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'image',
        'link',
        'position',
        'sort_order',
        'status',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    /**
     * Scope để lấy banner đang hoạt động
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('start_date')
                  ->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', now());
            });
    }

    /**
     * Kiểm tra banner có đang hiển thị không
     */
    public function isActive(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        $now = now();
        
        if ($this->start_date && $now->lt($this->start_date)) {
            return false;
        }

        if ($this->end_date && $now->gt($this->end_date)) {
            return false;
        }

        return true;
    }

    /**
     * Lấy URL đầy đủ của ảnh (cố gắng xử lý các trường hợp đường dẫn khác nhau)
     */
    public function getImageUrlAttribute(): ?string
    {
        if (empty($this->image)) {
            return null;
        }

        // Chuẩn hóa path: loại bỏ prefix không cần thiết
        $path = ltrim($this->image, '/');
        $path = str_replace(['storage/', 'public/'], '', $path);

        // Không chặn hiển thị nếu Storage::exists trả về false,
        // vì phía view đã có onerror fallback cho trường hợp ảnh thật sự không tồn tại.
        return asset('storage/' . $path);
    }

    /**
     * Kiểm tra ảnh có tồn tại không
     */
    public function hasImage(): bool
    {
        return !empty($this->image) && Storage::disk('public')->exists($this->image);
    }
}

