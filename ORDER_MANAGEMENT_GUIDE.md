# Hướng dẫn sử dụng hệ thống quản lý đơn hàng Meteor-Shop

## 🚀 Cài đặt và chạy ứng dụng

### 1. Cài đặt dependencies
```bash
composer install
npm install
```

### 2. Cấu hình môi trường
```bash
cp .env.example .env
php artisan key:generate
```

### 3. Chạy migration và seeder
```bash
php artisan migrate
php artisan db:seed
```

### 4. Chạy ứng dụng
```bash
# Terminal 1: Laravel server
php artisan serve

# Terminal 2: Vite dev server
npm run dev
```

## 👤 Tài khoản đăng nhập

- **Admin**: admin@meteor-shop.com / password
- **Staff**: staff@meteor-shop.com / password

## 📋 Tính năng đã hoàn thành

### ✅ Quản lý đơn hàng (Order Management)

#### 1. **Danh sách đơn hàng** (`/admin/orders`)
- **Hiển thị bảng**: ID, mã đơn hàng, khách hàng, ngày đặt, tổng tiền, trạng thái, thanh toán, phương thức
- **Phân trang**: 10/15/25/50 đơn hàng mỗi trang
- **Tìm kiếm và lọc**:
  - Theo ID đơn hàng
  - Theo mã đơn hàng
  - Theo tên khách hàng
  - Theo email khách hàng
  - Theo trạng thái đơn hàng (pending, processing, completed, cancelled, refunded)
  - Theo trạng thái thanh toán (pending, paid, failed)
  - Theo phương thức thanh toán (cash, bank, momo, paypal)
  - Theo khoảng ngày (từ ngày - đến ngày)
  - Theo khoảng tổng tiền (min - max)

#### 2. **Xem chi tiết đơn hàng** (`/admin/orders/{id}`)
- **Thông tin khách hàng**: Tên, email, SĐT, địa chỉ
- **Chi tiết sản phẩm**: Hình ảnh, tên, giá, số lượng, thành tiền
- **Thông tin giao hàng**: Địa chỉ, SĐT, phí ship, ghi chú
- **Tổng kết đơn hàng**: Tổng tiền SP, giảm giá, phí ship, tổng cộng
- **Thao tác nhanh**: Bắt đầu xử lý, hoàn thành, hủy đơn hàng
- **Lịch sử**: Ngày tạo, ngày cập nhật cuối

#### 3. **Cập nhật đơn hàng** (`/admin/orders/{id}/edit`)
- **Thay đổi trạng thái**: pending → processing → completed
- **Cập nhật thanh toán**: pending → paid/failed
- **Chỉnh sửa thông tin**: Địa chỉ giao hàng, SĐT, phí ship, ghi chú
- **Bảo vệ dữ liệu**: Không cho phép chỉnh sửa sản phẩm để giữ tính toàn vẹn

#### 4. **Tạo đơn hàng thủ công** (`/admin/orders/create`)
- **Chọn khách hàng**: Dropdown danh sách user
- **Chọn sản phẩm**: Dynamic form thêm/xóa sản phẩm
- **Tính toán tự động**: Tổng tiền, giảm giá, phí ship, tổng cộng
- **Áp dụng mã khuyến mãi**: Dropdown các promotion available
- **Thông tin giao hàng**: Địa chỉ, SĐT, phí ship, ghi chú

#### 5. **Xóa đơn hàng** (Soft Delete)
- **Xóa mềm**: Đơn hàng được đánh dấu deleted_at
- **Xác nhận**: Modal xác nhận trước khi xóa
- **Khôi phục**: Có thể restore đơn hàng đã xóa
- **Xóa vĩnh viễn**: Force delete nếu cần

### ✅ Thống kê và báo cáo

