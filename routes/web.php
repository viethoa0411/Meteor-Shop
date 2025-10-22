<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;

Route::get('/', function () {
    return view('admin.dashboard');
});

Route::get('/categories', function () {
    return view('admin.categories.list');
});

// Order Management Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('orders', OrderController::class);
    Route::post('orders/{id}/restore', [OrderController::class, 'restore'])->name('orders.restore');
    Route::delete('orders/{id}/force-delete', [OrderController::class, 'forceDelete'])->name('orders.force-delete');
    Route::get('orders-statistics', [OrderController::class, 'statistics'])->name('orders.statistics');
});
