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
        Schema::create('transaction_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaction_id')->comment('ID giao dịch');
            $table->unsignedBigInteger('user_id')->comment('ID người thực hiện');
            $table->string('action')->comment('Hành động: confirm, cancel, refund, update');
            $table->text('description')->nullable()->comment('Mô tả hành động');
            $table->json('old_data')->nullable()->comment('Dữ liệu cũ');
            $table->json('new_data')->nullable()->comment('Dữ liệu mới');
            $table->timestamps();

            // Khóa ngoại
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Index
            $table->index('transaction_id');
            $table->index('user_id');
            $table->index('action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_logs');
    }
};

