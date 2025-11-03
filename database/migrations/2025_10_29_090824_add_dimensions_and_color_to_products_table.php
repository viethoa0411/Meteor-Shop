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
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('length', 8, 2)->nullable()->comment('Chiều dài (cm)')->after('image');
            $table->decimal('width', 8, 2)->nullable()->comment('Chiều rộng (cm)')->after('length');
            $table->decimal('height', 8, 2)->nullable()->comment('Chiều cao (cm)')->after('width');
            $table->string('color_code', 20)->nullable()->comment('Mã màu hex, VD: #FF0000')->after('height');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['length', 'width', 'height', 'color_code']);
        });
    }
};