<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('post_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('post_tag', function (Blueprint $table) {
            $table->unsignedBigInteger('post_id');
            $table->unsignedBigInteger('tag_id');
            $table->primary(['post_id','tag_id']);
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            $table->foreign('tag_id')->references('id')->on('tags')->onDelete('cascade');
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable()->after('id');
            $table->boolean('is_featured')->default(false)->after('status');
            $table->string('meta_title')->nullable()->after('content');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->string('og_image')->nullable()->after('meta_description');
            $table->foreign('category_id')->references('id')->on('post_categories')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn(['category_id','is_featured','meta_title','meta_description','og_image']);
        });
        Schema::dropIfExists('post_tag');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('post_categories');
    }
};


