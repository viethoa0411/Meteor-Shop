<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('guest_token', 100)->nullable(); // Token cho khách không đăng nhập
            $table->string('guest_name', 100)->nullable();
            $table->string('guest_email', 100)->nullable();
            $table->string('guest_phone', 20)->nullable();
            $table->enum('status', ['active', 'closed', 'pending'])->default('active');
            $table->foreignId('assigned_admin_id')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('unread_count')->default(0); // Số tin nhắn chưa đọc (admin)
            $table->integer('client_unread_count')->default(0); // Số tin nhắn chưa đọc (client)
            $table->text('last_message')->nullable();
            $table->timestamp('last_message_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->string('page_url', 500)->nullable(); // Trang user đang xem
            $table->timestamps();
            
            $table->index(['status', 'last_message_at']);
            $table->index('guest_token');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_sessions');
    }
};

