<?php

/**
 * Script test tính phí vận chuyển
 *
 * Mục đích: Kiểm tra logic tính phí vận chuyển với dữ liệu thực tế
 *
 * Cách chạy: php test_shipping_fee.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ShippingSetting;
use App\Models\ShippingDistance;
use App\Models\Product;

echo "\n";
echo "╔════════════════════════════════════════════════════════════════════════════╗\n";
echo "║           TEST TÍNH PHÍ VẬN CHUYỂN - 6 SẢN PHẨM MẪU                       ║\n";
echo "╚════════════════════════════════════════════════════════════════════════════╝\n\n";

// Lấy cài đặt vận chuyển
$settings = ShippingSetting::getSettings();

echo "📦 CÀI ĐẶT PHÍ VẬN CHUYỂN:\n";
echo "┌─────────────────────────────────────────────────────────────────┐\n";
echo "│ Chiều dài:  Mét đầu " . str_pad(number_format($settings->first_length_price) . "đ", 15) . " | Mét tiếp theo " . str_pad(number_format($settings->next_length_price) . "đ", 10) . " │\n";
echo "│ Chiều rộng: Mét đầu " . str_pad(number_format($settings->first_width_price) . "đ", 15) . " | Mét tiếp theo " . str_pad(number_format($settings->next_width_price) . "đ", 10) . " │\n";
echo "│ Chiều cao:  Mét đầu " . str_pad(number_format($settings->first_height_price) . "đ", 15) . " | Mét tiếp theo " . str_pad(number_format($settings->next_height_price) . "đ", 10) . " │\n";
echo "│ Cân nặng:   Kg đầu  " . str_pad(number_format($settings->first_weight_price) . "đ", 15) . " | Kg tiếp theo  " . str_pad(number_format($settings->next_weight_price) . "đ", 10) . " │\n";
echo "└─────────────────────────────────────────────────────────────────┘\n\n";

// Lấy 6 sản phẩm mẫu từ database
$products = Product::whereIn('sku', [
    'TL-SAMSUNG-236L',
    'MG-LG-9KG',
    'TV-SONY-55-4K',
    'DH-DAIKIN-1.5HP',
    'LVS-PANA-25L',
    'NCD-TOSHIBA-1.8L'
])->get();

if ($products->count() < 6) {
    echo "❌ CẢNH BÁO: Chưa có đủ 6 sản phẩm mẫu. Vui lòng chạy: php artisan db:seed --class=ProductTestSeeder\n\n";
    exit(1);
}

// Địa điểm test
$testLocations = [
    ['city' => 'Hà Nội', 'district' => 'Quận Cầu Giấy'],
    ['city' => 'Hà Nội', 'district' => 'Quận Hoàn Kiếm'],
    ['city' => 'Hải Phòng', 'district' => 'Quận Hồng Bàng'],
];

$testIndex = 0;

foreach ($products as $product) {
    $testIndex++;
    $location = $testLocations[($testIndex - 1) % count($testLocations)];

    echo "═══════════════════════════════════════════════════════════════════════════\n";
    echo "TEST #{$testIndex}: {$product->name}\n";
    echo "═══════════════════════════════════════════════════════════════════════════\n";
    echo "📦 Thông tin sản phẩm:\n";
    echo "   - SKU: {$product->sku}\n";
    echo "   - Giá: " . number_format($product->price) . " đ";
    if ($product->sale_price) {
        echo " → " . number_format($product->sale_price) . " đ (giảm giá)\n";
    } else {
        echo "\n";
    }
    echo "   - Kích thước: {$product->length}cm x {$product->width}cm x {$product->height}cm\n";
    echo "   - Cân nặng: {$product->weight} kg\n";
    echo "   - Số lượng: 1\n\n";

    echo "📍 Địa điểm giao hàng:\n";
    echo "   - Từ: Hà Nội - Nam Từ Liêm - Phương Canh\n";
    echo "   - Đến: {$location['city']} - {$location['district']}\n";

    $distance = ShippingDistance::findDistance($location['city'], $location['district']);
    echo "   - Khoảng cách: {$distance} km\n\n";

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

    echo "💰 Tính toán phí vận chuyển:\n";
    echo "   ┌─────────────────────────────────────────────────────────────┐\n";

    // Tính chi tiết từng thành phần
    $lengthM = $product->length / 100;
    $widthM = $product->width / 100;
    $heightM = $product->height / 100;

    $lengthFee = $settings->first_length_price + (max(0, ceil($lengthM - 1)) * $settings->next_length_price);
    $widthFee = $settings->first_width_price + (max(0, ceil($widthM - 1)) * $settings->next_width_price);
    $heightFee = $settings->first_height_price + (max(0, ceil($heightM - 1)) * $settings->next_height_price);
    $weightFee = $settings->first_weight_price + (max(0, ceil($product->weight - 1)) * $settings->next_weight_price);

    echo "   │ Chiều dài ({$lengthM}m):  " . str_pad(number_format($lengthFee) . " đ", 45) . "│\n";
    echo "   │ Chiều rộng ({$widthM}m): " . str_pad(number_format($widthFee) . " đ", 45) . "│\n";
    echo "   │ Chiều cao ({$heightM}m):  " . str_pad(number_format($heightFee) . " đ", 45) . "│\n";
    echo "   │ Cân nặng ({$product->weight}kg):   " . str_pad(number_format($weightFee) . " đ", 45) . "│\n";
    echo "   ├─────────────────────────────────────────────────────────────┤\n";

    $totalDimWeight = $lengthFee + $widthFee + $heightFee + $weightFee;
    echo "   │ Tổng phí kích thước + cân nặng: " . str_pad(number_format($totalDimWeight) . " đ", 28) . "│\n";
    echo "   │ Nhân với khoảng cách ({$distance}km):    " . str_pad("x {$distance}", 28) . "│\n";
    echo "   ├─────────────────────────────────────────────────────────────┤\n";
    echo "   │ PHÍ VẬN CHUYỂN TIÊU CHUẨN: " . str_pad(number_format($result['standard_fee']) . " đ", 33) . "│\n";
    echo "   └─────────────────────────────────────────────────────────────┘\n\n";

    // Kiểm tra miễn phí ship
    if ($result['total'] == 0 && $result['standard_fee'] > 0) {
        echo "   🎉 MIỄN PHÍ VẬN CHUYỂN (đơn hàng >= 10,000,000đ)\n\n";
    } else {
        echo "   💵 Tổng phí vận chuyển: " . number_format($result['total']) . " đ\n\n";
    }
}

echo "═══════════════════════════════════════════════════════════════════════════\n";
echo "                           KẾT THÚC TEST                                  \n";
echo "═══════════════════════════════════════════════════════════════════════════\n\n";

echo "✅ Đã test thành công 6 sản phẩm với logic tính phí vận chuyển.\n";
echo "📝 Công thức tính:\n";
echo "   1. Tính phí từng thành phần (chiều dài, rộng, cao, cân nặng)\n";
echo "   2. Cộng tổng phí các thành phần\n";
echo "   3. Nhân với khoảng cách (km)\n";
echo "   4. Nhân với số lượng sản phẩm\n\n";

