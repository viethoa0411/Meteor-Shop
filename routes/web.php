<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

// ============ AUTHENTICATION ROUTES ============
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ============ ADMIN ROUTES (Protected by AdminMiddleware) ============
Route::middleware(['admin'])->group(function () {
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::get('/categories', function () {
        return view('admin.categories.list');
    });

    //  Quản lý người dùng
    Route::get('/users', [UserController::class, 'index'])->name('admin.users.list');
    Route::get('/users/create', [UserController::class, 'create'])->name('admin.users.create');
    Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('admin.users.destroy');
    Route::get('/users/trash', [UserController::class, 'trash'])->name('admin.users.trash');
    Route::post('/users/{id}/restore', [UserController::class, 'restore'])->name('admin.users.restore');
    Route::delete('/users/{id}/force-delete', [UserController::class, 'forceDelete'])->name('admin.users.forceDelete');
    Route::get('/users/{id}', [UserController::class, 'show'])->name('admin.users.show');
});
