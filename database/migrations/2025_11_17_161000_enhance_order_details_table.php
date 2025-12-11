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
        Schema::table('order_details', function (Blueprint $table) {
            $table->string('product_name')->nullable()->after('product_id');
            $table->unsignedBigInteger('variant_id')->nullable()->after('product_name');
            $table->string('variant_name')->nullable()->after('variant_id');
            $table->string('variant_sku')->nullable()->after('variant_name');
            $table->decimal('total_price', 10, 2)->nullable()->after('subtotal');
            $table->string('image_path')->nullable()->after('total_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropColumn([
                'product_name',
                'variant_id',
                'variant_name',
                'variant_sku',
                'total_price',
                'image_path',
            ]);
        });
    }
};

