<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatSetting;
use App\Models\ChatSession;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatboxController extends Controller
{
    /**
     * Trang quản lý chat - danh sách conversations
     */
    public function index(Request $request)
    {
        $query = ChatSession::with(['user', 'assignedAdmin'])
            ->withCount('messages');

        // Filter theo status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter theo unread
        if ($request->filled('unread') && $request->unread == '1') {
            $query->where('unread_count', '>', 0);
        }

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('guest_name', 'like', "%{$search}%")
                  ->orWhere('guest_email', 'like', "%{$search}%")
                  ->orWhere('guest_phone', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $sessions = $query->orderBy('last_message_at', 'desc')->paginate(20);
        $settings = ChatSetting::getSettings();
        
        // Thống kê
        $stats = [
            'total' => ChatSession::count(),
            'active' => ChatSession::where('status', 'active')->count(),
            'unread' => ChatSession::where('unread_count', '>', 0)->count(),
            'today' => ChatSession::whereDate('created_at', today())->count(),
        ];

        return view('admin.chatbox.index', compact('sessions', 'settings', 'stats'));
    }
    /**
     * Trang cài đặt chatbox
     */
    public function settings()
    {
        $settings = ChatSetting::getSettings();
        return view('admin.chatbox.settings', compact('settings'));
    }
     /**
     * Cập nhật cài đặt chatbox
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'chatbox_title' => 'required|string|max:100',
            'welcome_message' => 'required|string|max:500',
            'offline_message' => 'required|string|max:500',
            'primary_color' => 'required|string|max:20',
        ]);

        $settings = ChatSetting::getSettings();
        
        $settings->update([
            'is_enabled' => $request->boolean('is_enabled'),
            'chatbox_title' => $request->chatbox_title,
            'chatbox_subtitle' => $request->chatbox_subtitle,
            'welcome_message' => $request->welcome_message,
            'offline_message' => $request->offline_message,
            'primary_color' => $request->primary_color,
            'secondary_color' => $request->secondary_color ?? $request->primary_color,
            'show_on_mobile' => $request->boolean('show_on_mobile'),
            'play_sound' => $request->boolean('play_sound'),
            'position_bottom' => $request->position_bottom ?? 24,
            'position_right' => $request->position_right ?? 24,
        ]);

        return back()->with('success', 'Đã cập nhật cài đặt chatbox');
    }
}

