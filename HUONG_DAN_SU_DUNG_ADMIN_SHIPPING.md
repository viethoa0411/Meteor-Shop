# HƯỚNG DẪN SỬ DỤNG TRANG CÀI ĐẶT VẬN CHUYỂN ADMIN

## URL: `/admin/shipping`

---

## 📐 GIAO DIỆN MỚI

```
┌────────────────────────────────────────────────────────────────┐
│                    CÀI ĐẶT ĐỊA CHỈ KHO HÀNG GỐC                │
│  - Tỉnh/Thành phố                                              │
│  - Quận/Huyện                                                  │
│  - Phường/Xã                                                   │
│  - Địa chỉ chi tiết                                            │
│  [Lưu địa chỉ kho hàng]                                        │
└────────────────────────────────────────────────────────────────┘

┌──────────────────────────┬─────────────────────────────────────┐
│ CÀI ĐẶT PHÍ VẬN CHUYỂN   │ QUẢN LÝ KHOẢNG CÁCH VẬN CHUYỂN      │
│                          │                                     │
│ - Khoảng cách mặc định   │ - Bảng danh sách                    │
│ - Phí lắp đặt            │ - Lọc theo tỉnh                     │
│ - Phí kích thước (m)     │ - Thêm/Sửa/Xóa                      │
│ - Phí cân nặng (kg)      │ - Phân trang                        │
│ - Phụ phí giao hàng      │                                     │
│                          │                                     │
│ [Lưu cài đặt phí VC]     │                                     │
└──────────────────────────┴─────────────────────────────────────┘

┌────────────────────────────────────────────────────────────────┐
│                        THÔNG TIN TÓM TẮT                       │
│  - Quy tắc tính phí                                            │
│  - Địa chỉ kho hàng hiện tại                                   │
└────────────────────────────────────────────────────────────────┘
```

---

## 🎯 3 PHẦN CHÍNH

### 1️⃣ CÀI ĐẶT ĐỊA CHỈ KHO HÀNG GỐC

**Mục đích:** Thiết lập địa chỉ kho hàng làm điểm xuất phát

**Cách sử dụng:**
1. Chọn Tỉnh/Thành phố (chỉ miền Bắc)
2. Chọn Quận/Huyện
3. Chọn Phường/Xã
4. Nhập địa chỉ chi tiết (tùy chọn)
5. Nhấn **"Lưu địa chỉ kho hàng"**

**Lưu ý:**
- Chỉ hỗ trợ địa chỉ miền Bắc
- Phải chọn đầy đủ Tỉnh/Huyện/Xã

---

### 2️⃣ CÀI ĐẶT PHÍ VẬN CHUYỂN (Bên trái)

**Mục đích:** Cấu hình các loại phí vận chuyển

**Các phần:**

#### A. Khoảng cách mặc định
- Nhập khoảng cách (km)
- Dùng khi không tìm thấy địa chỉ khách trong database
- Mặc định: 10 km

#### B. Phí lắp đặt
- Phí cố định khi khách chọn dịch vụ lắp đặt
- VD: 50,000 đ

#### C. Phí kích thước (đơn vị: mét, giá trên km)
- **Chiều dài:** Mét đầu + Mét tiếp theo
- **Chiều rộng:** Mét đầu + Mét tiếp theo
- **Chiều cao:** Mét đầu + Mét tiếp theo

**Ví dụ:**
- Chiều dài - Mét đầu: 10,000 đ/km
- Chiều dài - Mét tiếp theo: 5,000 đ/km

**Cách tính:**
- Sản phẩm dài 3m, khoảng cách 10km
- Phí = (1 × 10,000 + 2 × 5,000) × 10 = 200,000 đ

#### D. Phí cân nặng (đơn vị: kg, giá trên km)
- **Cân nặng đầu tiên (kg):** VD: 15,000 đ/km
- **Mỗi kg tiếp theo:** VD: 7,000 đ/km

