<?php

use App\Http\Controllers\Front\AuthController;
use App\Http\Controllers\Front\CartController;
use App\Http\Controllers\Front\CategoryController;
use App\Http\Controllers\Front\CheckoutController;
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
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('front.auth.login.show');
        Route::post('/login', [AuthController::class, 'login'])->name('front.auth.login');
        Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('front.auth.register.show');
        Route::post('/register', [AuthController::class, 'register'])->name('front.auth.register');
        Route::post('/logout', [AuthController::class, 'logout'])->name('front.auth.logout');
        Route::get('/category/{slug}', [CategoryController::class, 'show'])->name('front.category.show');
        Route::get('/cart', [CartController::class, 'show'])->name('front.cart.show');
        Route::get('/checkout', [CheckoutController::class, 'index'])->name('front.checkout.index');
        Route::post('/checkout/guest', [CheckoutController::class, 'submitGuestStep'])->name('front.checkout.guest');
        Route::post('/checkout/customer-address', [CheckoutController::class, 'setCustomerShippingAddress'])->name('front.checkout.customer_address')->middleware('auth:web');
        Route::post('/checkout/add-customer-address', [CheckoutController::class, 'addCustomerShippingAddress'])->name('front.checkout.add_customer_address')->middleware('auth:web');
        Route::post('/checkout/shipping-methods', [CheckoutController::class, 'getShippingMethods'])->name('front.checkout.shipping_methods');
        Route::post('/checkout/set-shipping-method', [CheckoutController::class, 'setShippingMethod'])->name('front.checkout.set_shipping_method');
        Route::post('/checkout/payment-methods', [CheckoutController::class, 'getPaymentMethods'])->name('front.checkout.payment_methods');
        Route::post('/checkout/set-payment-method', [CheckoutController::class, 'setPaymentMethod'])->name('front.checkout.set_payment_method');
        Route::post('/checkout/confirm', [CheckoutController::class, 'confirmOrder'])->name('front.checkout.confirm');
        Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('front.checkout.success');
        Route::post('/cart/add', [CartController::class, 'add'])->name('front.cart.add');
        Route::put('/cart', [CartController::class, 'update'])->name('front.cart.update');
        Route::delete('/cart', [CartController::class, 'destroy'])->name('front.cart.destroy');
        Route::get('/product/{slug}', [ProductController::class, 'show'])->name('front.product.show');
        Route::get('/{slug}', [PageController::class, 'show'])->name('front.page.show');
    });
