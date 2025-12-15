# HƯỚNG DẪN TÍCH HỢP GOOGLE MAPS API ĐỂ TÍNH KHOẢNG CÁCH

## 📋 TỔNG QUAN

Hệ thống đã được tích hợp Google Maps Distance Matrix API để tính khoảng cách thực tế giữa địa chỉ kho hàng và địa chỉ giao hàng. Hệ thống sẽ tự động fallback về phương pháp ước tính nếu Google Maps API không khả dụng.

---

## 🔑 BƯỚC 1: LẤY GOOGLE MAPS API KEY

### 1.1. Truy cập Google Cloud Console
1. Vào: https://console.cloud.google.com/
2. Đăng nhập bằng tài khoản Google
3. Tạo project mới hoặc chọn project hiện có

### 1.2. Bật Google Maps Distance Matrix API
1. Vào **APIs & Services** > **Library**
2. Tìm "Distance Matrix API"
3. Click **Enable**

### 1.3. Tạo API Key
1. Vào **APIs & Services** > **Credentials**
2. Click **Create Credentials** > **API Key**
3. Copy API Key (sẽ có dạng: `AIzaSy...`)
4. (Tùy chọn) Giới hạn API Key:
   - Click vào API Key vừa tạo
   - **Application restrictions**: Chọn "HTTP referrers"
   - **API restrictions**: Chọn "Restrict key" và chỉ chọn "Distance Matrix API"
   - Click **Save**

### 1.4. Bật Billing (Nếu cần)
- Google Maps API có free tier: **$200 credit/tháng** (đủ cho ~40,000 requests)
- Nếu vượt quá, cần bật billing
- Xem chi tiết: https://developers.google.com/maps/billing-and-pricing/pricing

---

## ⚙️ BƯỚC 2: CẤU HÌNH TRONG LARAVEL

### 2.1. Thêm vào file `.env`

Mở file `.env` và thêm:

```env
# Google Maps API Configuration
GOOGLE_MAPS_API_KEY=AIzaSyYour_API_Key_Here
GOOGLE_MAPS_ENABLED=true
```

**Lưu ý**: 
- Thay `AIzaSyYour_API_Key_Here` bằng API Key thực tế của bạn
- `GOOGLE_MAPS_ENABLED=true` để bật tính năng
- `GOOGLE_MAPS_ENABLED=false` để tắt (sẽ dùng phương pháp ước tính)

### 2.2. Kiểm tra file `config/services.php`

File đã được cấu hình sẵn:
```php
'google_maps' => [
    'api_key' => env('GOOGLE_MAPS_API_KEY'),
    'enabled' => env('GOOGLE_MAPS_ENABLED', false),
],
```

---

## 🔧 BƯỚC 3: KIỂM TRA HOẠT ĐỘNG

### 3.1. Kiểm tra Log

Sau khi cấu hình, kiểm tra log để xem API có hoạt động:

```bash
tail -f storage/logs/laravel.log
```

Tìm các dòng log:
- `Google Maps: Tính khoảng cách thành công` - API hoạt động
- `Google Maps API error` - Có lỗi với API
- `Shipping: Sử dụng Google Maps API` - Đang dùng Google Maps
- `Shipping: ... (ước tính)` - Đang dùng phương pháp ước tính

### 3.2. Test trên trang Checkout

1. Vào trang checkout: `/checkout?product_id=25&qty=1&type=buy_now`
2. Chọn địa chỉ giao hàng
3. Kiểm tra Console (F12) xem có log không
4. Kiểm tra phí vận chuyển có thay đổi theo địa chỉ không

---

## 📊 CÁCH HOẠT ĐỘNG

### Luồng xử lý:

```
1. User chọn địa chỉ giao hàng
   ↓
2. JavaScript gửi AJAX request với:
   - city, district, ward, address
   ↓
3. Controller nhận request
   ↓
4. ShippingSetting::calculateShippingFee()
   ↓
5. Kiểm tra GOOGLE_MAPS_ENABLED:
   ├─ TRUE → Gọi GoogleMapsService::calculateDistance()
   │   ├─ Thành công → Trả về khoảng cách thực tế (km)
   │   └─ Lỗi → Fallback về estimateDistance()
   └─ FALSE → Dùng estimateDistance() (ước tính)
   ↓
6. Tính phí vận chuyển dựa trên khoảng cách
   ↓
7. Trả về kết quả cho client
```

### Cache Mechanism:

- Kết quả từ Google Maps API được cache **24 giờ**
- Cache key: `google_maps_distance_{md5(origin+destination+mode)}`
- Giúp giảm số lần gọi API và tăng tốc độ

---

## 🛠️ SERVICE CLASS: GoogleMapsService

### Các phương thức:

#### 1. `calculateDistance()`
Tính khoảng cách và thời gian giữa 2 địa chỉ.

**Tham số**:
- `$origin`: Địa chỉ gốc (ví dụ: "123 Đường ABC, Phường X, Quận Y, Hà Nội")
- `$destination`: Địa chỉ đích
- `$mode`: Phương thức di chuyển (`driving`, `walking`, `bicycling`, `transit`)

