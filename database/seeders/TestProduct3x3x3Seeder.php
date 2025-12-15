<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class TestProduct3x3x3Seeder extends Seeder
{
    /**
     * Tạo sản phẩm test với kích thước 3m x 3m x 3m và cân nặng 3kg
     */
    public function run(): void
    {
        $category = Category::first();
        
        if (!$category) {
            $category = Category::create([
                'name' => 'Test Category',
                'slug' => 'test-category',
                'parent_id' => null,
                'status' => 1,
            ]);
        }

        $product = Product::updateOrCreate(
            ['sku' => 'TEST-3M-3M-3M-3KG'],
            [
                'name' => 'Sản phẩm Test Kích Thước Lớn 3x3x3m',
                'slug' => 'san-pham-test-kich-thuoc-lon-3x3x3m',
                'description' => 'Sản phẩm test với kích thước 3m x 3m x 3m và cân nặng 3kg để kiểm tra logic tính phí vận chuyển',
                'short_description' => 'Test 3x3x3m - 3kg',
                'price' => 5000000,
                'sale_price' => 4500000,
                'stock' => 10,
                'length' => 300.00,   // 3m = 300cm
                'width' => 300.00,    // 3m = 300cm
                'height' => 300.00,   // 3m = 300cm
                'weight' => 3.00,     // 3kg
                'category_id' => $category->id,
                'status' => 'active',
            ]
        );

        $this->command->info("✅ Đã tạo sản phẩm test:");
        $this->command->info("   - Tên: {$product->name}");
        $this->command->info("   - SKU: {$product->sku}");
        $this->command->info("   - Kích thước: {$product->length}cm x {$product->width}cm x {$product->height}cm");
        $this->command->info("   - Tương đương: 3m x 3m x 3m");
        $this->command->info("   - Cân nặng: {$product->weight} kg");
        $this->command->info("   - Giá: " . number_format($product->price) . " đ");
    }
}

