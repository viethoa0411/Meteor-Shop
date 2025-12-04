<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_enabled')->default(true);
            $table->string('welcome_message', 500)->default('Xin chào! 👋 Chào mừng bạn đến với Meteor Shop. Tôi có thể giúp gì cho bạn?');
            $table->string('offline_message', 500)->default('Hiện tại không có nhân viên trực tuyến. Vui lòng để lại tin nhắn, chúng tôi sẽ phản hồi sớm nhất!');
            $table->string('chatbox_title', 100)->default('Hỗ trợ Meteor');
            $table->string('chatbox_subtitle', 100)->default('Chúng tôi luôn sẵn sàng hỗ trợ bạn');
            $table->string('primary_color', 20)->default('#667eea');
            $table->string('secondary_color', 20)->default('#764ba2');
            $table->json('quick_replies')->nullable(); // Các câu trả lời nhanh
            $table->json('auto_replies')->nullable(); // Tự động trả lời theo từ khóa
            $table->json('working_hours')->nullable(); // Giờ làm việc
            $table->boolean('show_on_mobile')->default(true);
            $table->boolean('play_sound')->default(true);
            $table->integer('position_bottom')->default(24);
            $table->integer('position_right')->default(24);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_settings');
    }
};

