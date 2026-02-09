<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Payments\Gateways\MidtransSnapGateway;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentMidtransController extends Controller
{
    public function __construct(
        private readonly MidtransSnapGateway $gateway
    ) {}

    /**
     * Initiate Midtrans Snap payment for an order.
     *
     * POST /api/payments/midtrans/initiate
     */
    public function initiate(Request $request): JsonResponse
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);

        $order = Order::with('mobileUser')->findOrFail($request->input('order_id'));

        if ($order->mobile_user_id !== request()->user('customer')->id) {
            abort(403);
        }

        $result = $this->gateway->createPaymentIntent($order);
        if (! $result->success) {

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
     * Handle customer return from Midtrans Snap (Finish URL).
     *
     * GET /api/payments/midtrans/return
     */
    public function handleReturn(Request $request): JsonResponse
    {
        $result = $this->gateway->handleRedirectReturn($request);

        return response()->json([
            'status' => $result->status->value,
            'order_id' => $result->orderId,
            'payment_reference' => $result->paymentReference,
            'message' => $result->message,
        ]);
    }

    /**
     * Handle Midtrans HTTP notification (webhook).
     * Configure this URL in Midtrans Dashboard: Settings > Configuration > Payment Notification URL.
     *
     * POST /api/payments/midtrans/webhook/notify
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        $result = $this->gateway->handleWebhook($request);

        if (! $result->verified) {
            Log::warning('Midtrans: Unverified webhook received', [
                'payload' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $result->message,
            ], 400);
        }

        // Midtrans: respond with 2xx so they do not retry
        return response()->json(['ok' => true], 200);
    }
}
