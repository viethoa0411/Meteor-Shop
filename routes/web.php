<?php

use App\Http\Controllers\Admin\Account\AdminController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\Account\UserController;

Route::get('/', function () {
    return view('client.home');
});
Route::get('/admin', function () {
    return view('admin.dashboard');
});

// QUẢN LÝ TÀI KHOẢN ADMIN (role = admin)

// Hiển thị danh sách tất cả admin 
Route::get('/admins', [AdminController::class, 'index'])->name('admin.account.admin.list');

// Hiển thị form tạo admin mớimới
Route::get('/admins/create', [AdminController::class, 'create'])->name('admin.account.admin.create');

// Xử lý submit form tạo admin mới (lưu vào database)
Route::post('/admins', [AdminController::class, 'store'])->name('admin.account.admin.store');

// Trang hiển thị tài khoản bị ẩn
Route::get('/admins/trash', [AdminController::class, 'trash'])->name('admin.account.admin.trash');

// Hiển thị form chỉnh sửa thông tin admin
Route::get('/admins/{id}/edit', [AdminController::class, 'edit'])->name('admin.account.admin.edit');

// Hiển thị chi tiết thông tin 1 admin
Route::get('/admins/{id}', [AdminController::class, 'show'])->name('admin.account.admin.show');

// Xử lý submit form cập nhật thông tin admin
Route::put('/admins/{id}', [AdminController::class, 'update'])->name('admin.account.admin.update');

// ẩn tài khoản admin 
Route::delete('/admins/{id}', [AdminController::class, 'destroy'])->name('admin.account.admin.destroy');


// Khôi phục admin từ trash
Route::post('/admins/{id}/restore', [AdminController::class, 'restore'])->name('admin.account.admin.restore');

// Quản lý tài khoản user

// Hiển thị danh sách tất cả user (có phân trang + tìm kiếm)
Route::get('/users', [UserController::class, 'index'])->name('admin.account.users.list');

// Hiển thị form tạo user mới
    Route::get('/users/create', [UserController::class, 'create'])->name('admin.account.users.create');

// Xử lý submit form tạo user mới
Route::post('/users', [UserController::class, 'store'])->name('admin.account.users.store');

// ẩn tài khoản user
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('admin.account.users.destroy');