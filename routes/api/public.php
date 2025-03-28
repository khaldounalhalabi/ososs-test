<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\CustomerAuthController;

Route::prefix('/admin')
    ->name('admin.')
    ->group(function () {
        Route::post('login', [AdminAuthController::class, 'login'])->name('login');
        Route::post('request-password-reset', [AdminAuthController::class, 'requestToResetPassword'])->name('request-password-reset');
        Route::post('reset-password', [AdminAuthController::class, 'resetPassword'])->name('reset-password');
    });

Route::prefix('/customer')
    ->name('customer.')
    ->group(function () {
        Route::post('register', [CustomerAuthController::class, 'register'])->name('register');
        Route::post('login', [CustomerAuthController::class, 'login'])->name('login');
        Route::post('request-password-reset', [CustomerAuthController::class, 'requestToResetPassword'])->name('request-password-reset');
        Route::post('reset-password', [CustomerAuthController::class, 'resetPassword'])->name('reset-password');
    });

