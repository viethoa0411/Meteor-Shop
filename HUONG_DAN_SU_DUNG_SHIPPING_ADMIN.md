# HƯỚNG DẪN SỬ DỤNG TRANG CÀI ĐẶT VẬN CHUYỂN ADMIN

## URL: `/admin/shipping`

---

## 1. CÀI ĐẶT ĐỊA CHỈ KHO HÀNG GỐC

### Mục đích:
Thiết lập địa chỉ kho hàng làm điểm xuất phát để tính khoảng cách vận chuyển.

### Cách sử dụng:
1. Chọn **Tỉnh/Thành phố** từ dropdown (chỉ hiển thị các tỉnh miền Bắc)
2. Chọn **Quận/Huyện** (dropdown tự động load sau khi chọn tỉnh)
3. Chọn **Phường/Xã** (dropdown tự động load sau khi chọn quận)
4. Nhập **Địa chỉ chi tiết** (tùy chọn): Số nhà, tên đường...
5. Nhấn nút **"Lưu tất cả cài đặt"** ở cuối trang

### Lưu ý:
- Hệ thống chỉ hỗ trợ địa chỉ kho hàng tại **miền Bắc**
- Địa chỉ này sẽ được hiển thị ở phần "Địa chỉ kho hàng hiện tại" bên dưới

---

## 2. CÀI ĐẶT PHÍ VẬN CHUYỂN

### 2.1. Miễn phí vận chuyển

#### Bật/Tắt tính năng:
- Bật checkbox **"Bật miễn phí vận chuyển"**
- Nhập **Ngưỡng miễn phí vận chuyển** (VD: 10,000,000 đ)
- Khi bật: Đơn hàng từ ngưỡng này trở lên sẽ được miễn phí ship
- Khi tắt: Tất cả đơn hàng đều phải trả phí ship

### 2.2. Phí lắp đặt
- Nhập phí lắp đặt (VD: 50,000 đ)
- Phí này sẽ được cộng thêm khi khách chọn dịch vụ lắp đặt

### 2.3. Phí kích thước (tính theo mét, giá trên km)
Cấu hình phí cho từng chiều của sản phẩm:

**Chiều dài:**
- Mét đầu tiên: VD: 10,000 đ/km
- Mét tiếp theo: VD: 5,000 đ/km

**Chiều rộng:**
- Mét đầu tiên: VD: 8,000 đ/km
- Mét tiếp theo: VD: 4,000 đ/km

**Chiều cao:**
- Mét đầu tiên: VD: 8,000 đ/km
- Mét tiếp theo: VD: 4,000 đ/km

### 2.4. Phí theo cân nặng (giá trên km)
- Cân nặng đầu tiên (kg): VD: 15,000 đ/km
- Mỗi kg tiếp theo: VD: 7,000 đ/km

### 2.5. Phụ phí theo phương thức

**Giao nhanh (Express):**
- Tên hiển thị: VD: "Giao nhanh 24h"
- Loại phụ phí: % hoặc đ
- Giá trị: VD: 20% hoặc 50,000 đ

**Giao hỏa tốc (Fast):**
- Tên hiển thị: VD: "Giao hỏa tốc 12h"
- Loại phụ phí: % hoặc đ
- Giá trị: VD: 40% hoặc 100,000 đ

---

## 3. QUẢN LÝ KHOẢNG CÁCH VẬN CHUYỂN

### 3.1. Xem danh sách
- Bảng hiển thị tất cả khoảng cách đã cấu hình
- Có phân trang (10 records/trang)
- Có thể lọc theo tỉnh/thành phố

### 3.2. Thêm mới khoảng cách
1. Nhấn nút **"Thêm mới"**
2. Chọn **Tỉnh/Thành phố**
3. Chọn **Quận/Huyện/Thị Xã**
4. Nhập **Khoảng cách (Km)** (VD: 15.5)
5. Nhấn **"Lưu"**

