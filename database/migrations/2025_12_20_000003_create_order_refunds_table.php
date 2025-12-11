<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_payment_id')->nullable()->constrained('order_payments')->onDelete('set null');
            $table->string('refund_code')->unique();
            $table->enum('type', ['full', 'partial'])->default('full');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('VND');
            $table->text('reason')->nullable();
            $table->text('notes')->nullable();
            $table->text('refund_data')->nullable(); // JSON data from payment gateway
            $table->string('refund_transaction_id')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->unsignedBigInteger('processed_by')->nullable(); // Admin user ID
            $table->timestamps();

            $table->index('order_id');
            $table->index('refund_code');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_refunds');
    }
};

