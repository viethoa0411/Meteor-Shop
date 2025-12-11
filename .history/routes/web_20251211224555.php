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

// ===== AUTH CONTROLLERS =====
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;

// ===== ADMIN CONTROLLERS =====
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MonthlyTargetController;

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\OrderAnalyticsController;
use App\Http\Controllers\Admin\OrderReturnController;


use App\Http\Controllers\Admin\Blog\BlogController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\Contact\ContactController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\Admin\ChatboxController;

// Admin Account
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

// Admin Wallet (quản trị)
use App\Http\Controllers\Admin\Wallet\WalletManagementController;
use App\Http\Controllers\Admin\Wallet\WalletDetailController;
use App\Http\Controllers\Admin\Wallet\WalletTransactionFilterController;
use App\Http\Controllers\Admin\Wallet\WalletTransactionActionController;
use App\Http\Controllers\Admin\Wallet\WalletWithdrawController;
use App\Http\Controllers\Admin\Wallet\WalletController as AdminWalletController;
use App\Http\Controllers\Admin\Wallet\WithdrawController as AdminWithdrawController;
use App\Http\Controllers\Admin\Wallet\SettingsController as AdminWalletSettingsController;

// Thêm từ nhánh Trang_Chu_Client
use App\Http\Controllers\Admin\HomeCategoryController;
use App\Http\Controllers\Admin\ShippingSettingController;

// ===== CLIENT CONTROLLERS =====
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\ProductClientController;
use App\Http\Controllers\Client\CartController;
use App\Http\Controllers\Client\Blog\BlogClientController;
use App\Http\Controllers\Client\CheckoutController;
use App\Http\Controllers\Client\ChatController;
use App\Http\Controllers\Client\WishlistController as ClientWishlistController;
use App\Http\Controllers\Client\Account\OrderController as ClientAccountOrderController;
use App\Http\Controllers\Client\Contact\ContactController as ClientContactController;

// Client Wallet
use App\Http\Controllers\Client\Wallet\WalletController as ClientWalletController;
use App\Http\Controllers\Client\Wallet\DepositController as ClientDepositController;
use App\Http\Controllers\Client\Wallet\WithdrawController as ClientWithdrawController;

// Bổ sung các controller client khác mà route đang dùng
use App\Http\Controllers\Client\RoomController;
use App\Http\Controllers\Client\CollectionController;
use App\Http\Controllers\Client\DesignController;
use App\Http\Controllers\Client\ShareController;


/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/

// Admin login / logout
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


/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        /* Dashboard */
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/revenue/filter', [DashboardController::class, 'index'])->name('revenue.filter');
        Route::get('/api/dashboard/revenue/control-chart', [DashboardController::class, 'revenueControlChartApi'])
            ->name('dashboard.revenue.control-chart');
