<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Thêm 'wallet' vào enum payment_method trong bảng orders
     */
    public function up(): void
    {
        // MySQL cần ALTER TABLE để thay đổi ENUM
        DB::statement("ALTER TABLE orders MODIFY COLUMN payment_method ENUM('cash', 'bank', 'momo', 'paypal', 'wallet') DEFAULT 'cash'");
    }

    public function down(): void
    {
        // Rollback - xóa wallet khỏi enum
        DB::statement("ALTER TABLE orders MODIFY COLUMN payment_method ENUM('cash', 'bank', 'momo', 'paypal') DEFAULT 'cash'");
    }
};

