<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 更新 order_status enum 以支持所有新状态
        DB::statement(
            "ALTER TABLE orders MODIFY COLUMN order_status 
             ENUM(
                'pending',
                'awaiting_payment',
                'paid',
                'processing',
                'confirmed',
                'packed',
                'shipping',
                'delivered',
                'completed',
                'cancelled',
                'return_requested',
                'returned',
                'refunded',
                'partial_refund'
             ) 
             DEFAULT 'pending'"
        );

        // 更新 payment_status enum
        DB::statement(
            "ALTER TABLE orders MODIFY COLUMN payment_status 
             ENUM('pending', 'awaiting_payment', 'paid', 'failed', 'refunded', 'partially_refunded') 
             DEFAULT 'pending'"
        );

        // 添加 shipping_status 字段（如果还没有）
        if (!Schema::hasColumn('orders', 'shipping_status')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->enum('shipping_status', [
                    'pending',
                    'preparing',
                    'ready_to_ship',
                    'shipped',
                    'in_transit',
                    'out_for_delivery',
                    'delivered',
                    'failed',
                    'returned'
                ])->default('pending')->after('order_status');
            });
        }
    }

    public function down(): void
    {
        // 恢复原来的状态
        DB::statement(
            "ALTER TABLE orders MODIFY COLUMN order_status 
             ENUM('pending','processing','shipping','completed','cancelled','return_requested','returned','refunded') 
             DEFAULT 'pending'"
        );

        DB::statement(
            "ALTER TABLE orders MODIFY COLUMN payment_status 
             ENUM('pending', 'paid', 'failed') 
             DEFAULT 'pending'"
        );

        if (Schema::hasColumn('orders', 'shipping_status')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('shipping_status');
            });
        }
    }
};


