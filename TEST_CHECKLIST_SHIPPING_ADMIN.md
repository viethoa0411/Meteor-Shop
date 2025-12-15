# CHECKLIST KIỂM TRA CHỨC NĂNG SHIPPING ADMIN

## URL Test: `/admin/shipping`

---

## ✅ TEST 1: BẬT/TẮT NGƯỠNG PHÍ VẬN CHUYỂN

### Các bước test:
1. Truy cập `/admin/shipping`
2. Tìm checkbox "Bật miễn phí vận chuyển"
3. Click để bật/tắt checkbox
4. Kiểm tra text bên dưới có thay đổi:
   - Khi BẬT: "✓ Tính năng đang bật: Đơn hàng từ mức này trở lên sẽ được miễn phí vận chuyển"
   - Khi TẮT: "⊗ Tính năng đang tắt: Miễn phí vận chuyển sẽ không được áp dụng"
5. Nhấn nút "Lưu tất cả cài đặt" ở cuối trang
6. Kiểm tra thông báo "Cập nhật cài đặt vận chuyển thành công!"
7. Reload trang và kiểm tra checkbox vẫn giữ nguyên trạng thái

### Kết quả mong đợi:
- ✅ Checkbox hoạt động mượt mà
- ✅ Text thay đổi ngay lập tức khi click
- ✅ Dữ liệu được lưu vào database
- ✅ Sau khi reload, trạng thái vẫn đúng

---

## ✅ TEST 2: LƯU ĐỊA CHỈ KHO HÀNG GỐC

### Các bước test:
1. Truy cập `/admin/shipping`
2. Tìm phần "Cài đặt địa chỉ kho hàng gốc"
3. Chọn Tỉnh/Thành phố (ví dụ: Hà Nội)
4. Chọn Quận/Huyện (ví dụ: Cầu Giấy)
5. Chọn Phường/Xã (ví dụ: Dịch Vọng)
6. Nhập địa chỉ chi tiết (ví dụ: "123 Đường ABC")
7. Nhấn nút "Lưu tất cả cài đặt" ở cuối trang
8. Kiểm tra thông báo thành công
9. Reload trang và kiểm tra địa chỉ vẫn đúng

### Kết quả mong đợi:
- ✅ Dropdown tỉnh/huyện/xã hoạt động đúng
- ✅ Dữ liệu được lưu vào database
- ✅ Sau khi reload, địa chỉ vẫn hiển thị đúng
- ✅ Phần "Địa chỉ kho hàng hiện tại" ở cuối trang cập nhật đúng

---

## ✅ TEST 3: THÊM MỚI KHOẢNG CÁCH

### Các bước test:
1. Truy cập `/admin/shipping`
2. Tìm phần "Quản lý khoảng cách vận chuyển"
3. Nhấn nút "Thêm mới"
4. Modal hiện ra với title "Thêm khoảng cách mới"
5. Chọn Tỉnh/Thành phố (ví dụ: Hà Nội)
6. Chọn Quận/Huyện (ví dụ: Hoàn Kiếm)
7. Nhập khoảng cách (ví dụ: 5.5)
8. Nhấn nút "Lưu" trong modal
9. Kiểm tra modal đóng lại
10. Kiểm tra bảng dữ liệu có thêm record mới
11. Kiểm tra thông báo "Thêm khoảng cách thành công!"

### Kết quả mong đợi:
- ✅ Modal mở đúng
- ✅ Dropdown tỉnh/huyện hoạt động
- ✅ Dữ liệu được thêm vào database
- ✅ Bảng tự động reload và hiển thị record mới
- ✅ Thông báo thành công hiển thị

---

## ✅ TEST 4: SỬA KHOẢNG CÁCH

