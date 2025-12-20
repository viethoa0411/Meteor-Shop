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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // Admin user ID
            $table->string('type', 50); // order, product, review, chat, contact, etc.
            $table->string('level', 20)->default('info'); // info, warning, danger, success
            $table->string('title');
            $table->text('message');
            $table->string('url')->nullable(); // Link to related page
            $table->string('icon')->nullable(); // Bootstrap icon class
            $table->string('icon_color')->nullable(); // Bootstrap color class
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->json('metadata')->nullable(); // Additional data
            $table->timestamps();
            
            $table->index(['user_id', 'is_read']);
            $table->index(['type', 'created_at']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