### 3.3. Sửa khoảng cách
1. Nhấn nút **Sửa** (icon bút chì màu vàng) ở hàng cần sửa
2. Thay đổi thông tin
3. Nhấn **"Lưu"**

### 3.4. Xóa khoảng cách
1. Nhấn nút **Xóa** (icon thùng rác màu đỏ) ở hàng cần xóa
2. Xác nhận xóa trong popup
3. Record sẽ bị xóa khỏi hệ thống

### 3.5. Lọc theo tỉnh
1. Chọn tỉnh từ dropdown **"Lọc theo tỉnh/thành phố"**
2. Bảng sẽ chỉ hiển thị các record của tỉnh đó
3. Nhấn **"Làm mới"** để xem lại tất cả

---

## 4. LƯU CÀI ĐẶT

### Quan trọng:
- Chỉ có **1 nút "Lưu tất cả cài đặt"** duy nhất ở cuối trang
- Nút này sẽ lưu **TẤT CẢ** các thay đổi:
  - Địa chỉ kho hàng gốc
  - Cài đặt phí vận chuyển
  - Miễn phí vận chuyển
  - Phí lắp đặt
  - Phí kích thước
  - Phí cân nặng
  - Phụ phí theo phương thức

### Lưu ý:
- **KHÔNG CẦN** lưu riêng từng phần
- Chỉ cần thay đổi bất kỳ thông tin nào, sau đó nhấn nút "Lưu tất cả cài đặt" ở cuối trang
- Hệ thống sẽ hiển thị thông báo "Cập nhật cài đặt vận chuyển thành công!"

---

## 5. QUY TẮC TÍNH PHÍ VẬN CHUYỂN

### Công thức:
```
Phí tiêu chuẩn = (
    Phí chiều dài + 
    Phí chiều rộng + 
    Phí chiều cao + 
    Phí cân nặng
) × Khoảng cách (km) × Số lượng

Phí giao nhanh = Phí tiêu chuẩn + Phụ phí giao nhanh
Phí giao hỏa tốc = Phí tiêu chuẩn + Phụ phí giao hỏa tốc
```

### Ví dụ:
- Sản phẩm: 3m × 2m × 1.5m, 50kg
- Khoảng cách: 10 km
- Số lượng: 1

**Tính phí:**
- Chiều dài: (1 × 10,000) + (2 × 5,000) = 20,000 đ/km
- Chiều rộng: (1 × 8,000) + (1 × 4,000) = 12,000 đ/km
- Chiều cao: (1 × 8,000) + (0.5 × 4,000) = 10,000 đ/km
- Cân nặng: (1 × 15,000) + (49 × 7,000) = 358,000 đ/km
- **Tổng:** (20,000 + 12,000 + 10,000 + 358,000) × 10 = 4,000,000 đ

---

## 6. THÔNG TIN TÓM TẮT

Ở cuối trang có phần **"Thông tin tóm tắt"** hiển thị:
- Quy tắc tính phí vận chuyển
- Phụ phí giao nhanh/hỏa tốc
- Ngưỡng miễn phí vận chuyển
- Địa chỉ kho hàng hiện tại

---

## 7. TROUBLESHOOTING

### Lỗi: "Vui lòng chọn Tỉnh/Thành phố"
→ Bạn chưa chọn tỉnh/thành phố trong phần địa chỉ kho hàng

### Lỗi: "Địa chỉ này đã tồn tại trong hệ thống"
→ Khoảng cách cho tỉnh-quận này đã được thêm rồi, hãy sửa thay vì thêm mới

### Không thấy nút Lưu
→ Scroll xuống cuối trang, nút "Lưu tất cả cài đặt" nằm ở đó

### Checkbox miễn phí vận chuyển không hoạt động
→ Đã được sửa! Nếu vẫn lỗi, hãy clear cache trình duyệt (Ctrl+F5)

---

**Chúc bạn sử dụng hiệu quả! 🚀**

