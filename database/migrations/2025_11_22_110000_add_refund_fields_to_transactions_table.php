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
        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('refund_id')->nullable()->after('order_id')->comment('ID yêu cầu hoàn tiền');
            $table->unsignedBigInteger('processed_by')->nullable()->after('completed_at')->comment('ID admin xử lý giao dịch');
            $table->foreign('refund_id')->references('id')->on('refunds')->onDelete('set null');
            $table->foreign('processed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['refund_id']);
            $table->dropForeign(['processed_by']);
            $table->dropColumn(['refund_id', 'processed_by']);
        });
    }
};

