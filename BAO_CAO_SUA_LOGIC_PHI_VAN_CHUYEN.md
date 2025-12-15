# BÁO CÁO SỬA LOGIC PHÍ VẬN CHUYỂN

## Ngày: 2025-12-14

---

## ✅ YÊU CẦU

Thay đổi cách tính phí vận chuyển:

### TRƯỚC (Sai):
```
Phí kích thước: Giá/km
Phí cân nặng: Giá/km

Công thức:
- Phí chiều dài = (Mét đầu × Giá/km + Mét tiếp × Giá/km) × km
- Phí chiều rộng = (Mét đầu × Giá/km + Mét tiếp × Giá/km) × km
- Phí chiều cao = (Mét đầu × Giá/km + Mét tiếp × Giá/km) × km
- Phí cân nặng = (Kg đầu × Giá/km + Kg tiếp × Giá/km) × km
→ Nhân km 4 lần riêng biệt
```

### SAU (Đúng):
```
Phí kích thước: Giá cố định (đ)
Phí cân nặng: Giá cố định (đ)

Công thức:
- Phí chiều dài = Mét đầu × Giá + Mét tiếp × Giá
- Phí chiều rộng = Mét đầu × Giá + Mét tiếp × Giá
- Phí chiều cao = Mét đầu × Giá + Mét tiếp × Giá
- Phí cân nặng = Kg đầu × Giá + Kg tiếp × Giá
- Tổng = (Phí dài + Phí rộng + Phí cao + Phí cân nặng) × km × số lượng
→ Nhân km 1 lần duy nhất
```

---

## ✅ THAY ĐỔI ĐÃ THỰC HIỆN

### 1. Sửa giao diện admin

**File:** `resources/views/admin/shipping/index.blade.php`

**Thay đổi:**
- Đổi label từ `"Chiều dài - Mét đầu (đ/km)"` → `"Chiều dài - Mét đầu"`
- Đổi đơn vị từ `đ/km` → `đ`
- Cập nhật alert info: "Tổng phí kích thước sẽ được nhân với khoảng cách (km)"

**Trước:**
```html
<label>Chiều dài - Mét đầu (đ/km)</label>
<input type="number" name="first_length_price" ...>
<span class="input-group-text">đ/km</span>
```

**Sau:**
```html
<label>Chiều dài - Mét đầu</label>
<input type="number" name="first_length_price" ...>
<span class="input-group-text">đ</span>
```

### 2. Cập nhật thông tin tóm tắt

**File:** `resources/views/admin/shipping/index.blade.php`

**Trước:**
```
Tiêu chuẩn = (Dài + Rộng + Cao) theo mét đầu + mét tiếp theo + 
             phí cân nặng đầu + mỗi kg tiếp theo, nhân với số lượng
```

**Sau:**
```
Tiêu chuẩn = (Tổng phí chiều dài + Tổng phí chiều rộng + 
             Tổng phí chiều cao + Tổng phí cân nặng) × 
             Khoảng cách (km) × Số lượng
```

### 3. Logic tính phí (ĐÃ ĐÚNG TỪ TRƯỚC)

**File:** `app/Models/ShippingSetting.php`

**Logic hiện tại:**
```php
// Bước 1: Tính phí từng chiều (CHƯA nhân km)
$lengthFee = $this->calculateDimensionFee($lengthMeters, 
    $this->first_length_price, $this->next_length_price);
$widthFee = $this->calculateDimensionFee($widthMeters, 
    $this->first_width_price, $this->next_width_price);
$heightFee = $this->calculateDimensionFee($heightMeters, 
    $this->first_height_price, $this->next_height_price);

// Bước 2: Tính phí cân nặng (CHƯA nhân km)
$weightFee = $this->first_weight_price + 
    ($extraWeightUnit * $this->next_weight_price);

// Bước 3: Tổng phí (CHƯA nhân km)
$totalDimensionAndWeightFee = $lengthFee + $widthFee + 
    $heightFee + $weightFee;

// Bước 4: Nhân với km (CHỈ 1 LẦN)
$fee = $totalDimensionAndWeightFee * $distanceKm;

// Bước 5: Nhân với số lượng
$finalFee = $fee * $quantity;
```

**Helper function:**
```php
private function calculateDimensionFee(float $meters, 
    float $firstPrice, float $nextPrice): float
{
    if ($meters <= 0) return 0;
    
    $extraUnit = max(0, ceil($meters - 1));
    return $firstPrice + ($extraUnit * $nextPrice);
}
```

---

## 📊 VÍ DỤ TÍNH PHÍ

### Dữ liệu:
- Sản phẩm: 3m × 2m × 1.5m, 50kg
- Khoảng cách: 10 km
- Số lượng: 1

### Cài đặt phí:
- Chiều dài - Mét đầu: 10,000 đ
- Chiều dài - Mét tiếp: 5,000 đ
- Chiều rộng - Mét đầu: 8,000 đ
- Chiều rộng - Mét tiếp: 4,000 đ
- Chiều cao - Mét đầu: 8,000 đ
- Chiều cao - Mét tiếp: 4,000 đ
- Cân nặng đầu: 15,000 đ
- Cân nặng tiếp: 7,000 đ

### Tính toán:

**Bước 1: Phí kích thước**
- Chiều dài: 10,000 + (3-1) × 5,000 = 10,000 + 10,000 = **20,000 đ**
- Chiều rộng: 8,000 + (2-1) × 4,000 = 8,000 + 4,000 = **12,000 đ**
- Chiều cao: 8,000 + (1.5-1) × 4,000 = 8,000 + 2,000 = **10,000 đ**

**Bước 2: Phí cân nặng**
- Cân nặng: 15,000 + (50-1) × 7,000 = 15,000 + 343,000 = **358,000 đ**

**Bước 3: Tổng phí (chưa nhân km)**
- Tổng = 20,000 + 12,000 + 10,000 + 358,000 = **400,000 đ**

**Bước 4: Nhân với khoảng cách**
- Phí = 400,000 × 10 km = **4,000,000 đ**

**Bước 5: Nhân với số lượng**
- Tổng = 4,000,000 × 1 = **4,000,000 đ**

---

## ✅ KẾT QUẢ

### Files đã sửa:
1. `resources/views/admin/shipping/index.blade.php` (dòng 164-248, 363-370)

### Logic:
- ✅ Logic tính phí đã đúng từ trước
- ✅ Chỉ cần sửa giao diện để phù hợp với logic

### Kiểm tra:
- ✅ Giao diện hiển thị đúng đơn vị "đ" (không còn "đ/km")
- ✅ Thông tin tóm tắt mô tả đúng công thức
- ✅ Logic tính phí: (Tổng phí) × km × số lượng

---

## 🧪 HƯỚNG DẪN TEST

1. Truy cập `/admin/shipping`
2. Kiểm tra các label:
   - ✅ "Chiều dài - Mét đầu" (không có "/km")
   - ✅ Đơn vị: "đ" (không phải "đ/km")
3. Kiểm tra thông tin tóm tắt:
   - ✅ Mô tả đúng công thức mới
4. Test tính phí:
   - Tạo đơn hàng với sản phẩm có kích thước và cân nặng
   - Kiểm tra phí vận chuyển tính đúng theo công thức mới

---

**TRẠNG THÁI: ✅ HOÀN THÀNH**

**Lưu ý:** Logic tính phí đã đúng từ trước, chỉ cần sửa giao diện để người dùng hiểu đúng cách tính.

