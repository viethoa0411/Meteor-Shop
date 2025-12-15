<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->decimal('weight', 8, 3)->after('price');
            $table->enum('weight_unit', ['g', 'kg', 'lb'])->default('kg')->after('weight');
            // Nếu bạn muốn ảnh cho variant
            $table->string('image')->nullable()->after('weight_unit');
        });
    }

    public function down()
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn(['weight', 'weight_unit', 'image']);
        });
    }
};
