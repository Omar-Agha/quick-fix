<?php

use App\Http\Controllers\Dashboard\ArticleCrudController;
use App\Http\Controllers\Dashboard\BannerAdCrudController;
use App\Http\Controllers\Dashboard\CompanyCrudController;
use App\Http\Controllers\Dashboard\CouponCrudController;
use App\Http\Controllers\Dashboard\OfferCrudController;
use App\Http\Controllers\Dashboard\OrderCrudController;
use App\Http\Controllers\Dashboard\ServiceController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', []);
})->name('home');



// Route::middleware(['auth', 'verified'])->group(function () {

require __DIR__ . '/settings.php';
