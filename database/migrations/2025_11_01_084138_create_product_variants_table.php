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
            $table->string('color_name')->nullable();  // tên màu
            $table->string('color_code')->nullable();   // mã màu
            $table->decimal('length', 8, 2)->nullable();   // chiều dài
            $table->decimal('width', 8, 2)->nullable();   // chiều rộng
            $table->decimal('height', 8, 2)->nullable();   // chiều cao

            // Dữ liệu riêng cho biến thể
            $table->decimal('price', 10, 2)->nullable();   // giá
            $table->integer('stock')->default(0);   // tồn kho
            $table->string('sku')->nullable();   // mã sản phẩm chi tiết
            
            $table->timestamps();

            $table->unique(['product_id', 'color_code', 'length', 'width', 'height'], 'uniq_product_variant');




        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
