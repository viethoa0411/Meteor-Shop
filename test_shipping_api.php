<?php
/**
 * Script test các API endpoints của Shipping Admin
 * Chạy: php test_shipping_api.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\ShippingSettingController;
use App\Models\ShippingDistance;

echo "=== TEST SHIPPING API ENDPOINTS ===\n\n";

$controller = new ShippingSettingController();

// Test 1: distancesData API
echo "1. Test distancesData API...\n";
try {
    $request = Request::create('/admin/shipping/distances/data', 'GET', [
        'draw' => 1,
        'start' => 0,
        'length' => 10,
        'order' => [
            ['column' => 0, 'dir' => 'asc']
        ]
    ]);
    
    $response = $controller->distancesData($request);
    $data = json_decode($response->getContent(), true);
    
    if (isset($data['data']) && is_array($data['data'])) {
        echo "   ✓ API trả về dữ liệu đúng format\n";
        echo "   - Total records: " . ($data['recordsTotal'] ?? 0) . "\n";
        echo "   - Filtered records: " . ($data['recordsFiltered'] ?? 0) . "\n";
        echo "   - Data count: " . count($data['data']) . "\n";
        
        if (count($data['data']) > 0) {
            $sample = $data['data'][0];
            echo "   - Sample: ID={$sample['id']}, Province={$sample['province_name']}, District={$sample['district_name']}, Distance={$sample['distance_km']} km\n";
        }
    } else {
        echo "   ✗ API trả về dữ liệu sai format\n";
        echo "   Response: " . json_encode($data, JSON_UNESCAPED_UNICODE) . "\n";
    }
} catch (\Exception $e) {
    echo "   ✗ Lỗi: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 2: distancesStore API (CREATE)
echo "2. Test distancesStore API (CREATE)...\n";
try {
    $request = Request::create('/admin/shipping/distances', 'POST', [
        'province_name' => 'Test Province API',
        'district_name' => 'Test District API',
        'distance_km' => 77.77,
    ]);
    
    $response = $controller->distancesStore($request);
    $data = json_decode($response->getContent(), true);
    
    if ($data['success'] ?? false) {
        echo "   ✓ Tạo mới thành công\n";
        echo "   - ID: " . ($data['data']['id'] ?? 'N/A') . "\n";
        $testId = $data['data']['id'] ?? null;
    } else {
        echo "   ✗ Tạo mới thất bại\n";
        echo "   - Message: " . ($data['message'] ?? 'N/A') . "\n";
        $testId = null;
    }
} catch (\Exception $e) {
    echo "   ✗ Lỗi: " . $e->getMessage() . "\n";
    $testId = null;
}
echo "\n";

// Test 3: distancesShow API (READ)
if ($testId) {
    echo "3. Test distancesShow API (READ)...\n";
    try {
        $response = $controller->distancesShow($testId);
        $data = json_decode($response->getContent(), true);
        
        if ($data['success'] ?? false) {
            echo "   ✓ Đọc dữ liệu thành công\n";
            echo "   - Province: " . ($data['data']['province_name'] ?? 'N/A') . "\n";
            echo "   - District: " . ($data['data']['district_name'] ?? 'N/A') . "\n";
            echo "   - Distance: " . ($data['data']['distance_km'] ?? 'N/A') . " km\n";
        } else {
            echo "   ✗ Đọc dữ liệu thất bại\n";
        }
    } catch (\Exception $e) {
        echo "   ✗ Lỗi: " . $e->getMessage() . "\n";
    }
    echo "\n";
    
    // Test 4: distancesUpdate API (UPDATE)
    echo "4. Test distancesUpdate API (UPDATE)...\n";
    try {
        $request = Request::create("/admin/shipping/distances/{$testId}", 'PUT', [
            'province_name' => 'Test Province API Updated',
            'district_name' => 'Test District API Updated',
            'distance_km' => 88.88,
        ]);
        
        $response = $controller->distancesUpdate($request, $testId);
        $data = json_decode($response->getContent(), true);
        
        if ($data['success'] ?? false) {
            echo "   ✓ Cập nhật thành công\n";
            echo "   - New distance: " . ($data['data']['distance_km'] ?? 'N/A') . " km\n";
        } else {
            echo "   ✗ Cập nhật thất bại\n";
            echo "   - Message: " . ($data['message'] ?? 'N/A') . "\n";
        }
    } catch (\Exception $e) {
        echo "   ✗ Lỗi: " . $e->getMessage() . "\n";
    }
    echo "\n";
    
    // Test 5: distancesDestroy API (DELETE)
    echo "5. Test distancesDestroy API (DELETE)...\n";
    try {
        $response = $controller->distancesDestroy($testId);
        $data = json_decode($response->getContent(), true);
        
        if ($data['success'] ?? false) {
            echo "   ✓ Xóa thành công\n";
            
            // Verify deletion
            $deleted = ShippingDistance::find($testId);
            if (!$deleted) {
                echo "   ✓ Xác nhận: Record đã bị xóa khỏi database\n";
            } else {
                echo "   ✗ Cảnh báo: Record vẫn còn trong database\n";
            }
        } else {
            echo "   ✗ Xóa thất bại\n";
            echo "   - Message: " . ($data['message'] ?? 'N/A') . "\n";
        }
    } catch (\Exception $e) {
        echo "   ✗ Lỗi: " . $e->getMessage() . "\n";
    }
    echo "\n";
} else {
    echo "3-5. Bỏ qua test READ/UPDATE/DELETE (không có test ID)\n\n";
}

// Test 6: Test duplicate validation
echo "6. Test duplicate validation...\n";
try {
    // Lấy một record hiện có
    $existing = ShippingDistance::first();
    
    if ($existing) {
        $request = Request::create('/admin/shipping/distances', 'POST', [
            'province_name' => $existing->province_name,
            'district_name' => $existing->district_name,
            'distance_km' => 99.99,
        ]);
        
        $response = $controller->distancesStore($request);
        $data = json_decode($response->getContent(), true);
        
        if (!($data['success'] ?? true)) {
            echo "   ✓ Validation hoạt động (từ chối duplicate)\n";
            echo "   - Message: " . ($data['message'] ?? 'N/A') . "\n";
        } else {
            echo "   ✗ Validation không hoạt động (cho phép duplicate)\n";
        }
    } else {
        echo "   - Không có dữ liệu để test\n";
    }
} catch (\Exception $e) {
    echo "   ✗ Lỗi: " . $e->getMessage() . "\n";
}
echo "\n";

echo "=== KẾT THÚC TEST API ===\n";

