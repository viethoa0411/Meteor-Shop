<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bảng yêu cầu rút tiền
     * - Khách gửi yêu cầu rút tiền với thông tin ngân hàng
     * - Admin xác nhận và trừ tiền từ ví
     */
    public function up(): void
    {
        Schema::create('withdraw_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('wallet_id')->constrained('client_wallets')->onDelete('cascade');
            $table->decimal('amount', 15, 2); // Số tiền khách muốn rút
            $table->decimal('confirmed_amount', 15, 2)->nullable(); // Số tiền admin xác nhận rút
            $table->string('request_code')->unique(); // Mã yêu cầu: WD_xxx
            
            // Thông tin ngân hàng
            $table->string('bank_name'); // Tên ngân hàng
            $table->string('account_number'); // Số tài khoản
            $table->string('account_holder'); // Tên chủ tài khoản
            $table->string('phone'); // Số điện thoại liên hệ
            
            $table->enum('status', ['pending', 'processing', 'completed', 'rejected', 'cancelled'])->default('pending');
            $table->text('note')->nullable(); // Ghi chú của khách
            $table->text('admin_note')->nullable(); // Ghi chú của admin
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('withdraw_requests');
    }
};

