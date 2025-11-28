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
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('ID của admin sở hữu ví');
            $table->decimal('balance', 15, 2)->default(0)->comment('Số dư hiện tại');
            $table->string('bank_name')->nullable()->comment('Tên ngân hàng');
            $table->string('bank_account')->nullable()->comment('Số tài khoản');
            $table->string('account_holder')->nullable()->comment('Tên chủ tài khoản');
            $table->string('qr_code_template')->nullable()->comment('Template QR code');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            // Khóa ngoại
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Index
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
