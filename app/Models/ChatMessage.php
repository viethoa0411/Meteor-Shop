<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    protected $fillable = [
        'chat_session_id',
        'sender_type',
        'sender_id',
        'message',
        'message_type',
        'attachment_url',
        'attachment_name',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(ChatSession::class, 'chat_session_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Lấy tên người gửi
     */
    public function getSenderNameAttribute()
    {
        if ($this->sender_type === 'bot') {
            return 'Bot';
        }
        
        if ($this->sender_type === 'admin' && $this->sender) {
            return $this->sender->name;
        }
        
        if ($this->sender_type === 'client') {
            return $this->session->customer_name ?? 'Khách';
        }
        
        return 'Hệ thống';
    }

    /**
     * Kiểm tra tin nhắn có phải từ client không
     */
    public function isFromClient()
    {
        return $this->sender_type === 'client';
    }

    /**
     * Kiểm tra tin nhắn có phải từ admin không
     */
    public function isFromAdmin()
    {
        return $this->sender_type === 'admin';
    }

    /**
     * Kiểm tra tin nhắn có phải từ bot không
     */
    public function isFromBot()
    {
        return $this->sender_type === 'bot';
    }

    /**
     * Format thời gian gửi
     */
    public function getFormattedTimeAttribute()
    {
        $now = now();
        $created = $this->created_at;

        if ($created->isToday()) {
            return $created->format('H:i');
        }

        if ($created->isYesterday()) {
            return 'Hôm qua ' . $created->format('H:i');
        }

        if ($created->year === $now->year) {
            return $created->format('d/m H:i');
        }

        return $created->format('d/m/Y H:i');
    }
}

