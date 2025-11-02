<?php

use App\Http\Controllers\Admin\Account\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;
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

// ====== CATEGORIES ======

Route::prefix('admin/categories')->name('admin.categories.')->group(function () {
    Route::get('/', [CategoryController::class, 'list'])->name('list');
    Route::get('/create', [CategoryController::class, 'create'])->name('create');
    Route::post('/store', [CategoryController::class, 'store'])->name('store');
    Route::get('/edit/{id}', [CategoryController::class, 'edit'])->name('edit');
    Route::post('/update/{id}', [CategoryController::class, 'update'])->name('update');
    Route::delete('/delete/{id}', [CategoryController::class, 'destroy'])->name('destroy');
    Route::get('/admin/categories', [CategoryController::class, 'list'])->name('admin.categories.list');
});

 // ====== PRODUCTS ======
    Route::prefix('products')->name('admin.products.')->group(function () {
        Route::get('/', [ProductController::class, 'list'])->name('list');
        Route::get('/create', [ProductController::class, 'create'])->name('create');
        Route::post('/store', [ProductController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [ProductController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [ProductController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [ProductController::class, 'destroy'])->name('destroy');
    });

    // ====== ORDERS ======
    Route::prefix('orders')->name('admin.orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/create', [OrderController::class, 'create'])->name('create');
        Route::post('/store', [OrderController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [OrderController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [OrderController::class, 'update'])->name('update');
        Route::get('/{id}/restore', [OrderController::class, 'restore'])->name('restore');

    });