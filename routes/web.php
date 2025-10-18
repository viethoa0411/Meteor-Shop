<?php

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
