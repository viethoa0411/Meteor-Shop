<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_timelines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('event_type'); // status_changed, payment_received, shipment_created, etc.
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('old_value')->nullable();
            $table->string('new_value')->nullable();
            $table->json('metadata')->nullable(); // Additional data
            $table->unsignedBigInteger('user_id')->nullable(); // User who performed the action
            $table->string('user_type')->nullable(); // admin, customer, system
            $table->timestamps();

            $table->index('order_id');
            $table->index('event_type');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_timelines');
    }
};

