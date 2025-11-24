<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderDto;
use App\Models\Order;
use App\Services\OrderService;

class OrderApiController extends Controller
{
    public function __construct(
        private OrderService $orderService
    ) {}

    public function setOrder()
    {
        $validated = request()->validate([
            'service_id' => 'required|exists:services,id',
            'reserve_datetime' => 'required|date_format:Y-m-d H:i:s',
            'address_id' => 'required|exists:location_addresses,id',
            'images' => 'array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'payment_method' => 'required|in:cash,card',
        ]);

        $order = $this->orderService->createOrder($validated);

        return response()->json(new OrderDto($order));
    }

    public function cancelOrder(Order $order)
    {
        $cancelled = $this->orderService->cancelOrder($order);

        if (! $cancelled) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        return response()->json([
            'message' => 'Order cancelled successfully',
        ]);
    }
}
