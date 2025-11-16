<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Resources\OrderDto;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logout-other-devices', [AuthController::class, 'logoutFromOtherDevices']);
    Route::get('/user', [AuthController::class, 'user']);
});





Route::get('/all-services', function () {});
//get banner ads (if for home page is true then return only for home page, otherwise return all)
Route::get('/all-banner-ads', function () {
    request()->validate([
        'for_home_page' => 'boolean',
    ]);
});
Route::get('/all-offers', function () {});
Route::get('/all-articles', function () {});
Route::get('/user-orders', function () {
    request()->validate([
        'status' => 'nullable|in:pending,confirmed,completed,cancelled',
    ]);
});

//get user order by id
Route::get('/user-order/{order}', function () {});

//update user
Route::put('/user-update/{user}', function () {
    request()->validate([
        'full_name' => 'string|max:255',
        'email' => 'email|unique:mobile_users,email',
        'mobile_number' => 'string|max:255',
        'address' => 'string|max:255',
        'avatar' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);
});


//user login 
Route::post('/user-login', function () {});
//user logout 
Route::post('/user-logout', function () {});
//user delete account 
Route::post('/user-delete-account', function () {});
//user confirm OTP
Route::post('/user-confirm-otp', function () {});


//create or update address
Route::post('/user-address/{address}', function () {
    request()->validate([
        'id' => 'nullable|exists:location_addresses,id',
        'address_type' => 'in:home,work,other',
        'address' => 'string|max:255',
        'full_address' => 'string|max:255',
    ]);
});

Route::post('/set-order', function () {
    request()->validate([
        'service_id' => 'required|exists:services,id',
        'reserve_datetime' => 'required|date_format:Y-m-d H:i:s',
        'address_id' => 'required|exists:location_addresses,id',
        'images' => 'array',
        'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'payment_method' => 'required|in:cash,card', //.....

    ]);

    //order must be returned with resource 
    // return response()->json(new OrderDto($order));
});

//cancel order
Route::post('/cancel-order/{order}', function () {});
