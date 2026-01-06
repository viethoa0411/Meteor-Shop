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

            if (!Schema::hasColumn('shipping_settings', 'volume_price_per_m3')) {
                $table->decimal('volume_price_per_m3', 10, 0)->default(5000)->after('same_order_discount_percent')
                    ->comment('Phí vận chuyển cho mỗi sản phẩm tiếp theo (sau SP đầu tiên)');
            }
            if (!Schema::hasColumn('shipping_settings', 'min_shipping_fee')) {
                $table->decimal('min_shipping_fee', 10, 0)->default(40000)->after('volume_price_per_m3')
                    ->comment('Phí vận chuyển cho sản phẩm đầu tiên');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipping_settings', function (Blueprint $table) {
            if (Schema::hasColumn('shipping_settings', 'volume_price_per_m3')) {
                $table->dropColumn('volume_price_per_m3');
            }
            if (Schema::hasColumn('shipping_settings', 'min_shipping_fee')) {
                $table->dropColumn('min_shipping_fee');
            }

        });
    }
};
