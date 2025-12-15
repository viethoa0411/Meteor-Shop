# HƯỚNG DẪN SỬ DỤNG BẢNG KHOẢNG CÁCH VẬN CHUYỂN

## 📋 TỔNG QUAN

Bảng `shipping_distances` được tạo để lưu trữ khoảng cách (km) từ kho hàng đến các quận/huyện/thị xã trong khu vực miền Bắc. Bảng này giúp tính phí vận chuyển chính xác hơn dựa trên khoảng cách thực tế.

---

## 🗄️ CẤU TRÚC BẢNG

### Tên bảng: `shipping_distances`

| Trường | Kiểu dữ liệu | Mô tả |
|--------|--------------|-------|
| `id` | BIGINT (Primary Key) | ID tự động tăng |
| `province_name` | VARCHAR(255) | Tên Tỉnh/Thành Phố |
| `district_name` | VARCHAR(255) | Tên Quận/Huyện/Thị Xã |
| `distance_km` | DECIMAL(8,2) | Số Km từ kho hàng đến địa chỉ này |
| `created_at` | TIMESTAMP | Thời gian tạo |
| `updated_at` | TIMESTAMP | Thời gian cập nhật |

### Indexes:
- `province_name` và `district_name` (composite index)
- `province_name` (single index)
- `district_name` (single index)

---

## 📝 CÁCH SỬ DỤNG

### 1. Thêm dữ liệu mẫu (Seeder)

Chạy seeder để thêm dữ liệu mẫu:

```bash
php artisan db:seed --class=ShippingDistanceSeeder
```

Hoặc chạy tất cả seeders:

```bash
php artisan db:seed
```

### 2. Thêm dữ liệu thủ công

#### Sử dụng Model:

```php
use App\Models\ShippingDistance;

// Tạo mới hoặc cập nhật nếu đã tồn tại
ShippingDistance::createOrUpdate(
    'Hà Nội',           // Tên tỉnh/thành phố
    'Quận Cầu Giấy',    // Tên quận/huyện
    7.5                 // Khoảng cách (km)
);

// Hoặc tạo mới trực tiếp
ShippingDistance::create([
    'province_name' => 'Hà Nội',
    'district_name' => 'Quận Ba Đình',
    'distance_km' => 5.0,
]);
```

#### Sử dụng Database Query:

```php
DB::table('shipping_distances')->insert([
    'province_name' => 'Hà Nội',
    'district_name' => 'Quận Hoàn Kiếm',
    'distance_km' => 3.0,
    'created_at' => now(),
    'updated_at' => now(),
]);
```

### 3. Truy vấn dữ liệu

#### Tìm khoảng cách theo tỉnh và quận/huyện:

```php
use App\Models\ShippingDistance;

$distance = ShippingDistance::findDistance('Hà Nội', 'Quận Cầu Giấy');
// Trả về: 7.5 (float) hoặc null nếu không tìm thấy
```

#### Tìm khoảng cách trung bình theo tỉnh:

```php
$avgDistance = ShippingDistance::findDistanceByProvince('Hà Nội');
// Trả về khoảng cách trung bình của tất cả quận/huyện trong Hà Nội
```

#### Truy vấn trực tiếp:

```php
// Tìm tất cả quận/huyện của một tỉnh
$districts = ShippingDistance::where('province_name', 'Hà Nội')->get();

// Tìm khoảng cách cụ thể
$distance = ShippingDistance::where('province_name', 'Hà Nội')
    ->where('district_name', 'Quận Cầu Giấy')
    ->first();
```

### 4. Cập nhật dữ liệu

```php
// Cập nhật khoảng cách
$distance = ShippingDistance::where('province_name', 'Hà Nội')
    ->where('district_name', 'Quận Cầu Giấy')
    ->first();

if ($distance) {
    $distance->update(['distance_km' => 8.0]);
}

// Hoặc sử dụng createOrUpdate
ShippingDistance::createOrUpdate('Hà Nội', 'Quận Cầu Giấy', 8.0);
```

