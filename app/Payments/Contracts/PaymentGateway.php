<?php

namespace App\Payments\Contracts;

use App\Models\Order;
use App\Payments\DTOs\PaymentInitResult;
use App\Payments\DTOs\PaymentReturnResult;
use App\Payments\DTOs\WebhookResult;
use Illuminate\Http\Request;

interface PaymentGateway
{
    /**
     * Create a payment intent for the given order.
     * Returns a result containing the redirect URL and payment reference.
     */
    public function createPaymentIntent(Order $order): PaymentInitResult;

    /**
     * Handle the customer's return from the payment gateway.
     * Processes redirect parameters and determines payment status.
     */
    public function handleRedirectReturn(Request $request): PaymentReturnResult;

    /**
     * Handle server-to-server webhook notification from the payment gateway.
     * Verifies authenticity and processes the payment status update.
     */
    public function handleWebhook(Request $request): WebhookResult;
}
