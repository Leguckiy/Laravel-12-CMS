<?php

use App\Http\Controllers\Front\CartController;
use App\Http\Controllers\Front\CategoryController;
use App\Http\Controllers\Front\CurrencyController;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\PageController;
use App\Http\Controllers\Front\ProductController;
use Illuminate\Support\Facades\Route;

Route::middleware('front.locale')
    ->prefix('{lang}')
    ->where(['lang' => '[a-z]{2}'])
    ->group(function () {
        Route::get('/', [HomeController::class, 'index'])->name('front.home');
        Route::post('/set-currency', [CurrencyController::class, 'set'])->name('front.currency.set');
        Route::get('/category/{slug}', [CategoryController::class, 'show'])->name('front.category.show');
        Route::get('/cart', [CartController::class, 'show'])->name('front.cart.show');
        Route::post('/cart/add', [CartController::class, 'add'])->name('front.cart.add');
        Route::put('/cart', [CartController::class, 'update'])->name('front.cart.update');
        Route::delete('/cart', [CartController::class, 'destroy'])->name('front.cart.destroy');
        Route::get('/product/{slug}', [ProductController::class, 'show'])->name('front.product.show');
        Route::get('/{slug}', [PageController::class, 'show'])->name('front.page.show');
    });
