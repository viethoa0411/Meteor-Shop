<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatSetting extends Model
{
    protected $fillable = [
        'is_enabled',
        'welcome_message',
        'offline_message',
        'chatbox_title',
        'chatbox_subtitle',
        'primary_color',
        'secondary_color',
        'quick_replies',
        'auto_replies',
        'working_hours',
        'show_on_mobile',
        'play_sound',
        'position_bottom',
        'position_right',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'show_on_mobile' => 'boolean',
        'play_sound' => 'boolean',
        'quick_replies' => 'array',
        'auto_replies' => 'array',
        'working_hours' => 'array',
    ];

    /**
     * Lấy settings hiện tại (singleton pattern)
     */
    public static function getSettings()
    {
        $settings = self::first();
        
        if (!$settings) {
            $settings = self::create([
                'is_enabled' => true,
                'welcome_message' => 'Xin chào! 👋 Chào mừng bạn đến với Meteor Shop. Tôi có thể giúp gì cho bạn?',
                'offline_message' => 'Hiện tại không có nhân viên trực tuyến. Vui lòng để lại tin nhắn!',
                'chatbox_title' => 'Hỗ trợ Meteor',
                'chatbox_subtitle' => 'Chúng tôi luôn sẵn sàng hỗ trợ bạn',
                'primary_color' => '#667eea',
                'secondary_color' => '#764ba2',
                'quick_replies' => [
                    ['icon' => 'bi-box-seam', 'text' => 'Tư vấn sản phẩm', 'message' => 'Tôi muốn tư vấn sản phẩm'],
                    ['icon' => 'bi-truck', 'text' => 'Kiểm tra đơn hàng', 'message' => 'Tôi muốn kiểm tra đơn hàng'],
                    ['icon' => 'bi-arrow-return-left', 'text' => 'Đổi trả hàng', 'message' => 'Tôi muốn đổi trả hàng'],
                    ['icon' => 'bi-question-circle', 'text' => 'Hỗ trợ khác', 'message' => 'Tôi cần hỗ trợ khác'],
                ],
                'auto_replies' => [
                    ['keywords' => ['giá', 'bao nhiêu', 'giá tiền'], 'reply' => 'Bạn có thể xem giá sản phẩm trực tiếp trên website hoặc cho tôi biết sản phẩm bạn quan tâm!'],
                    ['keywords' => ['giao hàng', 'ship', 'vận chuyển'], 'reply' => 'Meteor Shop giao hàng toàn quốc. Đơn hàng từ 500k được miễn phí ship nội thành!'],
                    ['keywords' => ['đổi trả', 'hoàn tiền', 'bảo hành'], 'reply' => 'Meteor Shop hỗ trợ đổi trả trong 7 ngày và bảo hành 12 tháng cho sản phẩm!'],
                ],
                'working_hours' => [
                    'monday' => ['start' => '08:00', 'end' => '22:00', 'enabled' => true],
                    'tuesday' => ['start' => '08:00', 'end' => '22:00', 'enabled' => true],
                    'wednesday' => ['start' => '08:00', 'end' => '22:00', 'enabled' => true],
                    'thursday' => ['start' => '08:00', 'end' => '22:00', 'enabled' => true],
                    'friday' => ['start' => '08:00', 'end' => '22:00', 'enabled' => true],
                    'saturday' => ['start' => '09:00', 'end' => '21:00', 'enabled' => true],
                    'sunday' => ['start' => '09:00', 'end' => '18:00', 'enabled' => true],
                ],
            ]);
        }
        
        return $settings;
    }

    /**
     * Kiểm tra có đang trong giờ làm việc không
     */
    public function isWorkingHours()
    {
        if (!$this->working_hours) {
            return true;
        }

        $dayOfWeek = strtolower(now()->format('l'));
        $currentTime = now()->format('H:i');

        $todayHours = $this->working_hours[$dayOfWeek] ?? null;
        
        if (!$todayHours || !($todayHours['enabled'] ?? false)) {
            return false;
        }

        return $currentTime >= $todayHours['start'] && $currentTime <= $todayHours['end'];
    }

    /**
     * Tìm auto reply theo tin nhắn
     */
    public function findAutoReply($message)
    {
        if (!$this->auto_replies) {
            return null;
        }

        $messageLower = mb_strtolower($message);
        
        foreach ($this->auto_replies as $autoReply) {
            foreach ($autoReply['keywords'] as $keyword) {
                if (mb_strpos($messageLower, mb_strtolower($keyword)) !== false) {
                    return $autoReply['reply'];
                }
            }
        }
        
        return null;
    }
}

