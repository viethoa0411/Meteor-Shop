<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('transaction_id')->nullable()->unique();
            $table->enum('payment_method', ['cash', 'bank', 'momo', 'paypal', 'stripe', 'zalopay'])->default('cash');
            $table->enum('status', ['pending', 'processing', 'paid', 'failed', 'refunded', 'partially_refunded'])->default('pending');
            $table->decimal('amount', 10, 2);
            $table->decimal('refunded_amount', 10, 2)->default(0);
            $table->string('currency', 3)->default('VND');
            $table->text('payment_data')->nullable(); // JSON data from payment gateway
            $table->text('notes')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->unsignedBigInteger('processed_by')->nullable(); // Admin user ID
            $table->timestamps();

            $table->index('order_id');
            $table->index('transaction_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_payments');
    }
};

