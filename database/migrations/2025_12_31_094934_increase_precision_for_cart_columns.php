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
        // Use raw SQL to avoid requiring doctrine/dbal
        DB::statement("ALTER TABLE cart_items 
            MODIFY COLUMN price DECIMAL(15,2) NOT NULL,
            MODIFY COLUMN subtotal DECIMAL(15,2) NOT NULL");

        DB::statement("ALTER TABLE carts 
            MODIFY COLUMN total_price DECIMAL(15,2) NOT NULL DEFAULT 0");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE cart_items 
            MODIFY COLUMN price DECIMAL(10,2) NOT NULL,
            MODIFY COLUMN subtotal DECIMAL(10,2) NOT NULL");

        DB::statement("ALTER TABLE carts 
            MODIFY COLUMN total_price DECIMAL(10,2) NOT NULL DEFAULT 0");
    }
};
