<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'discount_type',
        'discount_value',
        'start_date',
        'end_date',
        'usage_limit',
        'used_count',
        'status',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
    }

    public function scopeAvailable($query)
    {
        return $query->active()
                    ->where(function($q) {
                        $q->whereNull('usage_limit')
                          ->orWhereRaw('used_count < usage_limit');
                    });
    }

    // Accessors
    public function getFormattedDiscountValueAttribute()
    {
        if ($this->discount_type === 'percent') {
            return $this->discount_value . '%';
        }
        return number_format($this->discount_value, 0, ',', '.') . ' VNĐ';
    }

    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'active' => 'bg-success',
            'expired' => 'bg-danger',
            'inactive' => 'bg-secondary',
            default => 'bg-light'
        };
    }

    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'active' => 'Hoạt động',
            'expired' => 'Hết hạn',
            'inactive' => 'Không hoạt động',
            default => 'Không xác định'
        };
    }
}
