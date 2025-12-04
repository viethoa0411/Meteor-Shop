<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Seed 30 sample products.
     */
    public function run(): void
    {
        // Lấy danh mục sẵn có để gán cho sản phẩm
        $categories = Category::where('status', 1)->pluck('id')->all();

        if (empty($categories)) {
            // Nếu chưa có category, tạo tạm 3 cái
            $defaultCategories = [
                'Phòng khách',
                'Phòng ngủ',
                'Phòng ăn',
            ];

            foreach ($defaultCategories as $name) {
                $categories[] = Category::create([
                    'name' => $name,
                    'slug' => Str::slug($name) . '-' . Str::random(4),
                    'status' => 1,
                ])->id;
            }
        }

        // Nếu sau khi tạo vẫn không có category thì dừng
        if (empty($categories)) {
            return;
        }

        for ($i = 1; $i <= 30; $i++) {
            $name = 'Sản phẩm demo ' . $i;

            Product::create([
                'name' => $name,
                'slug' => Str::slug($name) . '-' . Str::random(6),
                'sku' => 'DEMO-' . str_pad((string) $i, 4, '0', STR_PAD_LEFT),
                'description' => 'Mô tả ngắn cho ' . $name . '. Đây là dữ liệu demo phục vụ phát triển giao diện.',
                'short_description' => 'Sản phẩm demo ' . $i,
                'price' => rand(1_000_000, 20_000_000),
                'sale_price' => null,
                'stock' => rand(0, 50),
                'image' => null,
                'length' => null,
                'width' => null,
                'height' => null,
                'color_code' => null,
                'category_id' => $categories[array_rand($categories)],
                'brand_id' => null,
                'status' => 1,
                'rating_avg' => 0,
                'total_sold' => 0,
            ]);
        }
    }
}


