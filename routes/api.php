<?php

use App\Http\Controllers\Language\SetLanguage;
use App\Http\Controllers\Marketplace\MarketplaceController;
use App\Http\Controllers\User\UserController;
use App\Http\Middleware\EnsureMarketplaceIsActive;
use App\Http\Middleware\EnsureMobileIsVerified;
use Illuminate\Support\Facades\Route;

Route::post('/set-language', [SetLanguage::class, 'setLanguage']);

// Public Routes for User with 'user' prefix
Route::prefix('user')->group(function () {
    Route::post('register', [UserController::class, 'registerUser']);
    Route::post('forget-password', [UserController::class, 'forgetUserPassword']);
    Route::post('verify-mobile', [UserController::class, 'verifyMobile']);
    Route::post('resend-verify-code', [UserController::class, 'resendVerificationCode']);
    Route::post('login', [UserController::class, 'loginUser']);
});

// Public Routes for Marketplace with 'marketplace' prefix
Route::prefix('marketplace')->group(function () {
    Route::post('register', [MarketplaceController::class, 'registerMarketplace']);
    Route::post('reset-password', [MarketplaceController::class, 'resetMarketplacePassword']);
    Route::post('verify-mobile', [MarketplaceController::class, 'verifyMobile']);
    Route::post('resend-verify-code', [MarketplaceController::class, 'setNewVerifyCodeAndSendToUser']);
    Route::post('login', [MarketplaceController::class, 'loginMarketplace']);
});

// Protected Routes with 'auth:sanctum' and 'EnsureMobileIsVerified' middlewares
Route::middleware(['auth:sanctum', EnsureMobileIsVerified::class])->group(function () {
    // Routes available to authenticated users
    Route::get('marketplaces', [MarketplaceController::class, 'index']);

    // User Routes with 'user' prefix
    Route::prefix('user')->group(function () {
        Route::post('reset-password', [UserController::class, 'resetUserPassword']);
        Route::delete('logout', [UserController::class, 'logoutUser']);
        Route::post('set-location', [UserController::class, 'setLocation']);
        Route::post('set-rate-and-review', [UserController::class, 'setRateAndReview']);
    });
});

// Protected Routes with 'auth:sanctum' and 'EnsureMarketplaceIsActive' middlewares
Route::middleware(['auth:sanctum', EnsureMarketplaceIsActive::class])->prefix('marketplace')->group(function () {
    // Marketplace Routes with 'marketplace' prefix
    Route::delete('logout', [MarketplaceController::class, 'logoutMarketplace']);
    Route::post('set-description', [MarketplaceController::class, 'setDescription']);
    Route::get('add-new-product', [MarketplaceController::class, 'addProduct']);
});
