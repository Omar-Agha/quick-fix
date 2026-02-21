<?php

use App\Http\Controllers\Api\MobileAppApiController;
use App\Http\Controllers\Api\MobileUserApiController;
use App\Http\Controllers\Api\OrderApiController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\PaymentMidtransController;
use App\Http\Controllers\Auth\AuthController;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Route;

// swagger
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
Route::prefix('user')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [MobileUserApiController::class, 'getUser']);
    Route::post('/', [MobileUserApiController::class, 'updateUserProfile']);
    Route::delete('/', [MobileUserApiController::class, 'deleteUserAccount']);
    Route::post('/change-password', [MobileUserApiController::class, 'changePassword']);
    Route::post('/change-phone-number/request', [MobileUserApiController::class, 'requestChangePhoneNumber']);
    Route::post('/change-phone-number/verify', [MobileUserApiController::class, 'verifyOtpForChangePhoneNumber']);
});
Route::get('supported-phone-codes', function () {
    $phoneCodes = explode(',', env('SUPPORTED_PHONE_CODES'));

    return response()->json($phoneCodes);
});

Route::get('/all-services', [MobileAppApiController::class, 'getAllActiveServices']);
Route::get('/all-banner-ads', [MobileAppApiController::class, 'getAllActiveBannerAds']);
Route::get('/all-offers', [MobileAppApiController::class, 'getAllActiveOffers']);
Route::get('/all-articles', [MobileAppApiController::class, 'getAllActiveArticles']);

Route::get('/user-address', [MobileUserApiController::class, 'getUserAddresses'])->middleware(['auth:customer']);
Route::post('/user-address', [MobileUserApiController::class, 'createOrUpdateAddress'])->middleware(['auth:customer']);
Route::delete('/user-address/{address}', [MobileUserApiController::class, 'deleteAddress'])->middleware(['auth:customer']);

Route::post('/set-order', [OrderApiController::class, 'setOrder'])->middleware(['auth:customer']);
Route::post('/calculate-service-fees', [OrderApiController::class, 'calculateServiceFees'])->middleware(['auth:customer']);
Route::get('/verify-coupon/{coupon}', [OrderApiController::class, 'verifyCoupon'])->middleware(['auth:customer']);

Route::post('/cancel-order/{order}', [OrderApiController::class, 'cancelOrder'])->middleware(['auth:customer']);

// Not implemented yet
Route::get('/user-orders', [MobileUserApiController::class, 'getUserOrders'])->middleware(['auth:customer']);

// get user order by id
Route::get('/user-order/{order}', [MobileUserApiController::class, 'getUserOrderById']);

// update user
Route::put('/user-update/{user}', [MobileUserApiController::class, 'updateUserProfile']);

// Payment routes
Route::prefix('payments')->group(function () {
    // Initiate payment (requires authentication)
    Route::post('/initiate', [PaymentController::class, 'initiate'])
        ->middleware(['auth:customer']);

    // DANA specific routes
    Route::prefix('dana')->group(function () {
        // Handle customer return from DANA hosted checkout
        Route::get('/return', [PaymentController::class, 'handleReturn']);

        // Webhook endpoint for DANA Finish Notify
        // Note: This endpoint should be publicly accessible (no auth middleware)
        // DANA will call this endpoint directly
        Route::post('/webhook/finish-notify', [PaymentController::class, 'handleWebhook']);
    });

    // Midtrans Snap routes
    Route::prefix('midtrans')->group(function () {
        Route::post('/initiate', [PaymentMidtransController::class, 'initiate'])
            ->middleware(['auth:customer']);
        Route::get('/return', [PaymentMidtransController::class, 'handleReturn']);
        Route::post('/webhook/notify', [PaymentMidtransController::class, 'handleWebhook']);
    });

    Route::get('/', function () {
        return Payment::all();
    });
});

Route::get('get-gg/{order_id}', function ($order_id) {
    $payments = Payment::where('order_id', $order_id)->get();
    return response()->json([
        'payments' => $payments,
        'order' => Order::find($order_id)
    ]);
});
