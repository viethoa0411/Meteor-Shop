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
            // Kiểm tra xem cột đã tồn tại chưa trước khi thêm
            if (!Schema::hasColumn('product_variants', 'weight')) {
                $table->integer('weight')->nullable()->after('height');
            }
            if (!Schema::hasColumn('product_variants', 'weight_unit')) {
                $table->string('weight_unit')->default('kg')->after('weight');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('product_variants', function (Blueprint $table) {
            if (Schema::hasColumn('product_variants', 'weight_unit')) {
                $table->dropColumn('weight_unit');
            }
            if (Schema::hasColumn('product_variants', 'weight')) {
                $table->dropColumn('weight');
            }
        });
    }
};
