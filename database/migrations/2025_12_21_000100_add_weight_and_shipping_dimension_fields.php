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
        Schema::table('product_variants', function (Blueprint $table) {
            if (!Schema::hasColumn('product_variants', 'weight')) {
                $table->decimal('weight', 8, 2)->default(0)->after('height');
            }
        });

        Schema::table('shipping_settings', function (Blueprint $table) {
            $table->decimal('first_length_price', 15, 0)->default(0)->after('other_province_fee');
            $table->decimal('next_length_price', 15, 0)->default(0)->after('first_length_price');

            $table->decimal('first_width_price', 15, 0)->default(0)->after('next_length_price');
            $table->decimal('next_width_price', 15, 0)->default(0)->after('first_width_price');

            $table->decimal('first_height_price', 15, 0)->default(0)->after('next_width_price');
            $table->decimal('next_height_price', 15, 0)->default(0)->after('first_height_price');

            $table->decimal('first_weight_price', 15, 0)->default(0)->after('next_height_price');
            $table->decimal('next_weight_price', 15, 0)->default(0)->after('first_weight_price');

            $table->string('express_surcharge_type', 10)->default('percent')->after('next_weight_price');
            $table->decimal('express_surcharge_value', 15, 0)->default(0)->after('express_surcharge_type');

            $table->string('fast_surcharge_type', 10)->default('percent')->after('express_surcharge_value');
            $table->decimal('fast_surcharge_value', 15, 0)->default(0)->after('fast_surcharge_type');

            $table->string('express_label')->default('Giao hàng nhanh')->after('fast_surcharge_value');
            $table->string('fast_label')->default('Giao hàng hỏa tốc')->after('express_label');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            if (Schema::hasColumn('product_variants', 'weight')) {
                $table->dropColumn('weight');
            }
        });

        Schema::table('shipping_settings', function (Blueprint $table) {
            $table->dropColumn([
                'first_length_price',
                'next_length_price',
                'first_width_price',
                'next_width_price',
                'first_height_price',
                'next_height_price',
                'first_weight_price',
                'next_weight_price',
                'express_surcharge_type',
                'express_surcharge_value',
                'fast_surcharge_type',
                'fast_surcharge_value',
                'express_label',
                'fast_label',
            ]);
        });
    }
};


