<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'level',
        'title',
        'message',
        'url',
        'icon',
        'icon_color',
        'is_read',
        'read_at',
        'metadata',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the notification
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope to get read notifications
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Scope to filter by type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to filter by level
     */
    public function scopeOfLevel($query, string $level)
    {
        return $query->where('level', $level);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(): bool
    {
        return $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Mark notification as unread
     */
    public function markAsUnread(): bool
    {
        return $this->update([
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    /**
     * Create a notification
     */
    public static function createNotification(array $data): self
    {
        return self::create([
            'user_id' => $data['user_id'] ?? auth()->id(),
            'type' => $data['type'],
            'level' => $data['level'] ?? 'info',
            'title' => $data['title'],
            'message' => $data['message'],
            'url' => $data['url'] ?? null,
            'icon' => $data['icon'] ?? self::getDefaultIcon($data['type']),
            'icon_color' => $data['icon_color'] ?? self::getDefaultIconColor($data['level'] ?? 'info'),
            'metadata' => $data['metadata'] ?? null,
        ]);
    }

    /**
     * Get default icon for notification type
     */
    public static function getDefaultIcon(string $type): string
    {
        return match($type) {
            'order' => 'bi-cart-check-fill',
            'product' => 'bi-box-seam-fill',
            'review' => 'bi-chat-left-text-fill',
            'chat' => 'bi-chat-dots-fill',
            'contact' => 'bi-envelope-fill',
            'deposit' => 'bi-wallet2',
            'withdraw' => 'bi-cash-coin',
            'user' => 'bi-person-fill',
            'voucher' => 'bi-ticket-perforated-fill',
            'shipping' => 'bi-truck-fill',
            'security' => 'bi-shield-fill',
            default => 'bi-bell-fill',
        };
    }

    /**
     * Get default icon color for level
     */
    public static function getDefaultIconColor(string $level): string
    {
        return match($level) {
            'info' => 'text-info',
            'warning' => 'text-warning',
            'danger' => 'text-danger',
            'success' => 'text-success',
            default => 'text-primary',
        };
    }
}
