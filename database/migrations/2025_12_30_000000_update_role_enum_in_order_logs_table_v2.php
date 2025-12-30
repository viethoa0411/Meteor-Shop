<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE order_logs MODIFY COLUMN role ENUM('admin','staff','customer','system') DEFAULT 'admin'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Lưu ý: Nếu có dữ liệu 'system' rồi thì rollback sẽ bị lỗi data truncated.
        // Nhưng đây là migration fix lỗi nên chấp nhận rủi ro hoặc xử lý data trước khi rollback.
        // Ở đây ta cứ giữ nguyên logic rollback cơ bản.
        DB::statement("DELETE FROM order_logs WHERE role = 'system'");
        DB::statement("ALTER TABLE order_logs MODIFY COLUMN role ENUM('admin','staff','customer') DEFAULT 'admin'");
    }
};
