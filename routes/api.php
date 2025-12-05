<?php

use App\Http\Controllers\Api\MobileAppApiController;
use App\Http\Controllers\Api\MobileUserApiController;
use App\Http\Controllers\Api\OrderApiController;
use App\Http\Controllers\Api\ServicesApiController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Dashboard\CouponCrudController;
use App\Http\Controllers\Dashboard\OrderCrudController;
use App\Http\Controllers\Dashboard\ServiceController;
use App\Http\Resources\Dashboard\OrderDto;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Service;
use Illuminate\Support\Facades\Route;


//swagger 
Route::redirect('/', 'documentation');

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('/resend-otp', [AuthController::class, 'resendOtp']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/logout-other-devices', [AuthController::class, 'logoutFromOtherDevices']);
        Route::get('/user', [AuthController::class, 'user']);
    });
});


// Route::resource('services', ServiceController::class);
// Route::resource('orders', OrderCrudController::class);
// Route::resource('coupons', CouponCrudController::class);
Route::get('gg', function () {
    $g =  Coupon::with('orders')->withCount(['orders']);
});


Route::get('/all-services', [MobileAppApiController::class, 'getAllServices']);
Route::get('/all-banner-ads', [MobileAppApiController::class, 'getAllBannerAds']);

Route::get('/user-address', [MobileUserApiController::class, 'getUserAddresses'])->middleware(['auth:customer']);
Route::post('/user-address', [MobileUserApiController::class, 'createOrUpdateAddress'])->middleware(['auth:customer']);
Route::delete('/user-address/{address}', [MobileUserApiController::class, 'deleteAddress'])->middleware(['auth:customer']);



Route::post('/set-order', [OrderApiController::class, 'setOrder'])->middleware(['auth:customer']);
Route::post('/calculate-service-fees', [OrderApiController::class, 'calculateServiceFees'])->middleware(['auth:customer']);
Route::get('/verify-coupon/{coupon}', [OrderApiController::class, 'verifyCoupon'])->middleware(['auth:customer']);
Route::post('/cancel-order/{order}', [OrderApiController::class, 'cancelOrder'])->middleware(['auth:customer']);

// Not implemented yet
Route::get('/all-offers', [MobileAppApiController::class, 'getAllOffers']);
Route::get('/all-articles', [MobileAppApiController::class, 'getAllArticles']);
Route::get('/user-orders', [MobileUserApiController::class, 'getUserOrders']);

//get user order by id
Route::get('/user-order/{order}', [MobileUserApiController::class, 'getUserOrderById']);

//update user
Route::put('/user-update/{user}', [MobileUserApiController::class, 'updateUserProfile']);