#### Dashboard thống kê:
- **Tổng đơn hàng**: Số lượng đơn hàng tổng cộng
- **Chờ xử lý**: Đơn hàng pending
- **Hoàn thành**: Đơn hàng completed
- **Doanh thu hôm nay**: Tổng tiền đơn hàng completed hôm nay
- **Doanh thu tháng**: Tổng tiền đơn hàng completed trong tháng

### ✅ Giao diện chuyên nghiệp

#### Thiết kế UI/UX:
- **Bootstrap 5.3.2**: Framework CSS hiện đại
- **Responsive**: Tương thích mobile/tablet
- **Icons**: Bootstrap Icons + Font Awesome
- **Color scheme**: Professional admin theme
- **Cards layout**: Thông tin được tổ chức rõ ràng
- **Tables**: Bảng có pagination, sorting, filtering
- **Modals**: Xác nhận xóa, thông báo
- **Alerts**: Success/error messages
- **Badges**: Trạng thái với màu sắc phân biệt

## 🗄️ Cấu trúc Database

### Models đã tạo:
- **Order**: Đơn hàng chính với soft delete
- **OrderDetail**: Chi tiết sản phẩm trong đơn hàng
- **User**: Người dùng (admin, staff, user)
- **Product**: Sản phẩm
- **Category**: Danh mục sản phẩm
- **Brand**: Thương hiệu
- **Promotion**: Mã khuyến mãi

### Relationships:
- Order belongsTo User
- Order belongsTo Promotion
- Order hasMany OrderDetail
- OrderDetail belongsTo Order
- OrderDetail belongsTo Product
- Product belongsTo Category
- Product belongsTo Brand
- Category hasMany Product
- Brand hasMany Product

## 🔧 API Endpoints

### Routes đã tạo:
```php
GET    /admin/orders              # Danh sách đơn hàng
GET    /admin/orders/create       # Form tạo đơn hàng
POST   /admin/orders              # Lưu đơn hàng mới
GET    /admin/orders/{id}         # Chi tiết đơn hàng
GET    /admin/orders/{id}/edit    # Form chỉnh sửa
PUT    /admin/orders/{id}         # Cập nhật đơn hàng
DELETE /admin/orders/{id}         # Xóa đơn hàng
POST   /admin/orders/{id}/restore # Khôi phục đơn hàng
DELETE /admin/orders/{id}/force-delete # Xóa vĩnh viễn
GET    /admin/orders-statistics   # API thống kê
```

## 📊 Dữ liệu mẫu

### Seeder đã tạo:
- **2 Admin/Staff accounts**
- **10 Khách hàng**
- **4 Danh mục sản phẩm**
- **5 Thương hiệu**
- **6 Sản phẩm** (iPhone, Samsung, MacBook, Dell, AirPods, Apple Watch)
- **2 Mã khuyến mãi**
- **20 Đơn hàng mẫu** với các trạng thái khác nhau

## 🎯 Tính năng nâng cao

### Validation:
- Form validation đầy đủ
- Error handling
- Success messages
- Input sanitization

### Security:
- CSRF protection
- Mass assignment protection
- SQL injection prevention
- XSS protection

### Performance:
- Eager loading relationships
- Database indexing
- Pagination cho large datasets
- Optimized queries

## 🚀 Cách sử dụng

1. **Truy cập**: http://localhost:8000/admin/orders
2. **Xem danh sách**: Sử dụng bộ lọc để tìm đơn hàng
3. **Xem chi tiết**: Click vào icon mắt
4. **Chỉnh sửa**: Click vào icon edit
5. **Tạo mới**: Click nút "Tạo đơn hàng mới"
6. **Thao tác nhanh**: Sử dụng các nút trong chi tiết đơn hàng

## 📝 Ghi chú

- Hệ thống đã được thiết kế theo chuẩn Laravel best practices
- Code được tổ chức rõ ràng, dễ maintain
- UI/UX chuyên nghiệp, user-friendly
- Responsive design cho mọi thiết bị
- Đầy đủ tính năng CRUD và business logic
