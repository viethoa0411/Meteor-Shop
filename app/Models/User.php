<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// 👉 Thêm dòng này để kích hoạt SoftDeletes
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes; // 👈 Thêm SoftDeletes ở đây

    /**
     * Các trường được phép gán giá trị hàng loạt
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'address',
        'status',
    ];

    /**
     * Ẩn khi chuyển đổi sang JSON
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Kiểu dữ liệu tự động ép kiểu
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Scope để lọc theo role
     */
    public function scopeAdmin($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopeUser($query)
    {
        return $query->where('role', 'user');
    }

    /**
     * Các quan hệ với models khác
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
