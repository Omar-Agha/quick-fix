<?php

namespace App\Services;

use App\Core\FeeCalculator;
use App\Enums\OrderStatus;
use App\Models\MobileUser;
use App\Models\Order;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;

class OrderService
{
    public function __construct(
        private FeeCalculator $feeCalculator
    ) {}

    public function createOrder(array $data, MobileUser $mobileUser, Service $service, array $images): Order
    {
        if ($service->is_active == false) {
            throw new \Exception('Service is not active');
        }
        $fees = $this->feeCalculator->calculateServiceFees($service, $mobileUser);
        $order = Order::create([
            'service_id' => $service->id,
            'reserve_datetime' => $data['reserve_datetime'],
            'location_address_id' => $data['address_id'],
            'payment_method' => $data['payment_method'],
            'mobile_user_id' => $mobileUser->id,
            'cost' => $service->price,
            'fees' => $fees,
            'status' => OrderStatus::PENDING,
        ]);

        if ($images) {
            foreach ($images as $image) {
                $order->storeFile($image, 'images');
            }
        }

        return $order->load('service', 'locationAddress', 'mobileUser', 'files');
    }

    public function cancelOrder(Order $order, MobileUser $mobileUser)
    {
        if ($order->status == OrderStatus::CANCELLED) {
            throw new \Exception('Order is already cancelled');
        }
        if ($order->status == OrderStatus::COMPLETED) {
            throw new \Exception('Order is already completed');
        }
        if ($order->status == OrderStatus::CONFIRMED) {
            throw new \Exception('Order is already confirmed');
        }
        if ($order->mobile_user_id !== $mobileUser->id) {
            throw new \Exception('Unauthorized, you are not the owner of this order');
        }


        $is_saved = $order->update(['status' => OrderStatus::CANCELLED]);
        if (!$is_saved) {
            throw new \Exception('Failed to cancel order');
        }
        return $order;
    }
}
