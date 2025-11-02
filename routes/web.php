<?php

use App\Http\Controllers\Admin\Account\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Account\UserController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;


// Đăng nhập / Đăng xuất 
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Đăng ký tài khoản
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Quên mật khẩu (OTP Email) - 3 bước
Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendOtp'])->name('password.email');
Route::get('/verify-otp', [ForgotPasswordController::class, 'showVerifyOtpForm'])->name('password.verify-otp');
Route::post('/verify-otp', [ForgotPasswordController::class, 'verifyOtp'])->name('password.verify-otp.post');


// Trang chủ client
Route::get('/', function () {
    return view('client.home');
});

// ============ ADMIN ROUTES - YÊU CẦU ĐĂNG NHẬP ============
Route::middleware(['auth'])->group(function () {

    // Trang Dashboard
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // QUẢN LÝ TÀI KHOẢN ADMIN (role = admin)
    // Hiển thị danh sách tất cả admin
    Route::get('/admins', [AdminController::class, 'index'])->name('admin.account.admin.list');

    // Hiển thị form tạo admin mới
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

    // QUẢN LÝ TÀI KHOẢN USER
    // Hiển thị danh sách tất cả user (có phân trang + tìm kiếm)
    Route::get('/users', [UserController::class, 'index'])->name('admin.account.users.list');

    // Hiển thị form tạo user mới
    Route::get('/users/create', [UserController::class, 'create'])->name('admin.account.users.create');

    // Xử lý submit form tạo user mới
    Route::post('/users', [UserController::class, 'store'])->name('admin.account.users.store');

    // ẩn tài khoản user
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('admin.account.users.destroy');

    // Hiển thị danh sách tài khoản bị ẩn
    Route::get('/users/trash', [UserController::class, 'trash'])->name('admin.account.users.trash');

    // Khôi phục user từ trash
    Route::post('/users/{id}/restore', [UserController::class, 'restore'])->name('admin.account.users.restore');

    // Hiển thị form chỉnh sửa thông tin user
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('admin.account.users.edit');

    // Xử lý submit form cập nhật thông tin user
    Route::put('/users/{id}', [UserController::class, 'update'])->name('admin.account.users.update');

    // Hiển thị chi tiết thông tin 1 user
    Route::get('/users/{id}', [UserController::class, 'show'])->name('admin.account.users.show');


});


// Admin routes - yêu cầu đăng nhập
Route::middleware(['auth'])->prefix('/admin')->name('admin.')->group(function () {

    // ====== CATEGORIES ======
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'list'])->name('list');
        Route::get('/create', [CategoryController::class, 'create'])->name('create');
        Route::post('/store', [CategoryController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [CategoryController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [CategoryController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [CategoryController::class, 'destroy'])->name('destroy');
    });

    // ====== PRODUCTS ======
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [ProductController::class, 'list'])->name('list');
        Route::get('/create', [ProductController::class, 'create'])->name('create');
        Route::post('/store', [ProductController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [ProductController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [ProductController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [ProductController::class, 'destroy'])->name('destroy');
    });

    // ====== USERS ======
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('list');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/store', [UserController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [UserController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [UserController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [UserController::class, 'destroy'])->name('destroy');
        Route::get('/users/{id}', [UserController::class, 'show'])->name('admin.users.show');
    });

    // ====== ORDERS ======
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/create', [OrderController::class, 'create'])->name('create');
        Route::post('/store', [OrderController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [OrderController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [OrderController::class, 'update'])->name('update');
        Route::get('/{id}/restore', [OrderController::class, 'restore'])->name('restore');
    });
});
