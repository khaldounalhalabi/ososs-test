<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\PriceListController;
use App\Http\Controllers\ProductController;

Route::get('/logout', [AdminAuthController::class, 'logout'])->name('logout');
Route::post('/update-user-details', [AdminAuthController::class, 'updateUserDetails'])->name('update-user-details');
Route::get('/refresh-token', [AdminAuthController::class, 'refreshToken'])->name('refresh-token');
Route::get('/user-details', [AdminAuthController::class, 'userDetails'])->name('user-details');

Route::apiResource('/countries', CountryController::class)->names('countries');
Route::apiResource('/currencies', CurrencyController::class)->names('currencies');
Route::apiResource('/products', ProductController::class)->names('products');

Route::apiResource('/price-lists', PriceListController::class)->only([
    'store', 'destroy'
])->names('price-lists');