#### E. Phụ phí theo phương thức
- **Giao nhanh:** Tên + Loại (% hoặc đ) + Giá trị
- **Giao hỏa tốc:** Tên + Loại (% hoặc đ) + Giá trị

**Cách sử dụng:**
1. Thay đổi bất kỳ trường nào
2. Nhấn **"Lưu cài đặt phí vận chuyển"**
3. Không cần điền địa chỉ kho hàng

---

### 3️⃣ QUẢN LÝ KHOẢNG CÁCH VẬN CHUYỂN (Bên phải)

**Mục đích:** Quản lý khoảng cách từ kho đến các quận/huyện

**Chức năng:**

#### A. Xem danh sách
- Bảng hiển thị: ID, Tỉnh, Quận/Huyện, Khoảng cách
- Phân trang: 10 records/trang

#### B. Lọc theo tỉnh
1. Chọn tỉnh từ dropdown
2. Bảng chỉ hiển thị các quận/huyện của tỉnh đó
3. Nhấn "Làm mới" để xem tất cả

#### C. Thêm mới
1. Nhấn nút **"Thêm mới"**
2. Chọn Tỉnh/Thành phố
3. Chọn Quận/Huyện
4. Nhập khoảng cách (km)
5. Nhấn **"Lưu"**

#### D. Sửa
1. Nhấn nút **Sửa** (icon bút chì màu vàng)
2. Thay đổi thông tin
3. Nhấn **"Lưu"**

#### E. Xóa
1. Nhấn nút **Xóa** (icon thùng rác màu đỏ)
2. Xác nhận xóa
3. Record bị xóa khỏi hệ thống

---

## 🔄 QUY TRÌNH SỬ DỤNG

### Lần đầu cài đặt:
1. **Bước 1:** Cài đặt địa chỉ kho hàng gốc
2. **Bước 2:** Cài đặt khoảng cách mặc định (VD: 10 km)
3. **Bước 3:** Cài đặt các loại phí vận chuyển
4. **Bước 4:** Thêm khoảng cách cho các quận/huyện thường giao hàng

### Cập nhật thường xuyên:
- **Thay đổi phí:** Chỉ cần sửa phần "Cài đặt phí vận chuyển" → Lưu
- **Thêm địa điểm mới:** Thêm vào "Quản lý khoảng cách"
- **Đổi kho hàng:** Sửa "Địa chỉ kho hàng gốc" → Lưu

---

## ⚠️ LƯU Ý QUAN TRỌNG

### 1. Địa chỉ kho hàng
- ✅ Chỉ hỗ trợ miền Bắc
- ✅ Phải chọn đầy đủ Tỉnh/Huyện/Xã
- ❌ Không được để trống

### 2. Khoảng cách mặc định
- Dùng khi không tìm thấy địa chỉ khách trong database
- Nên đặt giá trị hợp lý (VD: 10-15 km)

### 3. Phí vận chuyển
- Tất cả các trường đều có thể cập nhật riêng biệt
- Không cần điền đầy đủ tất cả thông tin
- Chỉ sửa phần nào thì lưu phần đó

### 4. Khoảng cách vận chuyển
- Không được trùng lặp (cùng Tỉnh + Quận)
- Nên thêm đầy đủ các quận/huyện thường giao hàng
- Nếu không có trong database → dùng khoảng cách mặc định

---

## 🎯 MẸO SỬ DỤNG

1. **Tối ưu chi phí:**
   - Cài đặt khoảng cách chính xác cho từng quận/huyện
   - Điều chỉnh phí theo kích thước và cân nặng hợp lý

2. **Quản lý hiệu quả:**
   - Sử dụng bộ lọc để tìm nhanh
   - Cập nhật thường xuyên khi có địa điểm mới

3. **Tránh lỗi:**
   - Luôn kiểm tra lại sau khi lưu
   - Không xóa khoảng cách đang sử dụng

---

**Chúc bạn sử dụng hiệu quả! 🚀**

