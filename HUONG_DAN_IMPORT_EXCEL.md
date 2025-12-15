# 📥 HƯỚNG DẪN IMPORT EXCEL KHOẢNG CÁCH VẬN CHUYỂN

---

## ⚠️ QUAN TRỌNG: CÀI ĐẶT TRƯỚC KHI SỬ DỤNG

```bash
composer require maatwebsite/excel
```

**Chạy lệnh trên trước khi sử dụng chức năng Import Excel!**

---

## 🚀 HƯỚNG DẪN SỬ DỤNG

### Bước 1: Tải file Excel mẫu

1. Truy cập `/admin/shipping`
2. Nhấn nút **"Import Excel"** (màu xanh lá)
3. Nhấn **"Tải file Excel mẫu"**
4. File `mau_khoang_cach_van_chuyen.xlsx` sẽ được tải về

---

### Bước 2: Điền dữ liệu vào file Excel

**File Excel phải có đúng 3 cột:**

| tinh_thanh_pho | quan_huyen | khoang_cach_km |
|----------------|------------|----------------|
| Hà Nội         | Quận Ba Đình | 8.5 |
| Hà Nội         | Quận Hoàn Kiếm | 10.0 |
| Hải Phòng      | Quận Hồng Bàng | 105.0 |

**Lưu ý:**
- ✅ Header phải chính xác: `tinh_thanh_pho`, `quan_huyen`, `khoang_cach_km`
- ✅ Không được thêm hoặc bớt cột
- ✅ Dữ liệu bắt đầu từ dòng 2
- ✅ Khoảng cách phải là số (VD: 10.5, 20, 105.75)

---

### Bước 3: Upload file

1. Nhấn nút **"Import Excel"**
2. Nhấn **"Chọn file Excel"**
3. Chọn file đã điền dữ liệu
4. Nhấn **"Import"**
5. Chờ xử lý (có thanh progress)

---

### Bước 4: Xem kết quả

#### ✅ Thành công:
```
Import thành công!
- Thêm mới: 50 bản ghi
- Cập nhật: 10 bản ghi
```

#### ⚠️ Có lỗi:
```
Import hoàn tất với lỗi:
- Thành công: 45 bản ghi
- Cập nhật: 10 bản ghi
- Lỗi: 5 bản ghi

Chi tiết lỗi:
- Dòng 7: Cột "khoang_cach_km" phải là số
- Dòng 12: Cột "tinh_thanh_pho" không được để trống
```

---

## 📋 QUY TẮC VALIDATION

### 1. Cột "tinh_thanh_pho"
- ✅ Bắt buộc
- ✅ Tối đa 255 ký tự
- ❌ Không được để trống

### 2. Cột "quan_huyen"
- ✅ Bắt buộc
- ✅ Tối đa 255 ký tự
- ❌ Không được để trống

### 3. Cột "khoang_cach_km"
- ✅ Bắt buộc
- ✅ Phải là số
- ✅ Phải >= 0
- ✅ Tối đa 999999.99
- ❌ Không được để trống
- ❌ Không được là chữ

---

## 🔄 XỬ LÝ TRÙNG LẶP

**Nếu tỉnh + huyện đã tồn tại:**
- ✅ Tự động cập nhật khoảng cách mới
- ✅ Không báo lỗi
- ✅ Đếm vào số lượng "Cập nhật"

**Ví dụ:**
```
Database có: Hà Nội - Quận Ba Đình - 8.5 km
File Excel:  Hà Nội - Quận Ba Đình - 10.0 km
→ Kết quả:   Hà Nội - Quận Ba Đình - 10.0 km (Cập nhật)
```

---

## ❌ CÁC LỖI THƯỜNG GẶP

### 1. File không đúng định dạng
```
❌ Lỗi: File phải có định dạng .xlsx hoặc .xls
✅ Giải pháp: Lưu file dưới dạng Excel (.xlsx)
```

### 2. Thiếu cột
```
❌ Lỗi: Dòng 2: Cột "tinh_thanh_pho" không được để trống
✅ Giải pháp: Kiểm tra header phải đúng: tinh_thanh_pho, quan_huyen, khoang_cach_km
```

### 3. Khoảng cách không phải số
```
❌ Lỗi: Dòng 5: Cột "khoang_cach_km" phải là số
✅ Giải pháp: Nhập số thay vì chữ (VD: 10.5 thay vì "mười")
```

### 4. Khoảng cách âm
```
❌ Lỗi: Dòng 7: Cột "khoang_cach_km" phải lớn hơn hoặc bằng 0
✅ Giải pháp: Nhập số dương (VD: 10.5 thay vì -5)
```

---

## 💡 MẸO SỬ DỤNG

### 1. Import số lượng lớn
- Nên chia nhỏ file (< 1000 dòng/file)
- Import từng file một
- Tránh timeout

### 2. Kiểm tra trước khi import
- Mở file Excel, kiểm tra header
- Kiểm tra dữ liệu mẫu
- Đảm bảo không có ô trống

### 3. Backup trước khi import
- Export dữ liệu hiện tại (nếu có)
- Import file mới
- Kiểm tra kết quả

---

## 📊 GIỚI HẠN

- **Kích thước file:** Tối đa 2MB
- **Định dạng:** .xlsx, .xls
- **Số lượng dòng:** Không giới hạn (khuyến nghị < 10,000)
- **Batch size:** 100 records/lần

---

## 🎯 VÍ DỤ FILE EXCEL HỢP LỆ

```
| tinh_thanh_pho | quan_huyen        | khoang_cach_km |
|----------------|-------------------|----------------|
| Hà Nội         | Quận Ba Đình      | 8.5            |
| Hà Nội         | Quận Hoàn Kiếm    | 10.0           |
| Hà Nội         | Quận Cầu Giấy     | 7.5            |
| Hải Phòng      | Quận Hồng Bàng    | 105.0          |
| Hải Phòng      | Quận Lê Chân      | 107.0          |
| Hải Dương      | Thành phố Hải Dương | 60.0         |
```

---

**Chúc bạn sử dụng thành công! 🎉**

