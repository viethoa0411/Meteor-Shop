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
        Schema::table('shipping_settings', function (Blueprint $table) {
            $table->boolean('free_shipping_enabled')->default(true)->after('free_shipping_threshold');
            $table->decimal('installation_fee', 15, 0)->default(0)->after('fast_label');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipping_settings', function (Blueprint $table) {
            $table->dropColumn(['free_shipping_enabled', 'installation_fee']);
        });
    }
};

