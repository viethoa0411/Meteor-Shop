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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->nullable()->comment('ID đơn hàng');
            $table->unsignedBigInteger('wallet_id')->comment('ID ví nhận tiền');
            $table->decimal('amount', 15, 2)->comment('Số tiền giao dịch');
            $table->enum('type', ['income', 'expense'])->default('income')->comment('Loại giao dịch');
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])->default('pending')->comment('Trạng thái');
            $table->string('payment_method')->nullable()->comment('Phương thức thanh toán');
            $table->string('transaction_code')->nullable()->unique()->comment('Mã giao dịch');
            $table->text('qr_code_url')->nullable()->comment('URL QR code');
            $table->text('description')->nullable()->comment('Mô tả giao dịch');
            $table->timestamp('completed_at')->nullable()->comment('Thời gian hoàn thành');
            $table->timestamps();

            // Khóa ngoại
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
            $table->foreign('wallet_id')->references('id')->on('wallets')->onDelete('cascade');

            // Index
            $table->index('order_id');
            $table->index('wallet_id');
            $table->index('status');
            $table->index('transaction_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
