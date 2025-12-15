# VÍ DỤ TÍNH PHÍ VẬN CHUYỂN CHI TIẾT

## 📋 CÔNG THỨC TỔNG QUÁT

```
Phí vận chuyển = [(Phí chiều dài + Phí chiều rộng + Phí chiều cao + Phí cân nặng) × Khoảng cách] × Số lượng
```

---

## 🔧 CÀI ĐẶT GIÁ PHÍ HIỆN TẠI

| Thành phần | Đơn vị đầu tiên | Đơn vị tiếp theo |
|------------|-----------------|------------------|
| **Chiều dài** | 8,000đ/mét đầu | 5,000đ/mét tiếp theo |
| **Chiều rộng** | 6,000đ/mét đầu | 4,000đ/mét tiếp theo |
| **Chiều cao** | 6,000đ/mét đầu | 4,000đ/mét tiếp theo |
| **Cân nặng** | 10,000đ/kg đầu | 3,000đ/kg tiếp theo |

---

## 📦 VÍ DỤ 1: TỦ LẠNH SAMSUNG 236L

### Thông tin sản phẩm:
- **Kích thước:** 144cm × 55.5cm × 63cm
- **Cân nặng:** 45.5 kg
- **Số lượng:** 1 cái
- **Khoảng cách:** 5 km (Hà Nội - Nam Từ Liêm → Quận Cầu Giấy)

### Bước 1: Chuyển đổi đơn vị
- Chiều dài: 144cm = **1.44 mét**
- Chiều rộng: 55.5cm = **0.555 mét**
- Chiều cao: 63cm = **0.63 mét**
- Cân nặng: **45.5 kg**

### Bước 2: Tính phí từng thành phần

#### 2.1. Phí chiều dài (1.44 mét)
```
Phí chiều dài = Mét đầu + (Số mét - 1) × Mét tiếp theo
              = 8,000 + (1.44 - 1) × 5,000
              = 8,000 + (0.44) × 5,000
              = 8,000 + 2,200
              = 10,200đ
```
**Lưu ý:** Hệ thống làm tròn lên: `ceil(1.44 - 1) = ceil(0.44) = 1`
```
Phí chiều dài = 8,000 + (1 × 5,000) = 13,000đ
```

#### 2.2. Phí chiều rộng (0.555 mét)
```
Phí chiều rộng = Mét đầu + (Số mét - 1) × Mét tiếp theo
               = 6,000 + (0.555 - 1) × 4,000
               = 6,000 + (-0.445) × 4,000
               = 6,000 + 0  (vì < 0)
               = 6,000đ
```
**Lưu ý:** Vì 0.555 < 1 mét, nên chỉ tính mét đầu tiên.

#### 2.3. Phí chiều cao (0.63 mét)
```
Phí chiều cao = Mét đầu + (Số mét - 1) × Mét tiếp theo
              = 6,000 + (0.63 - 1) × 4,000
              = 6,000 + (-0.37) × 4,000
              = 6,000 + 0  (vì < 0)
              = 6,000đ
```

#### 2.4. Phí cân nặng (45.5 kg)
```
Phí cân nặng = Kg đầu + (Số kg - 1) × Kg tiếp theo
             = 10,000 + (45.5 - 1) × 3,000
             = 10,000 + (44.5) × 3,000
             = 10,000 + 133,500
             = 143,500đ
```
**Lưu ý:** Hệ thống làm tròn lên: `ceil(45.5 - 1) = ceil(44.5) = 45`
```
Phí cân nặng = 10,000 + (45 × 3,000) = 145,000đ
```

### Bước 3: Tổng 4 thành phần
```
Tổng phí kích thước + cân nặng = 13,000 + 6,000 + 6,000 + 145,000
                                = 170,000đ
```

### Bước 4: Nhân với khoảng cách
```
Phí vận chuyển (chưa tính số lượng) = 170,000 × 5 km
                                     = 850,000đ
```

### Bước 5: Nhân với số lượng
```
Phí vận chuyển cuối cùng = 850,000 × 1
                         = 850,000đ
```

### ✅ KẾT QUẢ: **850,000đ**

---

## 📦 VÍ DỤ 2: NỒI CƠM ĐIỆN TOSHIBA 1.8L

### Thông tin sản phẩm:
- **Kích thước:** 35cm × 30cm × 25cm
- **Cân nặng:** 3.5 kg
- **Số lượng:** 1 cái
- **Khoảng cách:** 105 km (Hà Nội - Nam Từ Liêm → Hải Phòng - Quận Hồng Bàng)

### Bước 1: Chuyển đổi đơn vị
- Chiều dài: 35cm = **0.35 mét**
- Chiều rộng: 30cm = **0.30 mét**
- Chiều cao: 25cm = **0.25 mét**
- Cân nặng: **3.5 kg**

### Bước 2: Tính phí từng thành phần

#### 2.1. Phí chiều dài (0.35 mét)
```
Phí chiều dài = 8,000 + (0.35 - 1) × 5,000
              = 8,000 + 0  (vì < 1 mét)
              = 8,000đ
```

#### 2.2. Phí chiều rộng (0.30 mét)
```
Phí chiều rộng = 6,000 + (0.30 - 1) × 4,000
               = 6,000 + 0  (vì < 1 mét)
               = 6,000đ
```

#### 2.3. Phí chiều cao (0.25 mét)
```
Phí chiều cao = 6,000 + (0.25 - 1) × 4,000
              = 6,000 + 0  (vì < 1 mét)
              = 6,000đ
```

#### 2.4. Phí cân nặng (3.5 kg)
```
Phí cân nặng = 10,000 + (3.5 - 1) × 3,000
             = 10,000 + (2.5) × 3,000
             = 10,000 + 7,500
             = 17,500đ
```
**Lưu ý:** Hệ thống làm tròn lên: `ceil(3.5 - 1) = ceil(2.5) = 3`
```
Phí cân nặng = 10,000 + (3 × 3,000) = 19,000đ
```

### Bước 3: Tổng 4 thành phần
```
Tổng phí kích thước + cân nặng = 8,000 + 6,000 + 6,000 + 19,000
                                = 39,000đ
```

### Bước 4: Nhân với khoảng cách
```
Phí vận chuyển (chưa tính số lượng) = 39,000 × 105 km
                                     = 4,095,000đ
```

### Bước 5: Nhân với số lượng
```
Phí vận chuyển cuối cùng = 4,095,000 × 1
                         = 4,095,000đ
```

### ✅ KẾT QUẢ: **4,095,000đ**

---

## 🎯 KẾT LUẬN

Logic tính phí vận chuyển **HOÀN TOÀN ĐÚNG** với công thức:

```
Phí = [(Phí dài + Phí rộng + Phí cao + Phí nặng) × Khoảng cách] × Số lượng
```

Trong đó:
- **Phí dài** = Mét đầu + (Số mét - 1) × Mét tiếp theo
- **Phí rộng** = Mét đầu + (Số mét - 1) × Mét tiếp theo
- **Phí cao** = Mét đầu + (Số mét - 1) × Mét tiếp theo
- **Phí nặng** = Kg đầu + (Số kg - 1) × Kg tiếp theo

**Lưu ý:** Hệ thống sử dụng `ceil()` để làm tròn lên số đơn vị tiếp theo.

