<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bảng ví của client
     * - Mỗi user chỉ có 1 ví
     * - Lưu số dư hiện tại
     */
    public function up(): void
    {
        Schema::create('client_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->decimal('balance', 15, 2)->default(0); // Số dư
            $table->enum('status', ['active', 'frozen', 'closed'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_wallets');
    }
};

