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
        Schema::table('products', function (Blueprint $table) {
            $table->string('sku')->nullable()->after('slug');
            $table->decimal('sale_price', 10, 2)->nullable()->after('price');
            $table->text('short_description')->nullable()->after('description');
            $table->decimal('rating_avg', 3, 2)->default(0)->after('stock');
            $table->integer('total_sold')->default(0)->after('rating_avg');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['sku', 'sale_price', 'short_description', 'rating_avg', 'total_sold']);
        });
    }
};
