<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\HomeController;
// --- ĐÃ SỬA: Đổi thành ProductController mới ---
use App\Http\Controllers\Client\ProductController as ClientProductController;
use App\Http\Controllers\Admin\Account\AdminController;
use App\Http\Controllers\Admin\Account\UserController as AccountUserController;

// ============ AUTHENTICATION ROUTES ============
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Đăng ký tài khoản
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Quên mật khẩu (OTP Email)
Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendOtp'])->name('password.email');
Route::get('/verify-otp', [ForgotPasswordController::class, 'showVerifyOtpForm'])->name('password.verify-otp');
Route::post('/verify-otp', [ForgotPasswordController::class, 'verifyOtp'])->name('password.verify-otp.post');
Route::get('/reset-password', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.update');

// ============ CLIENT ROUTES ============
Route::get('/', [HomeController::class, 'index'])->name('client.home');
Route::get('/home', [HomeController::class, 'index']);
Route::get('/search', [ProductPublicController::class, 'search'])->name('client.product.search');
Route::get('/category/{slug}', [ClientProductController::class, 'productsByCategory'])->name('client.product.category');
Route::get('/products/{slug}', [ClientProductController::class, 'showDetail'])->name('client.product.detail');


// ============ ADMIN ROUTES ============
Route::middleware(['admin'])->prefix('/admin')->name('admin.')->group(function () {

    // ===== DASHBOARD =====
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('dashboard');

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
        Route::get('/show/{id}', [ProductController::class, 'show'])->name('show');
        Route::delete('/delete/{id}', [ProductController::class, 'destroy'])->name('destroy');
    });
    // ====== ORDERS ======
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/{id}', [OrderController::class, 'show'])->name('show');
        Route::put('/{id}/update-status', [OrderController::class, 'updateStatus'])->name('updateStatus');
    });

    // ====== ACCOUNT MANAGEMENT ======
    Route::prefix('account')->name('account.')->group(function () {

        // ----- ADMIN -----
        Route::prefix('admins')->name('admin.')->group(function () {
            Route::get('/', [AdminController::class, 'index'])->name('list');
            Route::get('/create', [AdminController::class, 'create'])->name('create');
            Route::post('/', [AdminController::class, 'store'])->name('store');
            Route::get('/trash', [AdminController::class, 'trash'])->name('trash');
            Route::get('/{id}/edit', [AdminController::class, 'edit'])->name('edit');
            Route::get('/{id}', [AdminController::class, 'show'])->name('show');
            Route::put('/{id}', [AdminController::class, 'update'])->name('update');
            Route::delete('/{id}', [AdminController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/restore', [AdminController::class, 'restore'])->name('restore');
        });

        // ----- USER -----
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [AccountUserController::class, 'index'])->name('list');
            Route::get('/create', [AccountUserController::class, 'create'])->name('create');
            Route::post('/', [AccountUserController::class, 'store'])->name('store');
            Route::delete('/{id}', [AccountUserController::class, 'destroy'])->name('destroy');
            Route::get('/trash', [AccountUserController::class, 'trash'])->name('trash');
            Route::post('/{id}/restore', [AccountUserController::class, 'restore'])->name('restore');
            Route::get('/{id}/edit', [AccountUserController::class, 'edit'])->name('edit');
            Route::put('/{id}', [AccountUserController::class, 'update'])->name('update');
            Route::get('/{id}', [AccountUserController::class, 'show'])->name('show');
        });


    });
});