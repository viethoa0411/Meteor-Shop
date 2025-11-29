<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('return_code')->unique();
            $table->enum('type', ['refund', 'exchange', 'repair'])->default('refund');
            $table->enum('status', ['requested', 'approved', 'rejected', 'in_transit', 'received', 'processed', 'completed', 'cancelled'])->default('requested');
            $table->text('reason')->nullable();
            $table->text('description')->nullable();
            $table->enum('product_condition', ['new', 'like_new', 'used', 'damaged'])->nullable();
            $table->json('attachments')->nullable(); // Array of image paths
            $table->text('admin_notes')->nullable();
            $table->enum('resolution', ['refund', 'exchange', 'repair', 'reject'])->nullable();
            $table->foreignId('exchange_product_id')->nullable()->constrained('products')->onDelete('set null');
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->unsignedBigInteger('processed_by')->nullable(); // Admin user ID
            $table->timestamps();

            $table->index('order_id');
            $table->index('return_code');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_returns');
    }
};

