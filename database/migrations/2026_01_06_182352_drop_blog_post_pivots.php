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
        // Xóa các bảng pivot nếu tồn tại
        Schema::dropIfExists('blog_post_category');
        Schema::dropIfExists('blog_post_tag');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Không khôi phục lại các bảng pivot
    }
};
