<?php

use App\Http\Controllers\Language\SetLanguage;
use App\Http\Controllers\Marketplace\MarketplaceController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\User\UserController;
use App\Http\Middleware\EnsureMarketplaceIsActive;
use App\Http\Middleware\EnsureMobileIsVerified;
use Illuminate\Support\Facades\Route;

Route::post('/set-language', [SetLanguage::class, 'setLanguage']);

Route::prefix('user')->group(function () {
    Route::post('register', [UserController::class, 'registerUser']);
    Route::post('forget-password', [UserController::class, 'forgetUserPassword']);
    Route::post('verify-mobile', [UserController::class, 'verifyMobile']);
    Route::post('resend-verify-code', [UserController::class, 'resendVerificationCode']);
    Route::post('login', [UserController::class, 'loginUser']);
});

Route::prefix('marketplace')->group(function () {
    Route::post('register', [MarketplaceController::class, 'registerMarketplace']);
    Route::post('reset-password', [MarketplaceController::class, 'resetMarketplacePassword']);
    Route::post('forget-password', [MarketplaceController::class, 'forgetMarketplacePassword']);
    Route::post('verify-mobile', [MarketplaceController::class, 'verifyMobile']);
    Route::post('resend-verify-code', [MarketplaceController::class, 'setNewVerifyCodeAndSendToUser']);
    Route::post('login', [MarketplaceController::class, 'loginMarketplace']);
});

Route::middleware(['auth:sanctum', EnsureMobileIsVerified::class])->group(function () {
    Route::get('marketplaces', [MarketplaceController::class, 'index']);

    Route::prefix('user')->group(function () {
        Route::post('reset-password', [UserController::class, 'resetUserPassword']);
        Route::delete('logout', [UserController::class, 'logoutUser']);
        Route::post('set-location', [UserController::class, 'setLocation']);
        Route::post('set-rate-and-review', [UserController::class, 'setRateAndReview']);
    });
});

Route::middleware(['auth:sanctum', EnsureMarketplaceIsActive::class])->prefix('marketplace')->group(function () {
    Route::delete('logout', [MarketplaceController::class, 'logoutMarketplace']);
    Route::post('set-description', [MarketplaceController::class, 'setDescription']);
    Route::post('set-location', [MarketplaceController::class, 'setLocation']);

    Route::prefix('products')->group(function () {
        Route::post('add-new-product', [ProductController::class, 'createProduct']);
        Route::patch('update-product', [ProductController::class, 'updateProduct']);
        Route::delete('delete-product/{id}', [ProductController::class, 'deleteProduct']);
    });
});

Route::get('get-all-products', [ProductController::class, 'getAllProduct']);
