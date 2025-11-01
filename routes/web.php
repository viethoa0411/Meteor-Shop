<?php

use App\Http\Controllers\Admin\Account\AdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('admin.dashboard');
});
Route::get('/categories', function () {
    return view('admin.categories.list');
});

// QUẢN LÝ TÀI KHOẢN ADMIN (role = admin)

// Hiển thị danh sách tất cả admin 
Route::get('/admins', [AdminController::class, 'index'])->name('admin.account.admin.list');

// Hiển thị form tạo admin mớimới
Route::get('/admins/create', [AdminController::class, 'create'])->name('admin.account.admin.create');

// Xử lý submit form tạo admin mới (lưu vào database)
Route::post('/admins', [AdminController::class, 'store'])->name('admin.account.admin.store');


// Hiển thị form chỉnh sửa thông tin admin
Route::get('/admins/{id}/edit', [AdminController::class, 'edit'])->name('admin.account.admin.edit');

// Hiển thị chi tiết thông tin 1 admin
Route::get('/admins/{id}', [AdminController::class, 'show'])->name('admin.account.admin.show');

// Xử lý submit form cập nhật thông tin admin
Route::put('/admins/{id}', [AdminController::class, 'update'])->name('admin.account.admin.update');

