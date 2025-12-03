<?php

// use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\Auth\AuthController;
// use App\Http\Controllers\Auth\RegisterController;
// use App\Http\Controllers\Auth\ForgotPasswordController;
// use App\Http\Controllers\Admin\CategoryController;
// use App\Http\Controllers\Admin\ProductController;
// use App\Http\Controllers\Admin\OrderController;
// use App\Http\Controllers\Admin\PromotionController;
// use App\Http\Controllers\Admin\Account\AdminController;
// use App\Http\Controllers\Admin\Account\UserController as AccountUserController;
// use App\Http\Controllers\Admin\BannerController;
// use App\Http\Controllers\Admin\Blog\BlogController;
// use App\Http\Controllers\Admin\DashboardController;
// use App\Http\Controllers\Admin\MonthlyTargetController;

// use App\Http\Controllers\Client\HomeController;
// use App\Http\Controllers\Client\ProductController as ClienProductController ;
// use App\Http\Controllers\Client\ProductPublicController;
// use App\Http\Controllers\Client\CartController;
// use App\Http\Controllers\Client\Blog\BlogClientController;
// use App\Http\Controllers\Client\CheckoutController;
// use App\Http\Controllers\Client\Account\OrderController as ClientAccountOrderController;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\OrderAnalyticsController;
use App\Http\Controllers\Admin\OrderReturnController;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\ProductPublicController;
use App\Http\Controllers\Admin\ProductController;

// --- ĐÃ SỬA: Đổi thành ProductController mới ---
use App\Http\Controllers\Client\ProductController as ClientProductController;
use App\Http\Controllers\Client\ProductClientController;
use App\Http\Controllers\Admin\Account\AdminController;
use App\Http\Controllers\Admin\Account\UserController as AccountUserController;
use App\Http\Controllers\Admin\AdminWishListController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\Blog\BlogController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MonthlyTargetController;

use App\Http\Controllers\Admin\Wallet\WalletDetailController;
use App\Http\Controllers\Admin\Wallet\WalletManagementController;
use App\Http\Controllers\Admin\Wallet\WalletTransactionActionController;
use App\Http\Controllers\Admin\Wallet\WalletTransactionFilterController;
use App\Http\Controllers\Admin\Wallet\WalletWithdrawController;
use App\Http\Controllers\Admin\Contact\ContactController;
use App\Http\Controllers\Admin\WishListController;
use App\Http\Controllers\Client\CartController;
use App\Http\Controllers\Client\Blog\BlogClientController;
use App\Http\Controllers\Client\CheckoutController;
use App\Http\Controllers\Client\Account\OrderController as ClientAccountOrderController;
use App\Http\Controllers\Client\Contact\ContactController as ClientContactController;
use App\Http\Controllers\Client\WishlistController as ClientWishlistController;



// Ưu tiên chuyển hướng /login sang /login-client 
Route::get('/login', function () {
    return redirect('/login-client');
});

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


//  ROUTES LOGIN/LOGOUT CLIENT
Route::middleware('guest')->group(function () {
    Route::get('/login-client', [LoginController::class, 'showClientLoginForm'])->name('client.login');
    Route::post('/login-client', [LoginController::class, 'loginClient'])->name('client.login.post');
});
Route::post('/logout-client', [LoginController::class, 'logoutClient'])->name('client.logout');



// ============ CLIENT ROUTES ============
    Route::middleware('guest')->group(function () {
    Route::get('/login-client', [AuthController::class, 'showLoginFormClient'])->name('client.login');
    Route::post('/login-client', [AuthController::class, 'loginClient'])->name('client.login.post');
});
Route::post('/logout-client', [AuthController::class, 'logoutClient'])->name('client.logout');
Route::get('/', [HomeController::class, 'index'])->name('client.home');
Route::get('/home', [HomeController::class, 'index']);
Route::get('/detail/{slug}', [ProductPublicController::class, 'show'])->name('client.product.detail');
Route::get('/search', [HomeController::class, 'search'])->name('client.product.search');
Route::get('/blogs/list', [BlogClientController::class, 'list'])->name('client.blogs.list');
Route::get('/blog/{slug}', [BlogClientController::class, 'show'])->name('client.blog.show');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update-qty', [CartController::class, 'updateQty'])->name('cart.updateQty');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');

// CHECKOUT
Route::get('/checkout', [CheckoutController::class, 'index'])->name('client.checkout.index');
Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('client.checkout.process');
Route::post('/checkout/create-order', [CheckoutController::class, 'createOrder'])->name('client.checkout.createOrder');

Route::middleware('auth')->prefix('account')->name('client.account.')->group(function () {
    Route::get('/orders', [ClientAccountOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [ClientAccountOrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/tracking', [ClientAccountOrderController::class, 'tracking'])->name('orders.tracking');
    Route::post('/orders/{order}/cancel', [ClientAccountOrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/orders/{order}/reorder', [ClientAccountOrderController::class, 'reorder'])->name('orders.reorder');
    Route::post('/orders/{order}/return', [ClientAccountOrderController::class, 'returnRequest'])->name('orders.return');
});

// đường dẫn đến trang chi tiết sản phẩm
Route::get('/products/{slug}', [ClientProductController::class, 'showDetail'])->name('client.product.detail');
// Hiển thị trang tổng hợp 6 danh mục + 4 sản phẩm mới nhất mỗi danh mục
Route::get('/categories', [ClientProductController::class, 'index'])->name('client.product.listProductsByCategory');

// Hiển thị tất cả sản phẩm của 1 danh mục cụ thể
Route::get('/category/{slug}', [ClientProductController::class, 'productsByCategory'])->name('client.product.category');

// đường dẫn đến trang sản phẩm sắp xếp theo danh mục
Route::get('/products', [ClientProductController::class, 'index'])->name('client.products.index');
//khi Người dùng click vào link: click Laravel tự động tạo URL: /category/noi-that-phong-khach


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
    
    // ====== PROMOTIONS ======
    Route::prefix('promotions')->name('promotions.')->group(function () {
        Route::get('/', [PromotionController::class, 'index'])->name('index');
        Route::get('/create', [PromotionController::class, 'create'])->name('create');
        Route::post('/', [PromotionController::class, 'store'])->name('store');
        Route::get('/{promotion}/edit', [PromotionController::class, 'edit'])->name('edit');
        Route::put('/{promotion}', [PromotionController::class, 'update'])->name('update');
        Route::delete('/{promotion}', [PromotionController::class, 'destroy'])->name('destroy');
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
     // ====== BLOGS ======
      // ====== BLOGS ======
      Route::prefix('blogs')->name('blogs.')->group(function () {
    Route::get('/', [BlogController::class, 'index'])->name('index');
    Route::get('/create', [BlogController::class, 'create'])->name('create');
    Route::post('/store', [BlogController::class, 'store'])->name('store');
    Route::get('/edit/{id}', [BlogController::class, 'edit'])->name('edit');
    Route::put('/update/{id}', [BlogController::class, 'update'])->name('update');
    Route::get('/show/{id}', [BlogController::class, 'show'])->name('show');
    Route::delete('/delete/{id}', [BlogController::class, 'destroy'])->name('destroy');
    });
});
