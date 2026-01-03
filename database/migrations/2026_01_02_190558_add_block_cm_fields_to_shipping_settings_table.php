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
            if (!Schema::hasColumn('shipping_settings', 'length_block_cm')) {
                $table->integer('length_block_cm')->default(200)->after('next_length_price')
                    ->comment('Số cm cho block đầu tiên của chiều dài (ví dụ: 200cm)');
            }
            if (!Schema::hasColumn('shipping_settings', 'width_block_cm')) {
                $table->integer('width_block_cm')->default(200)->after('next_width_price')
                    ->comment('Số cm cho block đầu tiên của chiều rộng (ví dụ: 200cm)');
            }
            if (!Schema::hasColumn('shipping_settings', 'height_block_cm')) {
                $table->integer('height_block_cm')->default(200)->after('next_height_price')
                    ->comment('Số cm cho block đầu tiên của chiều cao (ví dụ: 200cm)');
            }
            if (!Schema::hasColumn('shipping_settings', 'weight_block_kg')) {
                $table->integer('weight_block_kg')->default(10)->after('next_weight_price')
                    ->comment('Số kg cho block đầu tiên của cân nặng (ví dụ: 10kg)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipping_settings', function (Blueprint $table) {
            if (Schema::hasColumn('shipping_settings', 'length_block_cm')) {
                $table->dropColumn('length_block_cm');
            }
            if (Schema::hasColumn('shipping_settings', 'width_block_cm')) {
                $table->dropColumn('width_block_cm');
            }
            if (Schema::hasColumn('shipping_settings', 'height_block_cm')) {
                $table->dropColumn('height_block_cm');
            }
            if (Schema::hasColumn('shipping_settings', 'weight_block_kg')) {
                $table->dropColumn('weight_block_kg');
            }
        });
    }
};
