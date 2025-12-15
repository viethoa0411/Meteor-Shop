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
        Schema::create('shipping_distances', function (Blueprint $table) {
            $table->id();
            $table->string('province_name', 255)->comment('Tên Tỉnh/Thành Phố');
            $table->string('district_name', 255)->comment('Tên Quận/Huyện/Thị Xã');
            $table->decimal('distance_km', 8, 2)->default(0)->comment('Số Km từ kho hàng đến địa chỉ này');
            $table->timestamps();
            
            // Index để tối ưu truy vấn
            $table->index(['province_name', 'district_name']);
            $table->index('province_name');
            $table->index('district_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_distances');
    }
};
