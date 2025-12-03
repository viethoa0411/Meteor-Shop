<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bảng cài đặt ví (thông tin ngân hàng admin để nhận tiền nạp)
     */
    public function up(): void
    {
        Schema::create('wallet_settings', function (Blueprint $table) {
            $table->id();
            $table->string('bank_name')->default('MB Bank');
            $table->string('bank_account')->default('0123456789');
            $table->string('account_holder')->default('NGUYEN VAN A');
            $table->string('bank_code')->default('MB'); // Mã ngân hàng cho VietQR
            $table->string('support_phone')->default('0123456789');
            $table->string('support_email')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_settings');
    }
};

