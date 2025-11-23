<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['internal', 'customer', 'system'])->default('internal');
            $table->text('note');
            $table->boolean('is_pinned')->default(false);
            $table->json('attachments')->nullable(); // Array of file paths
            $table->unsignedBigInteger('created_by')->nullable(); // User ID (admin or customer)
            $table->unsignedBigInteger('tagged_user_id')->nullable(); // Tagged admin user
            $table->timestamps();

            $table->index('order_id');
            $table->index('type');
            $table->index('is_pinned');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_notes');
    }
};

