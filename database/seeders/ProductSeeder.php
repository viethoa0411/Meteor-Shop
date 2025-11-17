<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoryIds = Category::pluck('id')->all();
        $brandIds = Brand::pluck('id')->all();

        if (empty($categoryIds)) {
            $this->call(CategorySeeder::class);
            $categoryIds = Category::pluck('id')->all();
        }

        if (empty($brandIds)) {
            $this->call(BrandSeeder::class);
            $brandIds = Brand::pluck('id')->all();
        }

        $products = [
            [
                'name' => 'Sofa Mây Breeze',
                'description' => 'Ghế sofa mây mang phong cách Địa Trung Hải, chất liệu mây tre tự nhiên.',
                'price' => 15990000,
                'stock' => 15,
                'image' => 'products/sofa-breeze.jpg',
                'size' => '220cm',
                'color' => 'Be sáng',
                'length' => 220,
                'width' => 90,
                'height' => 85,
                'color_code' => '#F5E6D3',
                'status' => 'active',
            ],
            [
                'name' => 'Bàn Trà Aurora',
                'description' => 'Bàn trà mặt đá kết hợp chân gỗ óc chó, thiết kế tối giản.',
                'price' => 5490000,
                'stock' => 30,
                'image' => 'products/ban-tra-aurora.jpg',
                'size' => '120cm',
                'color' => 'Trắng kem',
                'length' => 120,
                'width' => 60,
                'height' => 40,
                'color_code' => '#F9F6F0',
                'status' => 'active',
            ],
            [
                'name' => 'Ghế Công Thái Học Nova',
                'description' => 'Ghế công thái học phù hợp không gian làm việc cao cấp.',
                'price' => 3990000,
                'stock' => 50,
                'image' => 'products/ghe-nova.jpg',
                'size' => 'Standard',
                'color' => 'Xám nhạt',
                'length' => 70,
                'width' => 70,
                'height' => 120,
                'color_code' => '#C0C4CA',
                'status' => 'active',
            ],
            [
                'name' => 'Tủ Gỗ Latis',
                'description' => 'Tủ gỗ tự nhiên 4 cánh với hệ thống đèn LED cảm ứng.',
                'price' => 18990000,
                'stock' => 8,
                'image' => 'products/tu-latis.jpg',
                'size' => '240cm',
                'color' => 'Nâu óc chó',
                'length' => 240,
                'width' => 55,
                'height' => 210,
                'color_code' => '#7B4A2D',
                'status' => 'active',
            ],
            [
                'name' => 'Đèn Thả Modula',
                'description' => 'Đèn thả trần phong cách đương đại, phù hợp phòng ăn.',
                'price' => 3290000,
                'stock' => 25,
                'image' => 'products/den-modula.jpg',
                'size' => '180cm',
                'color' => 'Đen mờ',
                'length' => 180,
                'width' => 20,
                'height' => 40,
                'color_code' => '#1F1F1F',
                'status' => 'inactive',
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['slug' => Str::slug($product['name'])],
                array_merge($product, [
                    'slug' => Str::slug($product['name']),
                    'category_id' => $categoryIds[array_rand($categoryIds)],
                    'brand_id' => $brandIds[array_rand($brandIds)],
                ])
            );
        }
    }
}