### 5. Xóa dữ liệu

```php
// Xóa một bản ghi
ShippingDistance::where('province_name', 'Hà Nội')
    ->where('district_name', 'Quận Cầu Giấy')
    ->delete();

// Xóa tất cả dữ liệu
ShippingDistance::truncate();
```

---

## 🔧 TÍCH HỢP VÀO HỆ THỐNG

### Cập nhật logic tính khoảng cách

Bạn có thể cập nhật method `calculateDistance()` trong `ShippingSetting` model để ưu tiên sử dụng bảng này:

```php
// app/Models/ShippingSetting.php

private function calculateDistance(?string $destinationCity, ?string $destinationDistrict, ?string $destinationWard = null, ?string $destinationAddress = null): float
{
    // ... existing code ...
    
    // Ưu tiên tìm trong bảng shipping_distances
    if ($destinationCity && $destinationDistrict) {
        $distance = ShippingDistance::findDistance($destinationCity, $destinationDistrict);
        if ($distance !== null) {
            Log::info('Shipping: Sử dụng khoảng cách từ database', [
                'province' => $destinationCity,
                'district' => $destinationDistrict,
                'distance_km' => $distance,
            ]);
            return (float) $distance;
        }
    }
    
    // Fallback về Google Maps API hoặc ước tính
    // ... existing code ...
}
```

---

## 📊 QUẢN LÝ DỮ LIỆU

### Import dữ liệu từ Excel/CSV

Bạn có thể tạo một command để import dữ liệu:

```bash
php artisan make:command ImportShippingDistances
```

### Export dữ liệu

```php
use App\Models\ShippingDistance;
use Illuminate\Support\Facades\Storage;

$distances = ShippingDistance::all();
$csv = "Tỉnh/Thành phố,Quận/Huyện,Km\n";
foreach ($distances as $distance) {
    $csv .= "{$distance->province_name},{$distance->district_name},{$distance->distance_km}\n";
}
Storage::put('shipping_distances.csv', $csv);
```

---

## ⚠️ LƯU Ý

1. **Tên tỉnh/thành phố và quận/huyện**: Phải khớp chính xác với tên được sử dụng trong hệ thống (có thể có dấu hoặc không dấu tùy theo cách lưu trữ).

2. **Khoảng cách**: Lưu dưới dạng số thập phân (DECIMAL) với 2 chữ số sau dấu phẩy.

3. **Index**: Bảng đã có index để tối ưu truy vấn, đảm bảo hiệu suất tốt khi có nhiều dữ liệu.

4. **Duplicate**: Method `createOrUpdate()` sẽ tự động cập nhật nếu đã tồn tại bản ghi với cùng `province_name` và `district_name`.

---

## 🎯 VÍ DỤ SỬ DỤNG

### Thêm khoảng cách cho một quận mới:

```php
ShippingDistance::createOrUpdate('Hà Nội', 'Quận Nam Từ Liêm', 8.5);
```

### Lấy khoảng cách để tính phí:

```php
$province = 'Hà Nội';
$district = 'Quận Cầu Giấy';

$distance = ShippingDistance::findDistance($province, $district);

if ($distance) {
    $shippingFee = $distance * $feePerKm;
    echo "Phí vận chuyển: " . number_format($shippingFee) . " đ";
} else {
    echo "Không tìm thấy khoảng cách, sử dụng giá trị mặc định";
}
```

---

## 📚 TÀI LIỆU LIÊN QUAN

- Model: `app/Models/ShippingDistance.php`
- Migration: `database/migrations/2025_12_13_004801_create_shipping_distances_table.php`
- Seeder: `database/seeders/ShippingDistanceSeeder.php`

---

**Ngày tạo**: 2025-12-13
**Phiên bản**: 1.0


