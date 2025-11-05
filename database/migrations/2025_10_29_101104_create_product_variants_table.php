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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');

            // Thuộc tính biến thể
            $table->string('color_name')->nullable();          // VD: "Xám nhạt"
            $table->string('color_code', 20)->nullable();      // VD: "#D9D9D9"
            $table->decimal('length', 8, 2)->nullable();
            $table->decimal('width',  8, 2)->nullable();
            $table->decimal('height', 8, 2)->nullable();
            // Dữ liệu riêng cho biến thể (nếu cần)
            $table->decimal('price', 10, 2)->nullable();
            $table->integer('stock')->default(0);
            $table->string('sku')->nullable();

            $table->timestamps();

            // (Khuyến nghị) không trùng 1 tổ hợp trong 1 product
            $table->unique(['product_id','color_code','length','width','height'], 'uniq_product_variant');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