**Trả về**:
```php
[
    'distance_km' => 15.5,
    'duration_minutes' => 25.3,
    'distance_text' => '15.5 km',
    'duration_text' => '25 phút',
    'status' => 'OK'
]
```

#### 2. `geocode()`
Chuyển đổi địa chỉ thành tọa độ (lat, lng).

**Trả về**:
```php
[
    'lat' => 21.0285,
    'lng' => 105.8542
]
```

---

## 💡 VÍ DỤ SỬ DỤNG

### Trong Code:

```php
use App\Services\GoogleMapsService;

$googleMaps = new GoogleMapsService();

// Tính khoảng cách
$result = $googleMaps->calculateDistance(
    '123 Đường ABC, Phường Dịch Vọng, Quận Cầu Giấy, Hà Nội',
    '456 Đường XYZ, Phường Mai Dịch, Quận Cầu Giấy, Hà Nội'
);

if ($result) {
    echo "Khoảng cách: " . $result['distance_km'] . " km";
    echo "Thời gian: " . $result['duration_minutes'] . " phút";
}
```

---

## ⚠️ XỬ LÝ LỖI VÀ FALLBACK

### Các trường hợp fallback:

1. **API Key chưa được cấu hình**
   - Log: `Google Maps API key chưa được cấu hình`
   - Fallback: Dùng phương pháp ước tính

2. **API trả về lỗi**
   - Log: `Google Maps API returned error status`
   - Fallback: Dùng phương pháp ước tính

3. **Timeout hoặc Network error**
   - Log: `Google Maps API exception`
   - Fallback: Dùng phương pháp ước tính

4. **GOOGLE_MAPS_ENABLED=false**
   - Không gọi API
   - Dùng phương pháp ước tính

### Phương pháp ước tính (Fallback):

- Cùng quận/huyện: **10km**
- Cùng tỉnh, khác quận: **30km**
- Khác tỉnh: **100km**
- Mặc định: **10km**

---

## 📈 TỐI ƯU HÓA

### 1. Cache
- Kết quả được cache 24 giờ
- Giảm số lần gọi API
- Tăng tốc độ response

### 2. Timeout
- Timeout 10 giây cho mỗi request
- Tránh blocking quá lâu

### 3. Error Handling
- Xử lý lỗi toàn diện
- Fallback tự động
- Logging chi tiết

---

## 🔍 DEBUG VÀ TROUBLESHOOTING

### Kiểm tra API Key:

```php
// Trong tinker hoặc controller
dd(config('services.google_maps.api_key'));
```

### Test API trực tiếp:

```bash
curl "https://maps.googleapis.com/maps/api/distancematrix/json?origins=Hà+Nội&destinations=Hải+Phòng&key=YOUR_API_KEY"
```

### Kiểm tra Log:

```bash
# Xem log real-time
tail -f storage/logs/laravel.log | grep "Google Maps"

# Tìm lỗi
grep "Google Maps API error" storage/logs/laravel.log
```

### Các lỗi thường gặp:

1. **"REQUEST_DENIED"**
   - API Key không hợp lệ
   - API chưa được bật
   - API Key bị giới hạn

2. **"OVER_QUERY_LIMIT"**
   - Vượt quá quota
   - Cần bật billing

3. **"ZERO_RESULTS"**
   - Không tìm thấy địa chỉ
   - Địa chỉ không hợp lệ

---

## 📝 CẤU TRÚC ĐỊA CHỈ

### Format địa chỉ đầy đủ:

```
{Số nhà, tên đường}, {Phường/Xã}, {Quận/Huyện}, {Tỉnh/Thành phố}
```

**Ví dụ**:
```
123 Đường ABC, Phường Dịch Vọng, Quận Cầu Giấy, Hà Nội
```

### Các thành phần:

- **origin_address**: Số nhà, tên đường (từ admin/shipping)
- **origin_ward**: Phường/Xã (từ admin/shipping)
- **origin_district**: Quận/Huyện (từ admin/shipping)
- **origin_city**: Tỉnh/Thành phố (từ admin/shipping)

Tương tự cho destination từ checkout form.

---

## 🎯 KẾT QUẢ

Sau khi tích hợp:

✅ **Tính khoảng cách chính xác** dựa trên địa chỉ thực tế
✅ **Tự động fallback** nếu API không khả dụng
✅ **Cache kết quả** để tối ưu performance
✅ **Logging chi tiết** để debug
✅ **Xử lý lỗi toàn diện**

---

## 📚 TÀI LIỆU THAM KHẢO

- Google Distance Matrix API: https://developers.google.com/maps/documentation/distance-matrix
- Pricing: https://developers.google.com/maps/billing-and-pricing/pricing
- API Key Best Practices: https://developers.google.com/maps/api-security-best-practices

---

**Ngày tạo**: {{ date('Y-m-d') }}
**Phiên bản**: 1.0


