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
        Schema::create('shipping_settings', function (Blueprint $table) {
            $table->id();
            
            // Địa chỉ gốc (kho hàng)
            $table->string('origin_address')->nullable(); // Địa chỉ chi tiết
            $table->string('origin_city')->nullable(); // Tỉnh/Thành phố
            $table->string('origin_district')->nullable(); // Quận/Huyện
            $table->string('origin_ward')->nullable(); // Phường/Xã
            
            // Phí vận chuyển
            $table->decimal('base_fee', 15, 0)->default(30000); // Phí cơ bản
            $table->decimal('fee_per_km', 15, 0)->default(5000); // Phí mỗi km
            
            // Ngưỡng miễn phí vận chuyển
            $table->decimal('free_shipping_threshold', 15, 0)->default(10000000); // 10 triệu
            
            // Phí theo khu vực (nội thành, ngoại thành, tỉnh khác)
            $table->decimal('inner_city_fee', 15, 0)->default(30000); // Nội thành
            $table->decimal('outer_city_fee', 15, 0)->default(50000); // Ngoại thành
            $table->decimal('other_province_fee', 15, 0)->default(80000); // Tỉnh khác
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_settings');
    }
};

