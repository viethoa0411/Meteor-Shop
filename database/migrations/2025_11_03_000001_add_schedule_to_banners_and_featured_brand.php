<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('banners')) {
            Schema::table('banners', function (Blueprint $table) {
                if (!Schema::hasColumn('banners', 'start_at')) {
                    $table->timestamp('start_at')->nullable()->after('status');
                }
                if (!Schema::hasColumn('banners', 'end_at')) {
                    $table->timestamp('end_at')->nullable()->after('start_at');
                }
            });
        }
        if (Schema::hasTable('brands')) {
            Schema::table('brands', function (Blueprint $table) {
                if (!Schema::hasColumn('brands', 'is_featured')) {
                    $table->boolean('is_featured')->default(false)->after('status');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('banners')) {
            Schema::table('banners', function (Blueprint $table) {
                if (Schema::hasColumn('banners', 'start_at')) $table->dropColumn('start_at');
                if (Schema::hasColumn('banners', 'end_at')) $table->dropColumn('end_at');
            });
        }
        if (Schema::hasTable('brands')) {
            Schema::table('brands', function (Blueprint $table) {
                if (Schema::hasColumn('brands', 'is_featured')) $table->dropColumn('is_featured');
            });
        }
    }
};


