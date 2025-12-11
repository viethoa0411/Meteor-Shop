<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bảng yêu cầu nạp tiền
     * - Khách gửi yêu cầu nạp tiền
     * - Admin xác nhận và cộng tiền vào ví
     */
    public function up(): void
    {
        Schema::create('deposit_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('wallet_id')->constrained('client_wallets')->onDelete('cascade');
            $table->decimal('amount', 15, 2); // Số tiền khách muốn nạp
            $table->decimal('confirmed_amount', 15, 2)->nullable(); // Số tiền admin xác nhận
            $table->string('request_code')->unique(); // Mã yêu cầu: DEP_xxx
            $table->enum('status', ['pending', 'confirmed', 'rejected', 'cancelled'])->default('pending');
            $table->text('note')->nullable(); // Ghi chú của khách
            $table->text('admin_note')->nullable(); // Ghi chú của admin
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deposit_requests');
    }
};

