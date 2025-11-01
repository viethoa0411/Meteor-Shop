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
            $table->index('product_id');
            // (tùy chọn) SKU là duy nhất
            // $table->unique('sku');
        });
    }

public function down(): void
{
    Schema::table('product_variants', function (Blueprint $table) {
        $table->dropIndex(['product_id']);
        // $table->dropUnique(['sku']);
    });
}
};
