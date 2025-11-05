<?php

use App\Http\Controllers\Admin\ProductController;
use App\Models\Category;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CategoryController;

Route::redirect('/', '/admin')->name('home');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::view('/', 'admin.dashboard')->name('dashboard');
    Route::view('/categories', 'admin.categories.list')->name('categories.list');

    Route::get('/products/trash',           [ProductController::class, 'trash'])->name('products.trash');
    Route::get('/products/{id}/restore',    [ProductController::class, 'restore'])->name('products.restore');
    Route::get('/products/{id}/force',      [ProductController::class, 'force'])->name('products.force');

    // admin.products.index/create/store/show/edit/update/destroy
    Route::resource('products', ProductController::class)->names([
        'index'         =>  'products.list', 
        'create'        =>  'products.create', 
        'store'         =>  'products.store',
        'show'          =>  'products.show',
        'edit'          =>  'products.edit',
        'update'        =>  'products.update',
        'destroy'       =>  'products.destroy',
    ]);
});

Route::get('/', function () {
    return view('client.home');
});
Route::get('/admin', function () {
    return view('admin.dashboard');
});

Route::prefix('admin/categories')->name('admin.categories.')->group(function () {
    Route::get('/', [CategoryController::class, 'list'])->name('list');
    Route::get('/create', [CategoryController::class, 'create'])->name('create');
    Route::post('/store', [CategoryController::class, 'store'])->name('store');
    Route::get('/edit/{id}', [CategoryController::class, 'edit'])->name('edit');
    Route::post('/update/{id}', [CategoryController::class, 'update'])->name('update');
    Route::delete('/delete/{id}', [CategoryController::class, 'destroy'])->name('destroy');
    Route::get('/admin/categories', [CategoryController::class, 'list'])->name('admin.categories.list');
});