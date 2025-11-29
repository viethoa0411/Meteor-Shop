<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_refund_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('refund_id')->constrained('order_refunds')->onDelete('cascade');
            $table->foreignId('order_detail_id')->constrained('order_details')->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('amount', 10, 2);
            $table->text('reason')->nullable();
            $table->timestamps();

            $table->index('refund_id');
            $table->index('order_detail_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_refund_items');
    }
};

