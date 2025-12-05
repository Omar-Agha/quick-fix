<?php

use App\Http\Controllers\Dashboard\ArticleCrudController;
use App\Http\Controllers\Dashboard\BannerAdCrudController;
use App\Http\Controllers\Dashboard\CouponCrudController;
use App\Http\Controllers\Dashboard\OfferCrudController;
use App\Http\Controllers\Dashboard\OrderCrudController;
use App\Http\Controllers\Dashboard\ServiceController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');



// Route::middleware(['auth', 'verified'])->group(function () {
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::resource('services', ServiceController::class);
    Route::resource('banner-ads', BannerAdCrudController::class);
    Route::resource('offers', OfferCrudController::class);
    Route::resource('orders', OrderCrudController::class);
    Route::resource('coupons', CouponCrudController::class);
    Route::resource('articles', ArticleCrudController::class);
});

require __DIR__ . '/settings.php';
