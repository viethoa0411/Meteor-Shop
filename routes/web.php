<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\OrderShipmentController;
use App\Http\Controllers\Admin\OrderPaymentController;
use App\Http\Controllers\Admin\OrderRefundController;
use App\Http\Controllers\Admin\OrderReturnController;
use App\Http\Controllers\Admin\OrderNoteController;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\ProductClientController;
use App\Http\Controllers\Admin\Account\AdminController;
use App\Http\Controllers\Admin\Account\UserController as AccountUserController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\Blog\BlogController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MonthlyTargetController;
use App\Http\Controllers\Client\CartController;
use App\Http\Controllers\Client\Blog\BlogClientController;
use App\Http\Controllers\Client\CheckoutController;
use App\Http\Controllers\Client\Account\OrderController as ClientAccountOrderController;
use App\Http\Controllers\AssetController;

// ============ ASSET ROUTES ============
Route::get('/assets/css/{file}', [AssetController::class, 'css'])->name('assets.css');
Route::get('/assets/js/{file}', [AssetController::class, 'js'])->name('assets.js');

// ============ AUTHENTICATION ROUTES ============
Route::get('/login', [AuthController::class, 'showLoginFormadmin'])->name('login');
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

// ============ ADMIN ROUTES ============
Route::middleware(['admin'])->prefix('/admin')->name('admin.')->group(function () {

    // ===== DASHBOARD =====
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/revenue/filter', [DashboardController::class, 'index'])->name('revenue.filter');
    Route::get('monthly-target/create', [MonthlyTargetController::class, 'create'])
        ->name('monthly_target.create');

    Route::post('monthly-target/store', [MonthlyTargetController::class, 'store'])
        ->name('monthly_target.store');


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
        Route::delete('{product}/images/{image}', [ProductController::class, 'destroyImage'])->name('images.destroy');
    });

    // ====== ORDERS ======
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'list'])->name('list');
        Route::post('/bulk-action', [OrderController::class, 'bulkAction'])->name('bulkAction');
        Route::get('/export', [OrderController::class, 'export'])->name('export');
        Route::get('/create', [OrderController::class, 'create'])->name('create');
        Route::post('/', [OrderController::class, 'store'])->name('store');
     
        Route::get('/{id}/edit', [OrderController::class, 'edit'])->name('edit');
        Route::put('/{id}', [OrderController::class, 'update'])->name('update');
        Route::put('/{id}/update-status', [OrderController::class, 'updateStatus'])->name('updateStatus');
        Route::get('/{id}', [OrderController::class, 'show'])->name('show');

        // Shipments
        Route::prefix('{orderId}/shipments')->name('shipments.')->group(function () {
            Route::get('/', [OrderShipmentController::class, 'index'])->name('index');
            Route::get('/create', [OrderShipmentController::class, 'create'])->name('create');
            Route::post('/', [OrderShipmentController::class, 'store'])->name('store');
            Route::put('/{shipmentId}/status', [OrderShipmentController::class, 'updateStatus'])->name('updateStatus');
        });

        // Payments
        Route::prefix('{orderId}/payments')->name('payments.')->group(function () {
            Route::get('/', [OrderPaymentController::class, 'index'])->name('index');
            Route::post('/', [OrderPaymentController::class, 'store'])->name('store');
        });

        // Refunds
        Route::prefix('{orderId}/refunds')->name('refunds.')->group(function () {
            Route::get('/', [OrderRefundController::class, 'index'])->name('index');
            Route::get('/create', [OrderRefundController::class, 'create'])->name('create');
            Route::post('/', [OrderRefundController::class, 'store'])->name('store');
            Route::put('/{refundId}/status', [OrderRefundController::class, 'updateStatus'])->name('updateStatus');
        });

        // Returns
        Route::prefix('{orderId}/returns')->name('returns.')->group(function () {
            Route::get('/', [OrderReturnController::class, 'index'])->name('index');
            Route::get('/{returnId}', [OrderReturnController::class, 'show'])->name('show');
            Route::put('/{returnId}/status', [OrderReturnController::class, 'updateStatus'])->name('updateStatus');
        });

        // Notes
        Route::prefix('{orderId}/notes')->name('notes.')->group(function () {
            Route::post('/', [OrderNoteController::class, 'store'])->name('store');
            Route::put('/{noteId}', [OrderNoteController::class, 'update'])->name('update');
            Route::delete('/{noteId}', [OrderNoteController::class, 'destroy'])->name('destroy');
        });
    });

    // ====== BLOGS ======
    Route::prefix('blogs')->name('blogs.')->group(function () {
        Route::get('/', [BlogController::class, 'list'])->name('list');
        Route::get('/create', [BlogController::class, 'create'])->name('create');
        Route::post('/store', [BlogController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [BlogController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [BlogController::class, 'update'])->name('update');
        Route::get('/show/{id}', [BlogController::class, 'show'])->name('show');
        Route::delete('/delete/{id}', [BlogController::class, 'destroy'])->name('destroy');
    });

    // ====== BANNERS ======
    Route::prefix('banners')->name('banners.')->group(function () {
        Route::get('/', [BannerController::class, 'list'])->name('list');
        Route::get('/create', [BannerController::class, 'create'])->name('create');
        Route::post('/store', [BannerController::class, 'store'])->name('store');
        Route::get('/trash', [BannerController::class, 'trash'])->name('trash');
        Route::post('/bulk-delete', [BannerController::class, 'bulkDelete'])->name('bulkDelete');
        Route::post('/bulk-restore', [BannerController::class, 'bulkRestore'])->name('bulkRestore');
        Route::post('/bulk-force-delete', [BannerController::class, 'bulkForceDelete'])->name('bulkForceDelete');
        Route::post('/bulk-update-status', [BannerController::class, 'bulkUpdateStatus'])->name('bulkUpdateStatus');
        Route::post('/update-sort-order', [BannerController::class, 'updateSortOrder'])->name('updateSortOrder');
        Route::get('/{id}', [BannerController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [BannerController::class, 'edit'])->name('edit');
        Route::post('/{id}/duplicate', [BannerController::class, 'duplicate'])->name('duplicate');
        Route::put('/{id}', [BannerController::class, 'update'])->name('update');
        Route::delete('/{id}', [BannerController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/restore', [BannerController::class, 'restore'])->name('restore');
        Route::delete('/{id}/force-delete', [BannerController::class, 'forceDelete'])->name('forceDelete');
        Route::put('/{id}/status', [BannerController::class, 'updateStatus'])->name('updateStatus');
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

// ============ CLIENT ROUTES ============

Route::middleware('guest')->group(function () {
    Route::get('/login-client', [AuthController::class, 'showLoginFormClient'])->name('client.login');
    Route::post('/login-client', [AuthController::class, 'loginClient'])->name('client.login.post');
});

Route::post('/logout-client', [AuthController::class, 'logoutClient'])->name('client.logout');
Route::get('/', [HomeController::class, 'index'])->name('client.home');
Route::get('/home', [HomeController::class, 'index']);
Route::get('/search', [ProductClientController::class, 'search'])->name('client.product.search');
Route::get('/category/{slug}', [ProductClientController::class, 'productsByCategory'])->name('client.product.category');
Route::get('/products/{slug}', [ProductClientController::class, 'showDetail'])->name('client.product.detail');
Route::get('/products', [HomeController::class, 'index'])->name('client.products.index');
Route::get('/blogs/list', [BlogClientController::class, 'list'])->name('client.blogs.list');
Route::get('/blog/{slug}', [BlogClientController::class, 'show'])->name('client.blog.show');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update-qty', [CartController::class, 'updateQty'])->name('cart.updateQty');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');

Route::middleware('auth')->prefix('account')->name('client.account.')->group(function () {
    Route::get('/orders', [ClientAccountOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [ClientAccountOrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/tracking', [ClientAccountOrderController::class, 'tracking'])->name('orders.tracking');
    Route::post('/orders/{order}/cancel', [ClientAccountOrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/orders/{order}/reorder', [ClientAccountOrderController::class, 'reorder'])->name('orders.reorder');
    Route::post('/orders/{order}/return', [ClientAccountOrderController::class, 'returnRequest'])->name('orders.return');
});

// ============ CHECKOUT ROUTES ============
// Không dùng middleware auth ở đây vì CheckoutController đã tự kiểm tra và redirect đến client.login
Route::get('/checkout', [CheckoutController::class, 'index'])->name('client.checkout.index');
Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('client.checkout.process');
Route::get('/checkout/confirm', [CheckoutController::class, 'confirm'])->name('client.checkout.confirm');
Route::post('/checkout/create-order', [CheckoutController::class, 'createOrder'])->name('client.checkout.createOrder');
Route::get('/order-success/{order_code}', [CheckoutController::class, 'success'])->name('client.checkout.success');

Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
