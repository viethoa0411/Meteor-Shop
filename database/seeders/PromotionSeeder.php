<?php

namespace Database\Seeders;

use App\Models\Promotion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PromotionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $promotions = [
            [
                'code' => 'WELCOME10',
                'name' => 'Ưu đãi khách hàng mới',
                'description' => 'Giảm 10% cho đơn hàng đầu tiên.',
                'discount_type' => 'percent',
                'discount_value' => 10,
                'start_date' => $now->copy()->subDays(30),
                'end_date' => $now->copy()->addDays(60),
                'usage_limit' => 500,
                'used_count' => 120,
                'status' => 'active',
            ],
            [
                'code' => 'FLASH200K',
                'name' => 'Flash Sale cuối tuần',
                'description' => 'Giảm 200.000đ cho đơn từ 2.000.000đ.',
                'discount_type' => 'fixed',
                'discount_value' => 200000,
                'start_date' => $now->copy()->subDays(5),
                'end_date' => $now->copy()->addDays(2),
                'usage_limit' => 100,
                'used_count' => 35,
                'status' => 'active',
            ],
            [
                'code' => 'MIDSEASON15',
                'name' => 'Mid-season Sale',
                'description' => 'Giảm 15% cho toàn bộ sản phẩm đang hoạt động.',
                'discount_type' => 'percent',
                'discount_value' => 15,
                'start_date' => $now->copy()->subMonths(1),
                'end_date' => $now->copy()->addMonths(1),
                'usage_limit' => null,
                'used_count' => 250,
                'status' => 'active',
            ],
            [
                'code' => 'BLACKFRIDAY',
                'name' => 'Black Friday',
                'description' => 'Đã kết thúc.',
                'discount_type' => 'percent',
                'discount_value' => 30,
                'start_date' => $now->copy()->subMonths(2),
                'end_date' => $now->copy()->subMonths(2)->addDays(5),
                'usage_limit' => 1000,
                'used_count' => 990,
                'status' => 'expired',
            ],
            [
                'code' => 'XMAS500K',
                'name' => 'Christmas Gift',
                'description' => 'Chương trình chuẩn bị mở.',
                'discount_type' => 'fixed',
                'discount_value' => 500000,
                'start_date' => $now->copy()->addDays(15),
                'end_date' => $now->copy()->addDays(30),
                'usage_limit' => 300,
                'used_count' => 0,
                'status' => 'inactive',
            ],
        ];

        foreach ($promotions as $promotion) {
            Promotion::updateOrCreate(
                ['code' => Str::upper($promotion['code'])],
                $promotion
            );
        }
    }
}
