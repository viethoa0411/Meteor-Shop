<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bảng lịch sử giao dịch ví
     * - Ghi lại tất cả các giao dịch: nạp, rút, thanh toán, hoàn tiền
     */
    public function up(): void
    {
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained('client_wallets')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Loại giao dịch
            $table->enum('type', [
                'deposit',      // Nạp tiền
                'withdraw',     // Rút tiền
                'payment',      // Thanh toán đơn hàng
                'refund',       // Hoàn tiền từ đơn hàng bị hủy
                'cashback'      // Tiền về từ đơn hàng hoàn thành
            ]);
            
            $table->decimal('amount', 15, 2); // Số tiền (luôn dương)
            $table->decimal('balance_before', 15, 2); // Số dư trước giao dịch
            $table->decimal('balance_after', 15, 2); // Số dư sau giao dịch
            
            $table->string('transaction_code')->unique(); // Mã giao dịch: TXN_xxx
            $table->text('description')->nullable(); // Mô tả
            
            // Liên kết với đơn hàng (nếu có)
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            
            // Liên kết với yêu cầu nạp/rút (nếu có)
            $table->foreignId('deposit_request_id')->nullable()->constrained('deposit_requests')->nullOnDelete();
            $table->foreignId('withdraw_request_id')->nullable()->constrained('withdraw_requests')->nullOnDelete();
            
            // Admin xử lý (nếu có)
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])->default('completed');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};

