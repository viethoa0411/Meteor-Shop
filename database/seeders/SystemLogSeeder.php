<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SystemLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userIds = User::pluck('id')->all();

        if (empty($userIds)) {
            $this->call(AdminUserSeeder::class);
            $userIds = User::pluck('id')->all();
        }

        $logs = [
            [
                'action' => 'login_success',
                'module' => 'auth',
                'description' => 'Admin đăng nhập thành công.',
            ],
            [
                'action' => 'create_product',
                'module' => 'products',
                'description' => 'Thêm sản phẩm Sofa Mây Breeze.',
            ],
            [
                'action' => 'update_order_status',
                'module' => 'orders',
                'description' => 'Cập nhật đơn #MT-0001 sang completed.',
            ],
            [
                'action' => 'delete_banner',
                'module' => 'banners',
                'description' => 'Xóa banner Flash Sale cũ.',
            ],
            [
                'action' => 'export_report',
                'module' => 'reports',
                'description' => 'Xuất báo cáo doanh thu tháng.',
            ],
        ];

        foreach ($logs as $index => $log) {
            DB::table('system_logs')->updateOrInsert(
                ['action' => $log['action'], 'module' => $log['module'], 'description' => $log['description']],
                array_merge($log, [
                    'user_id' => $userIds[$index % count($userIds)],
                    'ip_address' => '127.0.0.' . ($index + 1),
                    'user_agent' => 'Seeder Bot',
                    'created_at' => now()->subDays(5 - $index),
                    'updated_at' => now()->subDays(5 - $index),
                ])
            );
        }
    }
}
