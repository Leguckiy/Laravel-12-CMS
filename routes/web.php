<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CurrencyController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\StockStatusController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UserGroupController;

Route::prefix('admin')->group(function () {
    Route::get('/', [LoginController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [LoginController::class, 'login'])->name('admin.login.submit');

    Route::middleware('auth.admin')->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');

        Route::get('/logout', [LoginController::class, 'logout'])->name('admin.logout');

        // ACL convention for admin routes:
        // - Route names must follow: admin.{module}.{action}
        // - Ability inference (AdminPermissionMiddleware::detectAbility):
        //   *.index, *.show => 'view'; everything else => 'edit'
        // - Module is taken from the second segment (e.g., admin.user.edit => module 'user')
        // Keep names consistent to ensure permissions resolve correctly.
        Route::middleware('admin.permission')->group(function () {
            Route::resource('user', UserController::class)->names('admin.user');
            Route::resource('user_group', UserGroupController::class)->names('admin.user_group');
            Route::resource('language', LanguageController::class)->names('admin.language');
            Route::resource('currency', CurrencyController::class)->names('admin.currency');
            Route::resource('stock_status', StockStatusController::class)->names('admin.stock_status');
        });

        // Fallback for unknown admin routes (404 within admin area)
        Route::fallback(function () {
            return response()->view('admin.not_found', [], 404);
        });
    });
});
