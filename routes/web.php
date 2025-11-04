<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\ProductPublicController;
use App\Http\Controllers\Client\ProductListingController;
use App\Http\Controllers\Client\SearchController;
use App\Http\Controllers\Admin\Account\AdminController;
use App\Http\Controllers\Admin\Account\UserController;

// ============ AUTHENTICATION ROUTES ============
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ============ CLIENT ROUTES ============
// Route gốc / trỏ trực tiếp đến trang chủ client
Route::get('/', [HomeController::class, 'index'])->name('client.home');

// Client routes với prefix /users
Route::prefix('users')->name('client.')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/home', [HomeController::class, 'index']);
    Route::get('/product/{slug}', [ProductPublicController::class, 'show'])->name('product.detail');
    // Listing + Search
    Route::get('/products', [ProductListingController::class, 'index'])->name('product.list');
    Route::get('/search', [ProductListingController::class, 'index'])->name('product.search');
    Route::get('/api/search/suggest', [SearchController::class, 'suggest'])->name('search.suggest');
    // SEO URLs for category and brand
    Route::get('/c/{categorySlug}', [ProductListingController::class, 'index'])->name('category.show');
    Route::get('/b/{brandSlug}', [ProductListingController::class, 'index'])->name('brand.show');
    // Newsletter
    Route::post('/newsletter/subscribe', [\App\Http\Controllers\Client\NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
    Route::get('/newsletter/confirm/{token}', [\App\Http\Controllers\Client\NewsletterController::class, 'confirm'])->name('newsletter.confirm');

    // Cart & Checkout
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Client\CartController::class, 'index'])->name('index');
        Route::post('/add', [\App\Http\Controllers\Client\CartController::class, 'add'])->name('add');
        Route::post('/update', [\App\Http\Controllers\Client\CartController::class, 'update'])->name('update');
        Route::post('/remove', [\App\Http\Controllers\Client\CartController::class, 'remove'])->name('remove');
        Route::post('/clear', [\App\Http\Controllers\Client\CartController::class, 'clear'])->name('clear');
    });
    Route::prefix('checkout')->name('checkout.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Client\CheckoutController::class, 'index'])->name('index');
        Route::post('/place-order', [\App\Http\Controllers\Client\CheckoutController::class, 'placeOrder'])->name('place');
        Route::get('/success', [\App\Http\Controllers\Client\CheckoutController::class, 'success'])->name('success');
    });
    // Coupon & shipping fee APIs
    Route::post('/cart/apply-coupon', [\App\Http\Controllers\Client\CartController::class, 'applyCoupon'])->name('cart.applyCoupon');
    Route::get('/api/shipping/fee', [\App\Http\Controllers\Client\CheckoutController::class, 'shippingFee'])->name('shipping.fee');

    // Wishlist
    Route::prefix('wishlist')->name('wishlist.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Client\WishlistController::class, 'index'])->name('index');
        Route::post('/toggle', [\App\Http\Controllers\Client\WishlistController::class, 'toggle'])->name('toggle');
        Route::post('/sync', [\App\Http\Controllers\Client\WishlistController::class, 'sync'])->name('sync');
    });
    // Compare
    Route::prefix('compare')->name('compare.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Client\CompareController::class, 'index'])->name('index');
        Route::post('/add', [\App\Http\Controllers\Client\CompareController::class, 'add'])->name('add');
        Route::post('/remove', [\App\Http\Controllers\Client\CompareController::class, 'remove'])->name('remove');
        Route::post('/clear', [\App\Http\Controllers\Client\CompareController::class, 'clear'])->name('clear');
    });
    // Price Watch
    Route::post('/price-watch/subscribe', [\App\Http\Controllers\Client\PriceWatchController::class, 'subscribe'])->name('pricewatch.subscribe');

    // Blog
    Route::prefix('blog')->name('blog.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Client\BlogController::class, 'index'])->name('index');
        Route::get('/c/{category:slug}', [\App\Http\Controllers\Client\BlogController::class, 'category'])->name('category');
        Route::get('/t/{tag:slug}', [\App\Http\Controllers\Client\BlogController::class, 'tag'])->name('tag');
        Route::get('/{slug}', [\App\Http\Controllers\Client\BlogController::class, 'show'])->name('show');
    });
});

// ============ ADMIN ROUTES ============
Route::middleware(['admin'])->prefix('/admin')->name('admin.')->group(function () {

    // ===== DASHBOARD =====
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // ====== CATEGORIES ======
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('list');
        Route::get('/create', [CategoryController::class, 'create'])->name('create');
        Route::post('/', [CategoryController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [CategoryController::class, 'edit'])->name('edit');
        Route::put('/{id}', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{id}', [CategoryController::class, 'destroy'])->name('destroy');
    });

    // ====== PRODUCTS ======
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('list');
        Route::get('/create', [ProductController::class, 'create'])->name('create');
        Route::post('/', [ProductController::class, 'store'])->name('store');
        Route::get('/{product}', [ProductController::class, 'show'])->name('show');
        Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
        Route::put('/{product}', [ProductController::class, 'update'])->name('update');
        Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
    });

    // ====== USERS ======
    // Users được quản lý trong account section

    // ====== ORDERS ======
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
        Route::put('/{order}/status', [OrderController::class, 'updateStatus'])->name('updateStatus');
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
            Route::put('/{id}', [AdminController::class, 'update'])->name('update');
            Route::post('/{id}/restore', [AdminController::class, 'restore'])->name('restore');
            Route::delete('/{id}', [AdminController::class, 'destroy'])->name('destroy');
            Route::get('/{id}', [AdminController::class, 'show'])->name('show');
        });

        // ----- USER -----
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('list');
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::get('/trash', [UserController::class, 'trash'])->name('trash');
            Route::get('/{id}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('/{id}', [UserController::class, 'update'])->name('update');
            Route::post('/{id}/restore', [UserController::class, 'restore'])->name('restore');
            Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
            Route::get('/{id}', [UserController::class, 'show'])->name('show');
        });
    });
});
