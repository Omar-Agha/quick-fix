<?php

use App\Http\Controllers\Api\MobileAppApiController;
use App\Http\Controllers\Api\MobileUserApiController;
use App\Http\Controllers\Api\OrderApiController;
use App\Http\Controllers\Api\ServicesApiController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Dashboard\ServiceController;
use App\Http\Resources\OrderDto;
use App\Models\Service;
use Illuminate\Support\Facades\Route;


//swagger 
Route::redirect('/', 'documentation');


Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logout-other-devices', [AuthController::class, 'logoutFromOtherDevices']);
    Route::get('/user', [AuthController::class, 'user']);
});

// Route::resource('services', ServiceController::class);
Route::get('gg', function () {
    return Service::all();
});


Route::get('/all-services', [MobileAppApiController::class, 'getAllServices']);


Route::get('/all-banner-ads', [MobileAppApiController::class, 'getAllBannerAds']);

Route::get('/all-offers', [MobileAppApiController::class, 'getAllOffers']);
Route::get('/all-articles', [MobileAppApiController::class, 'getAllArticles']);
Route::get('/user-orders', [MobileUserApiController::class, 'getUserOrders']);

//get user order by id
Route::get('/user-order/{order}', [MobileUserApiController::class, 'getUserOrderById']);

//update user
Route::put('/user-update/{user}', [MobileUserApiController::class, 'updateUserProfile']);



//user login 
Route::post('/user-login', function () {});
//user logout 
Route::post('/user-logout', function () {});
//user delete account 
Route::post('/user-delete-account', [MobileUserApiController::class, 'deleteUserAccount']);
//user confirm OTP
Route::post('/user-confirm-otp', function () {});


//create or update address
Route::post('/user-address/{address}', [MobileUserApiController::class, 'createOrUpdateAddress']);
Route::delete('/user-address/{address}', [MobileUserApiController::class, 'deleteAddress']);

Route::post('/set-order', [OrderApiController::class, 'setOrder']);


//cancel order
Route::post('/cancel-order/{order}', [OrderApiController::class, 'cancelOrder']);
