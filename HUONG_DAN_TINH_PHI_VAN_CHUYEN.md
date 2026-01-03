# HƯỚNG DẪN TÍNH PHÍ VẬN CHUYỂN KHI ĐẶT NHIỀU SẢN PHẨM

## 🎯 VÍ DỤ: ĐẶT 3 SẢN PHẨM MỘT LÚC

### Trường hợp 1: 3 sản phẩm khác nhau (mỗi sản phẩm 1 cái)

**Giả sử:**
- Sản phẩm A: 300cm × 200cm × 150cm, 25kg
- Sản phẩm B: 250cm × 180cm × 120cm, 20kg  
- Sản phẩm C: 400cm × 300cm × 200cm, 35kg
- Khoảng cách: 10km
- Giảm giá cùng đơn hàng: 10%
- Phương thức: Tiêu chuẩn

**Cách tính:**

#### Bước 1: Tính phí cho từng sản phẩm

**Sản phẩm A:**
- Chiều dài: 300cm ÷ 200 = 1.5 blocks → 1 block đầu + 1 block tiếp theo
  - Phí = 10,000 + 1 × 5,000 = 15,000đ
- Chiều rộng: 200cm ÷ 200 = 1 block → chỉ có 1 block đầu
  - Phí = 8,000đ
- Chiều cao: 150cm ÷ 200 = 0.75 blocks → chỉ có 1 block đầu
  - Phí = 8,000đ
- Cân nặng: 25kg ÷ 10 = 2.5 blocks → 1 block đầu + 2 blocks tiếp theo
  - Phí = 15,000 + 2 × 7,000 = 29,000đ

**Tổng phí kích thước + cân nặng = 15,000 + 8,000 + 8,000 + 29,000 = 60,000đ**

**Phí sản phẩm A = 60,000 × 10km × 1 (quantity) = 600,000đ**

**Sản phẩm B:**
- Chiều dài: 250cm ÷ 200 = 1.25 blocks → 1 block đầu + 1 block tiếp theo
  - Phí = 10,000 + 1 × 5,000 = 15,000đ
- Chiều rộng: 180cm ÷ 200 = 0.9 blocks → chỉ có 1 block đầu
  - Phí = 8,000đ
- Chiều cao: 120cm ÷ 200 = 0.6 blocks → chỉ có 1 block đầu
  - Phí = 8,000đ
- Cân nặng: 20kg ÷ 10 = 2 blocks → 1 block đầu + 1 block tiếp theo
  - Phí = 15,000 + 1 × 7,000 = 22,000đ

**Tổng phí kích thước + cân nặng = 15,000 + 8,000 + 8,000 + 22,000 = 53,000đ**

**Phí sản phẩm B = 53,000 × 10km × 1 (quantity) = 530,000đ**

**Sản phẩm C:**
- Chiều dài: 400cm ÷ 200 = 2 blocks → 1 block đầu + 1 block tiếp theo
  - Phí = 10,000 + 1 × 5,000 = 15,000đ
- Chiều rộng: 300cm ÷ 200 = 1.5 blocks → 1 block đầu + 1 block tiếp theo
  - Phí = 8,000 + 1 × 4,000 = 12,000đ
- Chiều cao: 200cm ÷ 200 = 1 block → chỉ có 1 block đầu
  - Phí = 8,000đ
- Cân nặng: 35kg ÷ 10 = 3.5 blocks → 1 block đầu + 3 blocks tiếp theo
  - Phí = 15,000 + 3 × 7,000 = 36,000đ

**Tổng phí kích thước + cân nặng = 15,000 + 12,000 + 8,000 + 36,000 = 71,000đ**

**Phí sản phẩm C = 71,000 × 10km × 1 (quantity) = 710,000đ**

#### Bước 2: Tổng phí trước giảm giá

**Tổng phí tiêu chuẩn = 600,000 + 530,000 + 710,000 = 1,840,000đ**

#### Bước 3: Áp dụng giảm giá cùng đơn hàng (có 3 sản phẩm)

**Giảm giá = 1,840,000 × 10% = 184,000đ**

**Phí sau giảm giá = 1,840,000 - 184,000 = 1,656,000đ**

#### Bước 4: Áp dụng phụ phí (nếu chọn express/fast)

**Nếu chọn Tiêu chuẩn:** Không có phụ phí

**Nếu chọn Express (20%):** Phụ phí = 1,656,000 × 20% = 331,200đ

**Nếu chọn Fast (40%):** Phụ phí = 1,656,000 × 40% = 662,400đ

#### Bước 5: Tổng phí cuối cùng

**Tiêu chuẩn:** 1,656,000đ

**Express:** 1,656,000 + 331,200 = 1,987,200đ

**Fast:** 1,656,000 + 662,400 = 2,318,400đ

---

### Trường hợp 2: 1 sản phẩm nhưng quantity = 3

**Giả sử:**
- Sản phẩm A: 300cm × 200cm × 150cm, 25kg, quantity = 3
- Khoảng cách: 10km
- Giảm giá cùng đơn hàng: 10%
- Phương thức: Tiêu chuẩn

**Cách tính:**

#### Bước 1: Tính phí cho 1 sản phẩm (như trên)

**Tổng phí kích thước + cân nặng = 60,000đ**

#### Bước 2: Nhân với quantity

**Phí sản phẩm A = 60,000 × 10km × 3 (quantity) = 1,800,000đ**

**Lưu ý:** Với 1 sản phẩm (dù quantity = 3), KHÔNG áp dụng giảm giá cùng đơn hàng vì chỉ có 1 item trong items array.

#### Bước 3: Tổng phí cuối cùng

**Tiêu chuẩn:** 1,800,000đ (không có giảm giá)

---

## 📋 TÓM TẮT LOGIC

1. **Tính phí cho mỗi item:**
   - Mỗi item trong items array được tính riêng
   - Phí item = (Phí kích thước + Phí cân nặng) × Khoảng cách × Quantity

2. **Tổng phí tất cả items:**
   - Cộng dồn phí của tất cả items

3. **Giảm giá cùng đơn hàng:**
   - Chỉ áp dụng khi có **2+ items** trong items array
   - Giảm giá = Tổng phí × Phần trăm giảm giá
   - Phí sau giảm = Tổng phí - Giảm giá

4. **Phụ phí phương thức:**
   - Express/Fast được tính trên phí sau giảm giá

5. **Tổng phí cuối cùng:**
   - Phí sau giảm giá + Phụ phí (nếu có)

---

## ⚠️ LƯU Ý QUAN TRỌNG

- **Giảm giá cùng đơn hàng chỉ áp dụng khi có 2+ items khác nhau**
- Nếu đặt 3 cái cùng 1 sản phẩm (quantity = 3), sẽ KHÔNG được giảm giá vì chỉ có 1 item trong items array
- Để được giảm giá, cần đặt ít nhất 2 sản phẩm khác nhau



