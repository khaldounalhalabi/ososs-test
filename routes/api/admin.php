<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\CountryController;

Route::get('/logout', [AdminAuthController::class, 'logout'])->name('logout');
Route::post('/update-user-details', [AdminAuthController::class, 'updateUserDetails'])->name('update-user-details');
Route::get('/refresh-token', [AdminAuthController::class, 'refreshToken'])->name('refresh-token');
Route::get('/user-details', [AdminAuthController::class, 'userDetails'])->name('user-details');

Route::apiResource('/countries', CountryController::class)->names('countries');
