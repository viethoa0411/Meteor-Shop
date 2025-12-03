<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatSession extends Model
{
    protected $fillable = [
        'user_id',
        'guest_token',
        'guest_name',
        'guest_email',
        'guest_phone',
        'status',
        'assigned_admin_id',
        'unread_count',
        'client_unread_count',
        'last_message',
        'last_message_at',
        'ip_address',
        'user_agent',
        'page_url',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_admin_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class)->orderBy('created_at', 'asc');
    }

    public function latestMessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class)->orderBy('created_at', 'desc');
    }

    /**
     * Lấy tên hiển thị của khách hàng
     */
    public function getCustomerNameAttribute()
    {
        if ($this->user) {
            return $this->user->name;
        }
        return $this->guest_name ?: 'Khách #' . $this->id;
    }

    /**
     * Lấy email hiển thị
     */
    public function getCustomerEmailAttribute()
    {
        if ($this->user) {
            return $this->user->email;
        }
        return $this->guest_email;
    }

    /**
     * Scope: chỉ lấy sessions active
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: chỉ lấy sessions có tin nhắn chưa đọc
     */
    public function scopeUnread($query)
    {
        return $query->where('unread_count', '>', 0);
    }

    /**
     * Scope: sắp xếp theo tin nhắn mới nhất
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('last_message_at', 'desc');
    }

    /**
     * Đánh dấu tất cả tin nhắn là đã đọc (cho admin)
     */
    public function markAsRead()
    {
        $this->messages()->where('sender_type', 'client')->where('is_read', false)->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
        $this->update(['unread_count' => 0]);
    }

    /**
     * Đánh dấu tất cả tin nhắn là đã đọc (cho client)
     */
    public function markAsReadByClient()
    {
        $this->messages()->where('sender_type', '!=', 'client')->where('is_read', false)->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
        $this->update(['client_unread_count' => 0]);
    }
}

