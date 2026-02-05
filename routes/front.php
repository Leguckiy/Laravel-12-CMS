<?php

use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\PageController;
use Illuminate\Support\Facades\Route;

Route::middleware('front.locale')
    ->prefix('{lang}')
    ->where(['lang' => '[a-z]{2}'])
    ->group(function () {
        Route::get('/', [HomeController::class, 'index'])->name('front.home');
        Route::get('/{slug}', [PageController::class, 'show'])->name('front.page.show');
    });
