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
            $table->string('color_name')->nulltable();  //tên màu
            $table->string('color_code')->nulltable();   // mã màu
            $table->decimal('length', 8, 2)->nulltable();   // chiều dài
            $table->decimal('width', 8, 2)->nulltable();   // chiều rộgn
            $table->decimal('heigh', 8, 2)->nulltable();   // chiều cao

            //Dữ liệu riêng cho biến thể
            $table->decimal('price', 10, 2)->nulltable();   // giá
            $table->integer('stock')->default();   // tồn kho
            $table->string('sku')->nulltable();   // mã sản phẩm chi tiết
            
            $table->timestamps();

            $table->unique([])



        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
