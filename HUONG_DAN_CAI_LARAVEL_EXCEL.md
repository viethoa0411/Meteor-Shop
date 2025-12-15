# HƯỚNG DẪN CÀI ĐẶT LARAVEL EXCEL

## Bước 1: Cài đặt package

```bash
composer require maatwebsite/excel
```

## Bước 2: Publish config (tùy chọn)

```bash
php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider" --tag=config
```

## Bước 3: Kiểm tra

Sau khi cài xong, bạn có thể sử dụng:
- `Maatwebsite\Excel\Facades\Excel`
- Import/Export classes

---

**LƯU Ý:** Vui lòng chạy lệnh trên trước khi tiếp tục!

