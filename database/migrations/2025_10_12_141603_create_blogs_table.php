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
        Schema::create('blogs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id'); // người tạo bài viết
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable(); // mô tả ngắn
            $table->longText('content');
            $table->string('thumbnail')->nullable(); // ảnh đại diện bài viết
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->timestamps();

            // Khóa ngoại
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
