<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ChatSetting;
use App\Models\ChatSession;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    /**
     * Lấy cài đặt chatbox và session hiện tại
     */
    public function getSettings(Request $request)
    {
        try {
            $settings = ChatSetting::getSettings();

            if (!$settings->is_enabled) {
                return response()->json([
                    'enabled' => false,
                ]);
            }

            $session = $this->getOrCreateSession($request);

            // Lấy tin nhắn của session
            $messages = $session->messages()->with('sender')->get();

            // Đánh dấu tin nhắn đã đọc cho client
            $session->markAsReadByClient();

            return response()->json([
                'enabled' => true,
                'settings' => [
                    'title' => $settings->chatbox_title,
                    'subtitle' => $settings->chatbox_subtitle,
                    'welcome_message' => $settings->welcome_message,
                    'offline_message' => $settings->offline_message,
                    'primary_color' => $settings->primary_color,
                    'secondary_color' => $settings->secondary_color,
                    'quick_replies' => $settings->quick_replies ?? [],
                    'show_on_mobile' => $settings->show_on_mobile,
                    'play_sound' => $settings->play_sound,
                    'is_working_hours' => $settings->isWorkingHours(),
                    'position_bottom' => $settings->position_bottom,
                    'position_right' => $settings->position_right,
                ],
                'session_id' => $session->id,
                'session_token' => $session->guest_token,
                'messages' => $messages->map(function($msg) {
                    return [
                        'id' => $msg->id,
                        'message' => $msg->message,
                        'sender_type' => $msg->sender_type,
                        'sender_name' => $msg->sender_name,
                        'message_type' => $msg->message_type,
                        'attachment_url' => $msg->attachment_url,
                        'attachment_name' => $msg->attachment_name,
                        'time' => $msg->formatted_time,
                        'created_at' => $msg->created_at->toISOString(),
                    ];
                }),
                'unread_count' => $session->client_unread_count,
            ]);
        } catch (\Exception $e) {
            \Log::error('Chat getSettings error: ' . $e->getMessage());
            return response()->json([
                'enabled' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}

