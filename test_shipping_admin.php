<?php
/**
 * Script test các chức năng Shipping Admin
 * Chạy: php test_shipping_admin.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ShippingSetting;
use App\Models\ShippingDistance;
use Illuminate\Support\Facades\DB;

echo "=== TEST SHIPPING ADMIN FUNCTIONS ===\n\n";

// Test 1: Kiểm tra ShippingSetting tồn tại
echo "1. Kiểm tra ShippingSetting...\n";
$settings = ShippingSetting::first();
if ($settings) {
    echo "   ✓ ShippingSetting tồn tại\n";
    echo "   - Origin: {$settings->origin_city}, {$settings->origin_district}, {$settings->origin_ward}\n";
    echo "   - Free shipping enabled: " . ($settings->free_shipping_enabled ? 'BẬT' : 'TẮT') . "\n";
    echo "   - Free shipping threshold: " . number_format($settings->free_shipping_threshold) . " đ\n";
} else {
    echo "   ✗ Không tìm thấy ShippingSetting\n";
}
echo "\n";

// Test 2: Test update free_shipping_enabled
echo "2. Test toggle free_shipping_enabled...\n";
if ($settings) {
    $oldValue = $settings->free_shipping_enabled;
    echo "   - Giá trị cũ: " . ($oldValue ? 'BẬT' : 'TẮT') . "\n";
    
    // Toggle
    $settings->free_shipping_enabled = !$oldValue;
    $settings->save();
    
    // Reload
    $settings->refresh();
    $newValue = $settings->free_shipping_enabled;
    echo "   - Giá trị mới: " . ($newValue ? 'BẬT' : 'TẮT') . "\n";
    
    if ($newValue !== $oldValue) {
        echo "   ✓ Toggle thành công\n";
        
        // Đổi lại về giá trị cũ
        $settings->free_shipping_enabled = $oldValue;
        $settings->save();
        echo "   ✓ Đã khôi phục giá trị cũ\n";
    } else {
        echo "   ✗ Toggle thất bại\n";
    }
}
echo "\n";

// Test 3: Test ShippingDistance CRUD
echo "3. Test ShippingDistance CRUD...\n";

// 3a. Test CREATE
echo "   a) Test CREATE...\n";
$testDistance = ShippingDistance::create([
    'province_name' => 'Test Province',
    'district_name' => 'Test District',
    'distance_km' => 99.99,
]);
if ($testDistance && $testDistance->id) {
    echo "      ✓ Tạo mới thành công (ID: {$testDistance->id})\n";
} else {
    echo "      ✗ Tạo mới thất bại\n";
}

// 3b. Test READ
echo "   b) Test READ...\n";
$found = ShippingDistance::find($testDistance->id);
if ($found && $found->province_name === 'Test Province') {
    echo "      ✓ Đọc dữ liệu thành công\n";
} else {
    echo "      ✗ Đọc dữ liệu thất bại\n";
}

// 3c. Test UPDATE
echo "   c) Test UPDATE...\n";
$testDistance->distance_km = 88.88;
$testDistance->save();
$testDistance->refresh();
if ($testDistance->distance_km == 88.88) {
    echo "      ✓ Cập nhật thành công\n";
} else {
    echo "      ✗ Cập nhật thất bại\n";
}

// 3d. Test DELETE
echo "   d) Test DELETE...\n";
$id = $testDistance->id;
$testDistance->delete();
$deleted = ShippingDistance::find($id);
if (!$deleted) {
    echo "      ✓ Xóa thành công\n";
} else {
    echo "      ✗ Xóa thất bại\n";
}
echo "\n";

// Test 4: Test distancesData API format
echo "4. Test distancesData API format...\n";
$distances = ShippingDistance::query()->paginate(10);
echo "   - Total records: " . $distances->total() . "\n";
echo "   - Items count: " . count($distances->items()) . "\n";

if ($distances->items()) {
    $data = collect($distances->items())->map(function($distance) {
        return [
            'id' => $distance->id,
            'province_name' => $distance->province_name,
            'district_name' => $distance->district_name,
            'distance_km' => number_format($distance->distance_km, 2),
        ];
    })->toArray();
    
    echo "   ✓ Map dữ liệu thành công\n";
    if (count($data) > 0) {
        echo "   - Sample data: " . json_encode($data[0], JSON_UNESCAPED_UNICODE) . "\n";
    }
} else {
    echo "   - Không có dữ liệu để test\n";
}
echo "\n";

// Test 5: Test validation
echo "5. Test validation...\n";
try {
    $invalid = ShippingDistance::create([
        'province_name' => '',
        'district_name' => '',
        'distance_km' => -1,
    ]);
    echo "   ✗ Validation không hoạt động (cho phép dữ liệu không hợp lệ)\n";
    $invalid->delete();
} catch (\Exception $e) {
    echo "   ✓ Validation hoạt động (từ chối dữ liệu không hợp lệ)\n";
}
echo "\n";

echo "=== KẾT THÚC TEST ===\n";

