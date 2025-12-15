<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;

class ProductTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Tạo 6 sản phẩm mẫu với đầy đủ thông tin kích thước và cân nặng
     * để test tính phí vận chuyển
     */
    public function run(): void
    {
        // Lấy hoặc tạo category mẫu
        $category = Category::firstOrCreate(
            ['slug' => 'dien-tu'],
            [
                'name' => 'Điện tử',
                'parent_id' => null,
                'status' => 1,
            ]
        );

        $products = [
            [
                'name' => 'Tủ lạnh Samsung Inverter 236L',
                'slug' => 'tu-lanh-samsung-inverter-236l',
                'sku' => 'TL-SAMSUNG-236L',
                'description' => 'Tủ lạnh Samsung Inverter 236 lít, tiết kiệm điện, công nghệ làm lạnh đa chiều',
                'short_description' => 'Tủ lạnh Samsung 236L tiết kiệm điện',
                'price' => 6500000,
                'sale_price' => 5990000,
                'stock' => 50,
                'length' => 144.0,  // cm
                'width' => 55.5,    // cm
                'height' => 63.0,   // cm
                'weight' => 45.5,   // kg
                'category_id' => $category->id,
                'status' => 'active',
            ],
            [
                'name' => 'Máy giặt LG Inverter 9kg',
                'slug' => 'may-giat-lg-inverter-9kg',
                'sku' => 'MG-LG-9KG',
                'description' => 'Máy giặt LG Inverter 9kg, công nghệ AI DD, giặt sạch sâu',
                'short_description' => 'Máy giặt LG 9kg công nghệ AI',
                'price' => 8900000,
                'sale_price' => 7990000,
                'stock' => 30,
                'length' => 85.0,   // cm
                'width' => 60.0,    // cm
                'height' => 105.0,  // cm
                'weight' => 62.0,   // kg
                'category_id' => $category->id,
                'status' => 'active',
            ],
            [
                'name' => 'Tivi Sony 55 inch 4K',
                'slug' => 'tivi-sony-55-inch-4k',
                'sku' => 'TV-SONY-55-4K',
                'description' => 'Smart Tivi Sony 55 inch 4K Ultra HD, HDR, Android TV',
                'short_description' => 'Tivi Sony 55 inch 4K HDR',
                'price' => 15900000,
                'sale_price' => 13990000,
                'stock' => 20,
                'length' => 123.0,  // cm
                'width' => 7.0,     // cm
                'height' => 71.0,   // cm
                'weight' => 18.5,   // kg
                'category_id' => $category->id,
                'status' => 'active',
            ],
            [
                'name' => 'Điều hòa Daikin Inverter 1.5HP',
                'slug' => 'dieu-hoa-daikin-inverter-1-5hp',
                'sku' => 'DH-DAIKIN-1.5HP',
                'description' => 'Điều hòa Daikin Inverter 1.5HP, tiết kiệm điện, làm lạnh nhanh',
                'short_description' => 'Điều hòa Daikin 1.5HP Inverter',
                'price' => 11500000,
                'sale_price' => 9990000,
                'stock' => 40,
                'length' => 80.0,   // cm
                'width' => 28.5,    // cm
                'height' => 29.0,   // cm
                'weight' => 12.0,   // kg
                'category_id' => $category->id,
                'status' => 'active',
            ],
            [
                'name' => 'Lò vi sóng Panasonic 25L',
                'slug' => 'lo-vi-song-panasonic-25l',
                'sku' => 'LVS-PANA-25L',
                'description' => 'Lò vi sóng Panasonic 25 lít, công suất 800W, nhiều chế độ nấu',
                'short_description' => 'Lò vi sóng Panasonic 25L',
                'price' => 2500000,
                'sale_price' => 1990000,
                'stock' => 60,
                'length' => 48.3,   // cm
                'width' => 39.6,    // cm
                'height' => 28.0,   // cm
                'weight' => 11.5,   // kg
                'category_id' => $category->id,
                'status' => 'active',
            ],
            [
                'name' => 'Nồi cơm điện Toshiba 1.8L',
                'slug' => 'noi-com-dien-toshiba-1-8l',
                'sku' => 'NCD-TOSHIBA-1.8L',
                'description' => 'Nồi cơm điện Toshiba 1.8 lít, lòng nồi chống dính, nấu cơm ngon',
                'short_description' => 'Nồi cơm điện Toshiba 1.8L',
                'price' => 1200000,
                'sale_price' => 990000,
                'stock' => 100,
                'length' => 35.0,   // cm
                'width' => 30.0,    // cm
                'height' => 25.0,   // cm
                'weight' => 3.5,    // kg
                'category_id' => $category->id,
                'status' => 'active',
            ],
        ];

        foreach ($products as $productData) {
            Product::updateOrCreate(
                ['sku' => $productData['sku']],
                $productData
            );
        }

        $this->command->info("✅ Đã tạo 6 sản phẩm mẫu với đầy đủ thông tin kích thước và cân nặng.");
    }
}

