<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Payments\Contracts\PaymentGateway;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct(
        private readonly PaymentGateway $paymentGateway
    ) {}

    /**
     * Initiate payment for an order.
     * 
     * POST /api/payments/initiate
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function initiate(Request $request): JsonResponse
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);

        $order = Order::findOrFail($request->input('order_id'));

        // TODO: Add authorization check - ensure user owns this order
        // Example: if ($order->mobile_user_id !== auth()->id()) { abort(403); }

        // TODO: Validate order is in a state that allows payment
        // Example: if ($order->status !== OrderStatus::PAYMENT_PENDING) { ... }

        $result = $this->paymentGateway->createPaymentIntent($order);

        if (!$result->success) {
            return response()->json([
                'success' => false,
                'message' => $result->errorMessage,
            ], 400);
        }

        return response()->json([
            'success' => true,
            'redirect_url' => $result->redirectUrl,
            'payment_reference' => $result->paymentReference,
        ]);
    }

    /**
     * Handle customer return from payment gateway.
     * 
     * GET /api/payments/dana/return
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function handleReturn(Request $request): JsonResponse
    {
        $result = $this->paymentGateway->handleRedirectReturn($request);

        // Return JSON response for Flutter app
        // You can also redirect to app deep link if needed
        return response()->json([
            'status' => $result->status->value,
            'order_id' => $result->orderId,
            'payment_reference' => $result->paymentReference,
            'message' => $result->message,
        ]);
    }

    /**
     * Handle payment gateway webhook.
     * 
     * POST /api/payments/dana/webhook/finish-notify
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        $result = $this->paymentGateway->handleWebhook($request);

        if (!$result->verified) {
            Log::warning('DANA: Unverified webhook received', [
                'payload' => $request->all(),
            ]);

            // Return 400 to indicate webhook was not processed
            return response()->json([
                'success' => false,
                'message' => $result->message,
            ], 400);
        }

        // Return success response as expected by DANA
        // DANA expects HTTP 200 with specific JSON format
        return response()->json([
            'responseCode' => '0000',
            'responseMessage' => 'SUCCESS',
        ], 200);
    }
}
