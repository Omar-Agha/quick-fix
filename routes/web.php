<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ServiceController;
use App\Models\Offer;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    $offers = Offer::query()
        ->where('is_active', true)
        ->whereNotNull('published_at')
        ->orderByDesc('published_at')
        ->get()
        ->map(fn (Offer $offer): array => [
            'id' => $offer->id,
            'name' => $offer->name,
            'image' => $offer->image,
        ]);

    return Inertia::render('Welcome', [
        'offers' => $offers,
    ]);
})->name('home');

Route::get('/about-us', function () {
    return Inertia::render('About', []);
})->name('about');

Route::get('/blogs', [BlogController::class, 'index'])->name('blogs.index');
Route::get('/blogs/{article}', [BlogController::class, 'show'])->name('blogs.show');

Route::get('/services', [ServiceController::class, 'index'])->name('services.index');

Route::get('/contact', [ContactController::class, 'create'])->name('contact.create');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// Route::middleware(['auth', 'verified'])->group(function () {

require __DIR__.'/settings.php';
