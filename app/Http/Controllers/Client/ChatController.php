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
    /**
     * Gửi tin nhắn từ client
     */
    public function sendMessage(Request $request)
    {
        try {
            $request->validate([
                'message' => 'required_without:image|nullable|string|max:2000',
                'image' => 'required_without:message|nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
                'session_token' => 'nullable|string',
            ]);

            $settings = ChatSetting::getSettings();

            if (!$settings->is_enabled) {
                return response()->json(['success' => false, 'error' => 'Chat đang tắt'], 403);
            }

            $session = $this->getOrCreateSession($request);

            // Xử lý upload hình ảnh
            $attachmentUrl = null;
            $attachmentName = null;
            $messageType = 'text';
            $messageText = $request->message ?? '';

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $fileName = 'chat_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/chat'), $fileName);
                $attachmentUrl = '/uploads/chat/' . $fileName;
                $attachmentName = $image->getClientOriginalName();
                $messageType = 'image';
                $messageText = $messageText ?: '[Hình ảnh]';
            }

            // Tạo tin nhắn từ client
            $message = ChatMessage::create([
                'chat_session_id' => $session->id,
                'sender_type' => 'client',
                'sender_id' => Auth::id(),
                'message' => $messageText,
                'message_type' => $messageType,
                'attachment_url' => $attachmentUrl,
                'attachment_name' => $attachmentName,
            ]);

            // Cập nhật session
            $session->update([
                'last_message' => $messageType === 'image' ? '📷 Hình ảnh' : $messageText,
                'last_message_at' => now(),
                'unread_count' => $session->unread_count + 1,
                'page_url' => $request->input('page_url'),
            ]);

            $responseMessages = [[
                'id' => $message->id,
                'message' => $message->message,
                'sender_type' => 'client',
                'message_type' => $messageType,
                'attachment_url' => $attachmentUrl,
                'attachment_name' => $attachmentName,
                'time' => $message->formatted_time,
                'created_at' => $message->created_at->toISOString(),
            ]];

            // Kiểm tra auto reply (chỉ cho text)
            if ($messageType === 'text' && $request->message) {
                $autoReply = $settings->findAutoReply($request->message);
                if ($autoReply) {
                    $botMessage = ChatMessage::create([
                        'chat_session_id' => $session->id,
                        'sender_type' => 'bot',
                        'message' => $autoReply,
                        'message_type' => 'text',
                    ]);

                    $responseMessages[] = [
                        'id' => $botMessage->id,
                        'message' => $botMessage->message,
                        'sender_type' => 'bot',
                        'sender_name' => 'Bot',
                        'time' => $botMessage->formatted_time,
                        'created_at' => $botMessage->created_at->toISOString(),
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'messages' => $responseMessages,
                'session_token' => $session->guest_token,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Chat send error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Có lỗi xảy ra: ' . $e->getMessage(),
            ], 500);
        }
    }
    /**
     * Lấy tin nhắn mới (polling)
     */
    public function getMessages(Request $request)
    {
        $session = $this->getOrCreateSession($request);
        $lastId = $request->input('last_id', 0);
        
        $messages = $session->messages()
            ->where('id', '>', $lastId)
            ->where('sender_type', '!=', 'client')
            ->get();

        // Đánh dấu đã đọc
        if ($messages->isNotEmpty()) {
            $session->markAsReadByClient();
        }

        return response()->json([
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
        ]);
    }

}