### Các bước test:
1. Truy cập `/admin/shipping`
2. Tìm một record trong bảng "Quản lý khoảng cách vận chuyển"
3. Nhấn nút "Sửa" (icon bút chì màu vàng)
4. Modal hiện ra với title "Sửa khoảng cách"
5. Kiểm tra dữ liệu cũ đã được load đúng
6. Thay đổi khoảng cách (ví dụ: từ 5.5 thành 6.0)
7. Nhấn nút "Lưu" trong modal
8. Kiểm tra modal đóng lại
9. Kiểm tra bảng dữ liệu đã cập nhật
10. Kiểm tra thông báo "Cập nhật khoảng cách thành công!"

### Kết quả mong đợi:
- ✅ Modal mở đúng với dữ liệu cũ
- ✅ Dữ liệu được cập nhật trong database
- ✅ Bảng tự động reload và hiển thị dữ liệu mới
- ✅ Thông báo thành công hiển thị

---

## ✅ TEST 5: XÓA KHOẢNG CÁCH

### Các bước test:
1. Truy cập `/admin/shipping`
2. Tìm một record trong bảng "Quản lý khoảng cách vận chuyển"
3. Nhấn nút "Xóa" (icon thùng rác màu đỏ)
4. Popup xác nhận hiện ra "Bạn có chắc chắn muốn xóa khoảng cách này?"
5. Nhấn "Xóa"
6. Kiểm tra record đã biến mất khỏi bảng
7. Kiểm tra thông báo "Xóa khoảng cách thành công!"

### Kết quả mong đợi:
- ✅ Popup xác nhận hiển thị
- ✅ Dữ liệu được xóa khỏi database
- ✅ Bảng tự động reload và record đã biến mất
- ✅ Thông báo thành công hiển thị

---

## ✅ TEST 6: LỌC THEO TỈNH

### Các bước test:
1. Truy cập `/admin/shipping`
2. Tìm dropdown "Lọc theo tỉnh/thành phố"
3. Chọn một tỉnh (ví dụ: Hà Nội)
4. Kiểm tra bảng chỉ hiển thị các record của tỉnh đó
5. Nhấn nút "Làm mới"
6. Kiểm tra bảng hiển thị lại tất cả record

### Kết quả mong đợi:
- ✅ Lọc hoạt động đúng
- ✅ Bảng chỉ hiển thị record của tỉnh được chọn
- ✅ Nút "Làm mới" reset filter

---

## ✅ TEST 7: PHÂN TRANG

### Các bước test:
1. Truy cập `/admin/shipping`
2. Kiểm tra nếu có > 10 records, phân trang sẽ hiển thị
3. Nhấn nút "Sau" để chuyển trang
4. Kiểm tra bảng hiển thị 10 records tiếp theo
5. Nhấn nút "Trước" để quay lại
6. Kiểm tra bảng hiển thị 10 records đầu tiên

### Kết quả mong đợi:
- ✅ Phân trang hiển thị đúng
- ✅ Chuyển trang hoạt động mượt mà
- ✅ Dữ liệu load đúng theo trang

---

## ✅ TEST 8: CHỈ CÓ 1 NÚT LƯU

### Các bước test:
1. Truy cập `/admin/shipping`
2. Scroll xuống cuối trang
3. Đếm số lượng nút "Lưu"

### Kết quả mong đợi:
- ✅ Chỉ có 1 nút "Lưu tất cả cài đặt" duy nhất
- ✅ Nút nằm ở vị trí trung tâm, dễ nhìn thấy
- ✅ Nút có kích thước lớn (btn-lg) và nổi bật

---

## KẾT QUẢ TỔNG HỢP

- [ ] Test 1: Bật/tắt ngưỡng phí vận chuyển
- [ ] Test 2: Lưu địa chỉ kho hàng gốc
- [ ] Test 3: Thêm mới khoảng cách
- [ ] Test 4: Sửa khoảng cách
- [ ] Test 5: Xóa khoảng cách
- [ ] Test 6: Lọc theo tỉnh
- [ ] Test 7: Phân trang
- [ ] Test 8: Chỉ có 1 nút Lưu

**Tất cả test PASS = Hệ thống hoạt động ổn định ✅**

