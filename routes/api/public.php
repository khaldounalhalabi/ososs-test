<?php

use App\Http\Controllers\AdminAuthController;

Route::prefix('/admin')
    ->name('admin.')
    ->group(function () {
        Route::post('login', [AdminAuthController::class, 'login'])->name('login');
        Route::post('request-password-reset', [AdminAuthController::class, 'requestToResetPassword'])->name('request-password-reset');
        Route::post('reset-password', [AdminAuthController::class, 'resetPassword'])->name('reset-password');
    });
