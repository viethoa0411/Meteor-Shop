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
            if (!Schema::hasColumn('shipping_settings', 'default_distance_km')) {
                $table->decimal('default_distance_km', 8, 2)->default(10.00)->after('fee_per_km');
            }

            if (!Schema::hasColumn('shipping_settings', 'first_length_price')) {
                $table->decimal('first_length_price', 10, 0)->default(10000);
            }
            if (!Schema::hasColumn('shipping_settings', 'next_length_price')) {
                $table->decimal('next_length_price', 10, 0)->default(5000);
            }

            if (!Schema::hasColumn('shipping_settings', 'first_width_price')) {
                $table->decimal('first_width_price', 10, 0)->default(8000);
            }
            if (!Schema::hasColumn('shipping_settings', 'next_width_price')) {
                $table->decimal('next_width_price', 10, 0)->default(4000);
            }

            if (!Schema::hasColumn('shipping_settings', 'first_height_price')) {
                $table->decimal('first_height_price', 10, 0)->default(8000);
            }
            if (!Schema::hasColumn('shipping_settings', 'next_height_price')) {
                $table->decimal('next_height_price', 10, 0)->default(4000);
            }

            if (!Schema::hasColumn('shipping_settings', 'first_weight_price')) {
                $table->decimal('first_weight_price', 10, 0)->default(15000);
            }
            if (!Schema::hasColumn('shipping_settings', 'next_weight_price')) {
                $table->decimal('next_weight_price', 10, 0)->default(7000);
            }

            if (!Schema::hasColumn('shipping_settings', 'express_surcharge_type')) {
                $table->string('express_surcharge_type')->default('percent');
            }
            if (!Schema::hasColumn('shipping_settings', 'express_surcharge_value')) {
                $table->decimal('express_surcharge_value', 8, 2)->default(20.00);
            }

            if (!Schema::hasColumn('shipping_settings', 'fast_surcharge_type')) {
                $table->string('fast_surcharge_type')->default('percent');
            }
            if (!Schema::hasColumn('shipping_settings', 'fast_surcharge_value')) {
                $table->decimal('fast_surcharge_value', 8, 2)->default(40.00);
            }

            if (!Schema::hasColumn('shipping_settings', 'express_label')) {
                $table->string('express_label')->default('Giao hàng nhanh');
            }
            if (!Schema::hasColumn('shipping_settings', 'fast_label')) {
                $table->string('fast_label')->default('Giao hàng hỏa tốc');
            }

            if (!Schema::hasColumn('shipping_settings', 'free_shipping_threshold')) {
                $table->bigInteger('free_shipping_threshold')->default(10000000);
            }
            if (!Schema::hasColumn('shipping_settings', 'free_shipping_enabled')) {
                $table->boolean('free_shipping_enabled')->default(true);
            }

            if (!Schema::hasColumn('shipping_settings', 'installation_fee')) {
                $table->decimal('installation_fee', 10, 0)->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipping_settings', function (Blueprint $table) {
            $columns = [
                'default_distance_km',
                'first_length_price', 'next_length_price',
                'first_width_price', 'next_width_price',
                'first_height_price', 'next_height_price',
                'first_weight_price', 'next_weight_price',
                'express_surcharge_type', 'express_surcharge_value',
                'fast_surcharge_type', 'fast_surcharge_value',
                'express_label', 'fast_label',
                'free_shipping_threshold', 'free_shipping_enabled',
                'installation_fee'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('shipping_settings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
