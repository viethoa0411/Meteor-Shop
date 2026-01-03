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
            if (!Schema::hasColumn('shipping_settings', 'same_product_discount_percent')) {
                $table->decimal('same_product_discount_percent', 5, 2)->default(0)->after('same_order_discount_percent')
                    ->comment('Phần trăm giảm giá phí vận chuyển khi mua nhiều sản phẩm cùng loại (quantity > 1)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipping_settings', function (Blueprint $table) {
            if (Schema::hasColumn('shipping_settings', 'same_product_discount_percent')) {
                $table->dropColumn('same_product_discount_percent');
            }
        });
    }
};
