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
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->comment('ID đơn hàng');
            $table->unsignedBigInteger('user_id')->comment('ID khách hàng');
            $table->enum('refund_type', ['cancel', 'return'])->comment('Loại hoàn tiền: hủy đơn hoặc trả hàng');
            $table->string('cancel_reason')->nullable()->comment('Lý do hủy/trả hàng');
            $table->text('reason_description')->nullable()->comment('Mô tả chi tiết lý do');
            $table->decimal('refund_amount', 15, 2)->comment('Số tiền hoàn');
            $table->string('bank_name')->nullable()->comment('Tên ngân hàng');
            $table->string('bank_account')->nullable()->comment('Số tài khoản');
            $table->string('account_holder')->nullable()->comment('Tên chủ tài khoản');
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending')->comment('Trạng thái');
            $table->text('admin_note')->nullable()->comment('Ghi chú của admin');
            $table->unsignedBigInteger('processed_by')->nullable()->comment('ID admin xử lý');
            $table->timestamp('processed_at')->nullable()->comment('Thời gian xử lý');
            $table->timestamps();

            // Khóa ngoại
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('processed_by')->references('id')->on('users')->onDelete('set null');

            // Index
            $table->index('order_id');
            $table->index('user_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refunds');
    }
};

