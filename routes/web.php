<?php

use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('admin.dashboard');
});
Route::get('/categories', function () {
    return view('admin.categories.list');
});
Route::get('/users', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('admin.users.list');
Route::get('/users/create', [App\Http\Controllers\Admin\UserController::class, 'create'])->name('admin.users.create');
Route::post('/users', [App\Http\Controllers\Admin\UserController::class, 'store'])->name('admin.users.store');

// ✅ Thêm mới:
Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
Route::put('/users/{id}', [UserController::class, 'update'])->name('admin.users.update');
Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('admin.users.destroy');

// ✅ Xóa mềm & khôi phục
Route::get('/users/trash', [App\Http\Controllers\Admin\UserController::class, 'trash'])->name('admin.users.trash');
Route::post('/users/{id}/restore', [App\Http\Controllers\Admin\UserController::class, 'restore'])->name('admin.users.restore');
Route::delete('/users/{id}/force-delete', [App\Http\Controllers\Admin\UserController::class, 'forceDelete'])->name('admin.users.forceDelete');
