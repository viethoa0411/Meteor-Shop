<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('promotions', function (Blueprint $table) {
            $table->enum('scope', ['all', 'category', 'product'])->default('all')->after('status');
            $table->decimal('max_discount', 10, 2)->nullable()->after('discount_value');
            $table->decimal('min_amount', 10, 2)->default(0)->after('max_discount');
            $table->unsignedInteger('min_orders')->default(0)->after('min_amount');
            $table->unsignedInteger('limit_per_user')->nullable()->after('usage_limit');
            $table->unsignedInteger('limit_global')->nullable()->after('limit_per_user');
        });
    }

    public function down(): void
    {
        Schema::table('promotions', function (Blueprint $table) {
            $table->dropColumn(['scope', 'max_discount', 'min_amount', 'min_orders', 'limit_per_user', 'limit_global']);
        });
    }
};

