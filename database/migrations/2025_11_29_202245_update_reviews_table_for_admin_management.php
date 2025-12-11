<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'rejected', 'hidden'])->default('pending')->after('is_verified_purchase');
            $table->integer('reported_count')->default(0)->after('status');
        });
        
        // Add content column if comment exists, otherwise rename
        if (Schema::hasColumn('reviews', 'comment') && !Schema::hasColumn('reviews', 'content')) {
            Schema::table('reviews', function (Blueprint $table) {
                $table->text('content')->nullable()->after('rating');
            });
            // Copy data from comment to content
            DB::statement('UPDATE reviews SET content = comment WHERE content IS NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn(['status', 'reported_count']);
            if (Schema::hasColumn('reviews', 'content')) {
                $table->dropColumn('content');
            }
        });
    }
};
