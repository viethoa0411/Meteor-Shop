<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductVariantController;

Route::get('/', function () {
    return view('admin.dashboard');
});
Route::get('/categories', function () {
    return view('admin.categories.list');
});
Route::prefix('products')->name('admin.products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('list');
    Route::get('/create', [ProductController::class, 'create'])->name('create');
    Route::post('/', [ProductController::class, 'store'])->name('store');
    Route::get('/trash', [ProductController::class, 'trash'])->name('trash');
    Route::get('/{id}', [ProductController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [ProductController::class, 'edit'])->name('edit');
    Route::put('/{id}', [ProductController::class, 'update'])->name('update');
    Route::delete('/{id}', [ProductController::class, 'destroy'])->name('destroy');
    Route::post('/{id}/restore', [ProductController::class, 'restore'])->name('restore');
    Route::delete('/{id}/force-delete', [ProductController::class, 'forceDelete'])->name('forceDelete');
});
Route::prefix('products/{product}/variants')->name('admin.products.variants.')->group(function () {
    Route::get('/', [ProductVariantController::class, 'index'])->name('index');
    Route::post('/', [ProductVariantController::class, 'store'])->name('store');
});