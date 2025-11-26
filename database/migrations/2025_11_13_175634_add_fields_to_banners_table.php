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
        Schema::table('banners', function (Blueprint $table) {
            $table->string('display_location')->default('home')->after('link'); // home, sidebar, popup, product_detail, etc.
            $table->dateTime('start_date')->nullable()->after('display_location');
            $table->dateTime('end_date')->nullable()->after('start_date');
            $table->integer('sort_order')->default(0)->after('position');
            $table->softDeletes()->after('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn(['display_location', 'start_date', 'end_date', 'sort_order', 'deleted_at']);
        });
    }
};
