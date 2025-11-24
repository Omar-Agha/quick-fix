<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderService
{
    public function createOrder(array $data): Order
    {
        $order = Order::create([
            'service_id' => $data['service_id'],
            'reserve_datetime' => $data['reserve_datetime'],
            'address_id' => $data['address_id'],
            'images' => $data['images'] ?? null,
            'payment_method' => $data['payment_method'],
            'mobile_user_id' => Auth::id(),
        ]);

        return $order;
    }

    public function cancelOrder(Order $order): bool
    {
        if ($order->mobile_user_id !== Auth::id()) {
            return false;
        }

        return $order->update(['status' => 'cancelled']);
    }
}
