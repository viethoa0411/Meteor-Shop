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
            // Thêm trường khoảng cách mặc định (km) khi không tìm thấy trong database
            $table->decimal('default_distance_km', 8, 2)->default(10.00)->after('fee_per_km');

            // Xóa các trường liên quan đến miễn phí vận chuyển
            $table->dropColumn(['free_shipping_threshold', 'free_shipping_enabled']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipping_settings', function (Blueprint $table) {
            // Khôi phục các trường miễn phí vận chuyển
            $table->decimal('free_shipping_threshold', 15, 0)->default(0);
            $table->boolean('free_shipping_enabled')->default(false);

            // Xóa trường khoảng cách mặc định
            $table->dropColumn('default_distance_km');
        });
    }
};