>>>>>>> fd7b4da683f7cef1efaff108fd509ead4ee20159

        // Monthly Target
        Route::get('monthly-target/create', [MonthlyTargetController::class, 'create'])
            ->name('monthly_target.create');
        Route::post('monthly-target/store', [MonthlyTargetController::class, 'store'])
            ->name('monthly_target.store');

        /* Categories */
        Route::prefix('categories')->name('categories.')->group(function () {
            Route::get('/', [CategoryController::class, 'list'])->name('list');
            Route::get('/create', [CategoryController::class, 'create'])->name('create');
            Route::post('/store', [CategoryController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [CategoryController::class, 'edit'])->name('edit');
            Route::put('/update/{id}', [CategoryController::class, 'update'])->name('update');
            Route::delete('/delete/{id}', [CategoryController::class, 'destroy'])->name('destroy');
        });

        /* Home Categories (3 ảnh trang chủ) */
        Route::prefix('home-categories')->name('home-categories.')->group(function () {
            Route::get('/', [HomeCategoryController::class, 'index'])->name('index');
            Route::get('/create', [HomeCategoryController::class, 'create'])->name('create');
            Route::post('/', [HomeCategoryController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [HomeCategoryController::class, 'edit'])->name('edit');
            Route::put('/{id}', [HomeCategoryController::class, 'update'])->name('update');
            Route::delete('/{id}', [HomeCategoryController::class, 'destroy'])->name('destroy');
        });

        /* Products */
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

        /* Orders */
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [OrderController::class, 'list'])->name('list');
            Route::get('/analytics', [OrderAnalyticsController::class, 'index'])->name('analytics');

            // Returns management - Đặt trước route /{id}
            Route::get('/returns', [OrderReturnController::class, 'index'])->name('returns.index');
            Route::get('/{orderId}/returns', [OrderReturnController::class, 'show'])->name('returns.show');
            Route::post('/{orderId}/returns/approve', [OrderReturnController::class, 'approve'])->name('returns.approve');
            Route::post('/{orderId}/returns/reject', [OrderReturnController::class, 'reject'])->name('returns.reject');
            Route::post('/{orderId}/returns/update-status', [OrderReturnController::class, 'updateStatus'])->name('returns.updateStatus');

            // Order detail routes
            Route::get('/{id}', [OrderController::class, 'show'])->name('show');
            Route::put('/{id}/update-status', [OrderController::class, 'updateStatus'])->name('updateStatus');
        });

        /* Blogs */
        Route::prefix('blogs')->name('blogs.')->group(function () {
            Route::get('/', [BlogController::class, 'list'])->name('list');
            Route::get('/create', [BlogController::class, 'create'])->name('create');
            Route::post('/store', [BlogController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [BlogController::class, 'edit'])->name('edit');
            Route::put('/update/{id}', [BlogController::class, 'update'])->name('update');
            Route::get('/show/{id}', [BlogController::class, 'show'])->name('show');
            Route::delete('/delete/{id}', [BlogController::class, 'destroy'])->name('destroy');
        });

        /* Banners */
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
            Route::put('/{id}', [BannerController::class, 'update'])->name('update');
            Route::delete('/{id}', [BannerController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/restore', [BannerController::class, 'restore'])->name('restore');
            Route::delete('/{id}/force-delete', [BannerController::class, 'forceDelete'])->name('forceDelete');
            Route::put('/{id}/status', [BannerController::class, 'updateStatus'])->name('updateStatus');
            Route::post('/{id}/duplicate', [BannerController::class, 'duplicate'])->name('duplicate');
        });

        /* Contacts (Tư vấn thiết kế) */
        Route::prefix('contacts')->name('contacts.')->group(function () {
            Route::get('/', [ContactController::class, 'index'])->name('index');
            Route::get('/show/{id}', [ContactController::class, 'show'])->name('show');
            Route::get('/edit/{id}', [ContactController::class, 'edit'])->name('edit');
            Route::put('/update/{id}', [ContactController::class, 'update'])->name('update');
        });

        /* Promotions */
        Route::prefix('promotions')->name('promotions.')->group(function () {
            Route::get('/', [PromotionController::class, 'list'])->name('list');
            Route::get('/create', [PromotionController::class, 'create'])->name('create');
            Route::post('/store', [PromotionController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [PromotionController::class, 'edit'])->name('edit');
            Route::put('/update/{id}', [PromotionController::class, 'update'])->name('update');
            Route::delete('/delete/{id}', [PromotionController::class, 'destroy'])->name('destroy');
        });

        /* Comments / Reviews */
        Route::prefix('comments')->name('comments.')->group(function () {
            Route::get('/', [CommentController::class, 'index'])->name('index');
            Route::get('/pending', [CommentController::class, 'pending'])->name('pending');
            Route::get('/reported', [CommentController::class, 'reported'])->name('reported');
            Route::get('/settings', [CommentController::class, 'settings'])->name('settings');
            Route::post('/settings', [CommentController::class, 'saveSettings'])->name('settings.save');
            Route::get('/export', [CommentController::class, 'export'])->name('export');
            Route::get('/{id}/quick-view', [CommentController::class, 'quickView'])->name('quickView');
            Route::get('/{id}', [CommentController::class, 'show'])->name('show');
            Route::post('/{id}/approve', [CommentController::class, 'approve'])->name('approve');
            Route::post('/{id}/reject', [CommentController::class, 'reject'])->name('reject');
            Route::post('/{id}/hide', [CommentController::class, 'hide'])->name('hide');
            Route::post('/{id}/show', [CommentController::class, 'showComment'])->name('showComment');
            Route::post('/{id}/reply', [CommentController::class, 'reply'])->name('reply');
            Route::delete('/{review}/reply/{reply}', [CommentController::class, 'deleteReply'])->name('reply.destroy');
            Route::delete('/{review}/replies/bulk', [CommentController::class, 'bulkDeleteReplies'])->name('reply.bulkDestroy');
            Route::post('/bulk-approve', [CommentController::class, 'bulkApprove'])->name('bulkApprove');
            Route::post('/bulk-reject', [CommentController::class, 'bulkReject'])->name('bulkReject');
            Route::post('/bulk-hide', [CommentController::class, 'bulkHide'])->name('bulkHide');
            Route::post('/bulk-delete', [CommentController::class, 'bulkDelete'])->name('bulkDelete');
            Route::delete('/{id}', [CommentController::class, 'destroy'])->name('destroy');
        });

        /* Wallet (ADMIN) - Hợp nhất tất cả route ví vào 1 prefix */
        Route::prefix('wallet')->name('wallet.')->group(function () {
            Route::get('/', [AdminWalletController::class, 'index'])->name('index');

            // Deposit routes
            Route::get('/deposit/{id}', [AdminWalletController::class, 'depositDetail'])->name('deposit.detail');
            Route::post('/deposit/{id}/confirm', [AdminWalletController::class, 'confirmDeposit'])->name('deposit.confirm');
            Route::post('/deposit/{id}/reject', [AdminWalletController::class, 'rejectDeposit'])->name('deposit.reject');

            // Withdraw routes
            Route::get('/withdraw/{id}', [AdminWithdrawController::class, 'detail'])->name('withdraw.detail');
            Route::post('/withdraw/{id}/confirm', [AdminWithdrawController::class, 'confirm'])->name('withdraw.confirm');
            Route::post('/withdraw/{id}/reject', [AdminWithdrawController::class, 'reject'])->name('withdraw.reject');
            Route::post('/withdraw/{id}/processing', [AdminWithdrawController::class, 'markProcessing'])->name('withdraw.processing');

            // Settings routes
            Route::get('/settings', [AdminWalletSettingsController::class, 'index'])->name('settings');
            Route::put('/settings', [AdminWalletSettingsController::class, 'update'])->name('settings.update');
        });

        /* Wishlist (ADMIN) */
        Route::prefix('wishlist')->name('wishlist.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\AdminWishListController::class, 'index'])->name('index');
            Route::get('/{id}', [\App\Http\Controllers\Admin\AdminWishListController::class, 'show'])->name('show');
        });

        /* Account Management */
        Route::prefix('account')->name('account.')->group(function () {
            // Admins
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

            // Users
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

        /* Chatbox (ADMIN) */
        Route::prefix('chatbox')->name('chatbox.')->group(function () {
            Route::get('/', [ChatboxController::class, 'index'])->name('index');
            Route::get('/settings', [ChatboxController::class, 'settings'])->name('settings');
            Route::post('/settings', [ChatboxController::class, 'updateSettings'])->name('settings.update');
            Route::post('/quick-replies', [ChatboxController::class, 'updateQuickReplies'])->name('quick-replies.update');
            Route::post('/auto-replies', [ChatboxController::class, 'updateAutoReplies'])->name('auto-replies.update');
            Route::post('/toggle', [ChatboxController::class, 'toggle'])->name('toggle');
            Route::get('/unread-count', [ChatboxController::class, 'getUnreadCount'])->name('unread-count');
            Route::get('/{id}', [ChatboxController::class, 'show'])->name('show');
            Route::post('/{id}/send', [ChatboxController::class, 'sendMessage'])->name('send');
            Route::post('/{id}/close', [ChatboxController::class, 'closeSession'])->name('close');
            Route::delete('/{id}', [ChatboxController::class, 'deleteSession'])->name('delete');
            Route::get('/{id}/messages', [ChatboxController::class, 'getNewMessages'])->name('messages');
        });

        /* Shipping settings */
        Route::prefix('shipping')->name('shipping.')->group(function () {
            Route::get('/', [ShippingSettingController::class, 'index'])->name('index');
            Route::put('/', [ShippingSettingController::class, 'update'])->name('update');
            Route::post('/calculate-fee', [ShippingSettingController::class, 'calculateFee'])->name('calculate-fee');
        });
    


/*
|--------------------------------------------------------------------------
| CLIENT ROUTES (PUBLIC)
|--------------------------------------------------------------------------
*/

// Auth client (guest)
Route::middleware('guest')->group(function () {
    Route::get('/login-client', [AuthController::class, 'showLoginFormClient'])->name('client.login');
    Route::post('/login-client', [AuthController::class, 'loginClient'])->name('client.login.post');
});

Route::post('/logout-client', [AuthController::class, 'logoutClient'])->name('client.logout');

// Home
Route::get('/', [HomeController::class, 'index'])->name('client.home');
Route::get('/home', [HomeController::class, 'index']);

// Products (public)
Route::get('/search', [ProductClientController::class, 'search'])->name('client.product.search');
Route::get('/category/{slug}', [ProductClientController::class, 'productsByCategory'])->name('client.product.category');
Route::get('/products/{slug}', [ProductClientController::class, 'showDetail'])->name('client.product.detail');
Route::post('/products/variant/get', [ProductClientController::class, 'getVariant'])->name('client.product.variant.get');
Route::get('/products/{slug}/reviews', [ProductClientController::class, 'getReviews'])->name('client.product.reviews');
Route::get('/products/{slug}/reviews/check-updates', [ProductClientController::class, 'checkUpdates'])->name('client.product.reviews.check-updates');
Route::post('/products/{slug}/review', [ProductClientController::class, 'storeReview'])->name('client.product.review.store')->middleware('auth');
Route::post('/reviews/{review}/helpful', [ProductClientController::class, 'markHelpful'])->name('client.review.helpful')->middleware('auth');
Route::post('/reviews/{review}/report', [ProductClientController::class, 'reportReview'])->name('client.review.report')->middleware('auth');

// Danh sách sản phẩm, phòng, blog, collection, design, share
Route::get('/products', [ProductClientController::class, 'search'])->name('client.products.index');
Route::get('/rooms', [RoomController::class, 'index'])->name('client.rooms.index');
Route::get('/blogs/list', [BlogClientController::class, 'list'])->name('client.blogs.list');
Route::get('/blog/{slug}', [BlogClientController::class, 'show'])->name('client.blog.show');
Route::get('/collections', [CollectionController::class, 'index'])->name('client.collections.index');
Route::get('/collections/{slug}', [CollectionController::class, 'show'])->name('client.collections.show');
Route::get('/designs', [DesignController::class, 'index'])->name('client.designs.index');
Route::get('/designs/{slug}', [DesignController::class, 'show'])->name('client.designs.show');
Route::get('/shares', [ShareController::class, 'index'])->name('client.shares.index');
Route::get('/shares/{slug}', [ShareController::class, 'show'])->name('client.shares.show');

// Cart (session-based)
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update-qty', [CartController::class, 'updateQty'])->name('cart.updateQty');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');

// Contacts (public)
Route::get('/contact/list', [ClientContactController::class, 'list'])->name('client.contact.list');
Route::post('/contact/store', [ClientContactController::class, 'store'])->name('client.contact.store');

// Wishlist (client)
Route::get('/wishlist', [ClientWishlistController::class, 'index'])->name('client.wishlist.index');
Route::post('/wishlist/toggle', [ClientWishlistController::class, 'toggle'])->name('client.wishlist.toggle');

// Chatbox CLIENT API
Route::prefix('chat')->name('chat.')->group(function () {
    Route::get('/settings', [ChatController::class, 'getSettings'])->name('settings');
    Route::post('/send', [ChatController::class, 'sendMessage'])->name('send');
    Route::get('/messages', [ChatController::class, 'getMessages'])->name('messages');
    Route::post('/guest-info', [ChatController::class, 'updateGuestInfo'])->name('guest-info');
});


/*
|--------------------------------------------------------------------------
| CLIENT ACCOUNT ROUTES (AUTH REQUIRED)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('account')->name('client.account.')->group(function () {
    // Orders
    Route::get('/orders', [ClientAccountOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [ClientAccountOrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/tracking', [ClientAccountOrderController::class, 'tracking'])->name('orders.tracking');
    Route::post('/orders/{order}/cancel', [ClientAccountOrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/orders/{order}/reorder', [ClientAccountOrderController::class, 'reorder'])->name('orders.reorder');
    Route::post('/orders/{order}/return', [ClientAccountOrderController::class, 'returnRequest'])->name('orders.return');
    Route::post('/orders/{order}/mark-received', [ClientAccountOrderController::class, 'markAsReceived'])->name('orders.markReceived');

    // Refund routes
    Route::get('/orders/{order}/refund/return', [\App\Http\Controllers\Client\Account\RefundController::class, 'showReturnForm'])->name('orders.refund.return');
    Route::post('/orders/{order}/refund/return', [\App\Http\Controllers\Client\Account\RefundController::class, 'submitReturnRefund'])->name('orders.refund.return.submit');
    Route::get('/orders/{order}/refund/cancel', [\App\Http\Controllers\Client\Account\RefundController::class, 'showCancelRefundForm'])->name('orders.refund.cancel');
    Route::post('/orders/{order}/refund/cancel', [\App\Http\Controllers\Client\Account\RefundController::class, 'submitCancelRefund'])->name('orders.refund.cancel.submit');
    Route::post('/orders/{order}/refund/reset', [\App\Http\Controllers\Client\Account\RefundController::class, 'resetCancelRefund'])->name('orders.refund.cancel.reset');

    // Wallet (Client)
    Route::prefix('wallet')->name('wallet.')->group(function () {
        Route::get('/', [ClientWalletController::class, 'index'])->name('index');
        Route::get('/history', [ClientWalletController::class, 'history'])->name('history');

        // Deposit
        Route::get('/deposit', [ClientDepositController::class, 'index'])->name('deposit');
        Route::post('/deposit', [ClientDepositController::class, 'store'])->name('deposit.store');
        Route::get('/deposit/{id}/success', [ClientDepositController::class, 'success'])->name('deposit.success');
        Route::post('/deposit/{id}/cancel', [ClientDepositController::class, 'cancel'])->name('deposit.cancel');

        // Withdraw
        Route::get('/withdraw', [ClientWithdrawController::class, 'index'])->name('withdraw');
        Route::post('/withdraw', [ClientWithdrawController::class, 'store'])->name('withdraw.store');
        Route::get('/withdraw/{id}/success', [ClientWithdrawController::class, 'success'])->name('withdraw.success');
        Route::post('/withdraw/{id}/cancel', [ClientWithdrawController::class, 'cancel'])->name('withdraw.cancel');
    });

    // Review reports
    Route::get('/review-reports', [\App\Http\Controllers\Client\Account\ReviewReportController::class, 'index'])->name('review-reports.index');
});


/*
|--------------------------------------------------------------------------
| CHECKOUT (không gắn middleware, controller tự kiểm tra đăng nhập)
|--------------------------------------------------------------------------
*/
Route::get('/checkout', [CheckoutController::class, 'index'])->name('client.checkout.index');
Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('client.checkout.process');
Route::get('/checkout/confirm', [CheckoutController::class, 'confirm'])->name('client.checkout.confirm');
Route::post('/checkout/create-order', [CheckoutController::class, 'createOrder'])->name('client.checkout.createOrder');
Route::post('/checkout/apply-promotion', [CheckoutController::class, 'applyPromotion'])->name('client.checkout.applyPromotion');
Route::get('/order-success/{order_code}', [CheckoutController::class, 'success'])->name('client.checkout.success');
Route::post('/checkout/calculate-shipping', [CheckoutController::class, 'calculateShippingFee'])->name('client.checkout.calculateShipping');


/*
|--------------------------------------------------------------------------
| FALLBACK 404
|--------------------------------------------------------------------------
*/
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
