<?php

use App\Http\Controllers\AdminAuthController;

Route::get('/logout', [AdminAuthController::class, 'logout'])->name('logout');
Route::post('/update-user-details', [AdminAuthController::class, 'updateUserDetails'])->name('update-user-details');
Route::get('/refresh-token', [AdminAuthController::class, 'refreshToken'])->name('refresh-token');

