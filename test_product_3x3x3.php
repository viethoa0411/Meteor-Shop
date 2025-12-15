<?php

/**
 * Script test tính phí vận chuyển cho sản phẩm 3m x 3m x 3m - 3kg
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ShippingSetting;
use App\Models\ShippingDistance;
use App\Models\Product;

echo "\n";
echo "╔════════════════════════════════════════════════════════════════════════════╗\n";
echo "║         TEST TÍNH PHÍ VẬN CHUYỂN - SẢN PHẨM 3M x 3M x 3M - 3KG           ║\n";
echo "╚════════════════════════════════════════════════════════════════════════════╝\n\n";

// Lấy sản phẩm test
$product = Product::where('sku', 'TEST-3M-3M-3M-3KG')->first();

if (!$product) {
    echo "❌ CẢNH BÁO: Chưa có sản phẩm test. Vui lòng chạy: php artisan db:seed --class=TestProduct3x3x3Seeder\n\n";
    exit(1);
}

// Lấy cài đặt vận chuyển
$settings = ShippingSetting::getSettings();

echo "📦 THÔNG TIN SẢN PHẨM:\n";
echo "┌─────────────────────────────────────────────────────────────────┐\n";
echo "│ Tên: " . str_pad($product->name, 60) . "│\n";
echo "│ SKU: " . str_pad($product->sku, 60) . "│\n";
echo "│ Kích thước: " . str_pad("{$product->length}cm x {$product->width}cm x {$product->height}cm", 51) . "│\n";
echo "│ Tương đương: " . str_pad("3m x 3m x 3m", 51) . "│\n";
echo "│ Cân nặng: " . str_pad("{$product->weight} kg", 54) . "│\n";
echo "│ Giá: " . str_pad(number_format($product->price) . " đ", 59) . "│\n";
echo "└─────────────────────────────────────────────────────────────────┘\n\n";

echo "💰 CÀI ĐẶT PHÍ VẬN CHUYỂN:\n";
echo "┌─────────────────────────────────────────────────────────────────┐\n";
echo "│ Chiều dài:  Mét đầu " . str_pad(number_format($settings->first_length_price) . "đ", 15) . " | Mét tiếp theo " . str_pad(number_format($settings->next_length_price) . "đ", 10) . " │\n";
echo "│ Chiều rộng: Mét đầu " . str_pad(number_format($settings->first_width_price) . "đ", 15) . " | Mét tiếp theo " . str_pad(number_format($settings->next_width_price) . "đ", 10) . " │\n";
echo "│ Chiều cao:  Mét đầu " . str_pad(number_format($settings->first_height_price) . "đ", 15) . " | Mét tiếp theo " . str_pad(number_format($settings->next_height_price) . "đ", 10) . " │\n";
echo "│ Cân nặng:   Kg đầu  " . str_pad(number_format($settings->first_weight_price) . "đ", 15) . " | Kg tiếp theo  " . str_pad(number_format($settings->next_weight_price) . "đ", 10) . " │\n";
echo "└─────────────────────────────────────────────────────────────────┘\n\n";

// Test với 3 địa điểm khác nhau
$testLocations = [
    ['city' => 'Hà Nội', 'district' => 'Quận Cầu Giấy', 'label' => 'Gần (5km)'],
    ['city' => 'Hà Nội', 'district' => 'Quận Hoàn Kiếm', 'label' => 'Trung bình (10km)'],
    ['city' => 'Hải Phòng', 'district' => 'Quận Hồng Bàng', 'label' => 'Xa (105km)'],
];

foreach ($testLocations as $index => $location) {
    echo "═══════════════════════════════════════════════════════════════════════════\n";
    echo "TEST #" . ($index + 1) . ": GIAO HÀNG ĐẾN {$location['city']} - {$location['district']} ({$location['label']})\n";
    echo "═══════════════════════════════════════════════════════════════════════════\n\n";
    
    $distance = ShippingDistance::findDistance($location['city'], $location['district']);
    echo "📍 Khoảng cách: {$distance} km\n\n";
    
    // Tính phí vận chuyển
    $items = [
        [
            'length_cm' => (float)$product->length,
            'width_cm' => (float)$product->width,
            'height_cm' => (float)$product->height,
            'weight_kg' => (float)$product->weight,
            'quantity' => 1,
        ]
    ];
    
    $result = $settings->calculateShippingFee(
        $items, 
        'standard', 
        $product->sale_price ?? $product->price, 
        $location['city'], 
        $location['district']
    );
    
    echo "🧮 TÍNH TOÁN CHI TIẾT:\n";
    echo "┌─────────────────────────────────────────────────────────────────┐\n";
    
    // Tính chi tiết từng thành phần
    $lengthM = $product->length / 100;  // 3m
    $widthM = $product->width / 100;    // 3m
    $heightM = $product->height / 100;  // 3m
    
    // Phí chiều dài: Mét đầu + (Số mét - 1) × Mét tiếp theo
    $lengthFee = $settings->first_length_price + (max(0, ceil($lengthM - 1)) * $settings->next_length_price);
    echo "│ 1. Phí chiều dài ({$lengthM}m):                                      │\n";
    echo "│    = Mét đầu + (Số mét - 1) × Mét tiếp theo                    │\n";
    echo "│    = " . number_format($settings->first_length_price) . " + (" . ceil($lengthM - 1) . " × " . number_format($settings->next_length_price) . ")                              │\n";
    echo "│    = " . number_format($settings->first_length_price) . " + " . number_format(ceil($lengthM - 1) * $settings->next_length_price) . "                                       │\n";
    echo "│    = " . str_pad(number_format($lengthFee) . " đ", 56) . "│\n";
    echo "│                                                                 │\n";
    
    // Phí chiều rộng
    $widthFee = $settings->first_width_price + (max(0, ceil($widthM - 1)) * $settings->next_width_price);
    echo "│ 2. Phí chiều rộng ({$widthM}m):                                     │\n";
    echo "│    = Mét đầu + (Số mét - 1) × Mét tiếp theo                    │\n";
    echo "│    = " . number_format($settings->first_width_price) . " + (" . ceil($widthM - 1) . " × " . number_format($settings->next_width_price) . ")                               │\n";
    echo "│    = " . number_format($settings->first_width_price) . " + " . number_format(ceil($widthM - 1) * $settings->next_width_price) . "                                        │\n";
    echo "│    = " . str_pad(number_format($widthFee) . " đ", 56) . "│\n";
    echo "│                                                                 │\n";
    
    // Phí chiều cao
    $heightFee = $settings->first_height_price + (max(0, ceil($heightM - 1)) * $settings->next_height_price);
    echo "│ 3. Phí chiều cao ({$heightM}m):                                     │\n";
    echo "│    = Mét đầu + (Số mét - 1) × Mét tiếp theo                    │\n";
    echo "│    = " . number_format($settings->first_height_price) . " + (" . ceil($heightM - 1) . " × " . number_format($settings->next_height_price) . ")                               │\n";
    echo "│    = " . number_format($settings->first_height_price) . " + " . number_format(ceil($heightM - 1) * $settings->next_height_price) . "                                        │\n";
    echo "│    = " . str_pad(number_format($heightFee) . " đ", 56) . "│\n";
    echo "│                                                                 │\n";
    
    // Phí cân nặng
    $weightFee = $settings->first_weight_price + (max(0, ceil($product->weight - 1)) * $settings->next_weight_price);
    echo "│ 4. Phí cân nặng ({$product->weight}kg):                                    │\n";
    echo "│    = Kg đầu + (Số kg - 1) × Kg tiếp theo                       │\n";
    echo "│    = " . number_format($settings->first_weight_price) . " + (" . ceil($product->weight - 1) . " × " . number_format($settings->next_weight_price) . ")                                │\n";
    echo "│    = " . number_format($settings->first_weight_price) . " + " . number_format(ceil($product->weight - 1) * $settings->next_weight_price) . "                                         │\n";
    echo "│    = " . str_pad(number_format($weightFee) . " đ", 56) . "│\n";
    echo "│                                                                 │\n";
    
    echo "├─────────────────────────────────────────────────────────────────┤\n";
    
    $totalDimWeight = $lengthFee + $widthFee + $heightFee + $weightFee;
    echo "│ 5. TỔNG 4 THÀNH PHẦN:                                           │\n";
    echo "│    = " . number_format($lengthFee) . " + " . number_format($widthFee) . " + " . number_format($heightFee) . " + " . number_format($weightFee) . "                    │\n";
    echo "│    = " . str_pad(number_format($totalDimWeight) . " đ", 56) . "│\n";
    echo "│                                                                 │\n";
    
    echo "├─────────────────────────────────────────────────────────────────┤\n";
    
    echo "│ 6. NHÂN VỚI KHOẢNG CÁCH:                                        │\n";
    echo "│    = " . number_format($totalDimWeight) . " × {$distance} km                                      │\n";
    echo "│    = " . str_pad(number_format($result['standard_fee']) . " đ", 56) . "│\n";
    echo "│                                                                 │\n";
    
    echo "└─────────────────────────────────────────────────────────────────┘\n\n";
    
    if ($result['total'] == 0 && $result['standard_fee'] > 0) {
        echo "   🎉 MIỄN PHÍ VẬN CHUYỂN (đơn hàng >= 10,000,000đ)\n\n";
    } else {
        echo "   💵 TỔNG PHÍ VẬN CHUYỂN: " . number_format($result['total']) . " đ\n\n";
    }
}

echo "═══════════════════════════════════════════════════════════════════════════\n";
echo "                           KẾT THÚC TEST                                  \n";
echo "═══════════════════════════════════════════════════════════════════════════\n\n";

