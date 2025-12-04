<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('monthly_targets', function (Blueprint $table) {
            $table->id();
            $table->year('year'); // Năm
            $table->tinyInteger('month'); // Tháng (1-12)
            $table->bigInteger('target_amount'); // Mục tiêu doanh thu
            $table->timestamps();

            $table->unique(['year', 'month']); // tránh trùng tháng/năm
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_targets');
    }
};
