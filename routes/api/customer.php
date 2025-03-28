<?php

use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\ProductController;

Route::get('/logout', [CustomerAuthController::class, 'logout'])->name('logout');
Route::post('/update-user-details', [CustomerAuthController::class, 'updateUserDetails'])->name('update-user-details');
Route::get('/refresh-token', [CustomerAuthController::class, 'refreshToken'])->name('refresh-token');
Route::get('/user-details', [CustomerAuthController::class, 'userDetails'])->name('user-details');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{productId}', [ProductController::class, 'show'])->name('products.show');
