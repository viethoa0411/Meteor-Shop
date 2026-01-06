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
        Schema::table('blogs', function (Blueprint $table) {
            if (!Schema::hasColumn('blogs', 'published_at')) {
                $table->timestamp('published_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('blogs', 'view_count')) {
                $table->unsignedBigInteger('view_count')->default(0)->after('published_at');
            }
            if (!Schema::hasColumn('blogs', 'seo_title')) {
                $table->string('seo_title')->nullable()->after('view_count');
            }
            if (!Schema::hasColumn('blogs', 'seo_description')) {
                $table->text('seo_description')->nullable()->after('seo_title');
            }
            if (!Schema::hasColumn('blogs', 'canonical_url')) {
                $table->string('canonical_url')->nullable()->after('seo_description');
            }
            if (!Schema::hasColumn('blogs', 'noindex')) {
                $table->boolean('noindex')->default(false)->after('canonical_url');
            }
            // deleted_at đã được thêm bằng migration riêng, tránh trùng cột
            if (!Schema::hasColumn('blogs', 'deleted_at')) {
                $table->softDeletes()->after('updated_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->dropColumn([
                'published_at',
                'view_count',
                'seo_title',
                'seo_description',
                'canonical_url',
                'noindex',
                'deleted_at'
            ]);
        });
    }
};
