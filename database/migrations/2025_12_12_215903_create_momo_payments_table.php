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
        Schema::create('momo_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('partner_code')->nullable();
            $table->string('request_id')->nullable();
            $table->string('order_id_momo')->nullable(); // orderId của Momo
            $table->string('trans_id')->nullable();
            $table->string('pay_type')->nullable();
            $table->decimal('amount', 15, 2);
            $table->integer('result_code')->nullable();
            $table->string('message')->nullable();
            $table->string('response_time')->nullable();
            $table->text('extra_data')->nullable();
            $table->text('signature')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('momo_payments');
    }
};
