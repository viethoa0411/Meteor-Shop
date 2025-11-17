<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $banners = [
            [
                'title' => 'BST Sofa Thu 2025',
                'description' => 'Khám phá bộ sưu tập sofa mới nhất với ưu đãi 15%.',
                'image' => 'banners/images/sofa-collection.jpg',
                'link' => '/san-pham/sofa',
                'position' => 1,
                'sort_order' => 1,
                'status' => 'active',
                'start_date' => $now->copy()->subDays(10),
                'end_date' => $now->copy()->addDays(20),
            ],
            [
                'title' => 'Combo Nội Thất Phòng Ngủ',
                'description' => 'Giảm thêm 500K khi mua combo giường + tủ + đèn.',
                'image' => 'banners/images/bedroom-combo.jpg',
                'link' => '/san-pham/phong-ngu',
                'position' => 2,
                'sort_order' => 2,
                'status' => 'active',
                'start_date' => $now->copy()->subDays(5),
                'end_date' => $now->copy()->addDays(25),
            ],
            [
                'title' => 'Gợi Ý Góc Làm Việc',
                'description' => 'Thiết kế góc làm việc chuẩn công thái học.',
                'image' => 'banners/images/workspace.jpg',
                'link' => '/bo-suu-tap/workspace',
                'position' => 3,
                'sort_order' => 3,
                'status' => 'active',
                'start_date' => $now->copy()->subDays(1),
                'end_date' => $now->copy()->addDays(45),
            ],
            [
                'title' => 'Phong Cách Tối Giản',
                'description' => 'BST Minimalism với gam màu trung tính.',
                'image' => 'banners/images/minimalism.jpg',
                'link' => '/bo-suu-tap/minimalism',
                'position' => 4,
                'sort_order' => 4,
                'status' => 'inactive',
                'start_date' => $now->copy()->addDays(10),
                'end_date' => $now->copy()->addDays(50),
            ],
            [
                'title' => 'Flash Sale Cuối Tuần',
                'description' => 'Áp dụng cho 50 sản phẩm bán chạy nhất.',
                'image' => 'banners/images/flash-sale.jpg',
                'link' => '/khuyen-mai',
                'position' => 5,
                'sort_order' => 5,
                'status' => 'active',
                'start_date' => $now->copy()->subDays(2),
                'end_date' => $now->copy()->addDays(1),
            ],
        ];

        foreach ($banners as $banner) {
            Banner::updateOrCreate(
                ['title' => $banner['title']],
                $banner
            );
        }
    }
}
