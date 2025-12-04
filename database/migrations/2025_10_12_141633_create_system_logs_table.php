<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('system_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->nullable(); // người thực hiện (có thể null nếu là hệ thống)
            $table->string('action'); // ví dụ: 'create_product', 'update_order'
            $table->string('module')->nullable(); // ví dụ: 'products', 'orders', 'users'
            $table->text('description')->nullable(); // mô tả chi tiết
            $table->ipAddress('ip_address')->nullable(); // IP của người thực hiện
            $table->string('user_agent')->nullable(); // trình duyệt hoặc thiết bị
            $table->timestamps();

            // Khóa ngoại
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_logs');
    }
};
