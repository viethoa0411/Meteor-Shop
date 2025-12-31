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
        Schema::table('cart_items', function (Blueprint $table) {
            // Check if index exists before dropping to avoid errors
            // This requires a raw query since Schema::hasIndex is not always reliable without Doctrine
            
            $exists = DB::select("SHOW INDEXES FROM cart_items WHERE Key_name = 'cart_items_cart_id_product_id_unique'");
            
            if (count($exists) > 0) {
                $table->dropUnique('cart_items_cart_id_product_id_unique');
            }
            
            // Also check for the array-generated name just in case
            $exists2 = DB::select("SHOW INDEXES FROM cart_items WHERE Key_name = 'cart_items_product_id_cart_id_unique'");
            if (count($exists2) > 0) {
                 $table->dropUnique('cart_items_product_id_cart_id_unique');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            // We do not want to restore the constraint that causes bugs.
        });
    }
};
