<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ServiceController;
use App\Models\Offer;
use App\Models\Service;
use Illuminate\Support\Facades\App;
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
    $services = Service::query()
        ->where('is_active', true)
        ->orderBy('name')
        ->get()
        ->map(fn (Service $service): array => [
            'id' => $service->id,
            'name' => $service->name,
            'description' => $service->description ?? '',
            'image' => $service->image,
            'cost' => $service->cost_per_worker,
        ]);

    return Inertia::render('Welcome', [
        'offers' => $offers,
        'services' => $services,

    ]);
})->name('home');

Route::get('/about-us', function () {
    return Inertia::render('About');
})->name('about');

Route::get('/privacy', function () {
    return Inertia::render('Privacy');
})->name('privacy');

Route::get('/download', function () {
    return Inertia::render('Download');
})->name('download');

Route::get('/blogs', [BlogController::class, 'index'])->name('blogs.index');
Route::get('/blogs/{article}', [BlogController::class, 'show'])->name('blogs.show');

Route::get('/services', [ServiceController::class, 'index'])->name('services.index');

Route::get('/contact', [ContactController::class, 'create'])->name('contact.create');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

Route::get('/locale/{locale}', function (string $locale) {
    $supported = config('app.supported_locales', ['id', 'en']);
    if (! in_array($locale, $supported, true)) {
        abort(404);
    }
    session(['locale' => $locale]);
    App::setLocale($locale);

    return back();
})->name('locale.switch');

// Route::middleware(['auth', 'verified'])->group(function () {

require __DIR__.'/settings.php';
