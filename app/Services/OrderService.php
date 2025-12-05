<?php

namespace App\Services;

use App\Contracts\OrderItemRequestDto;
use App\Core\FeeCalculator;
use App\Enums\OrderStatus;
use App\Models\MobileUser;
use App\Models\Order;
use App\Models\Service;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Spatie\LaravelData\DataCollection;

class OrderService
{
    public function __construct(
        private FeeCalculator $feeCalculator
    ) {}


    /**
     * @param Collection<OrderItemRequestDto> $order_items
     */
    public function createOrder(MobileUser $mobileUser, Collection $order_items, array $images): Order
    {

        $service_ids = $order_items->pluck('service_id')->unique()->toArray();
        $services = Service::whereIn('id', $service_ids)->get();
        $contains_inactive_service = collect($services)->containsOneItem(fn($service) => $service->is_active == false);
        if ($contains_inactive_service) {
            throw new \Exception('One or more services are not active');
        }

        $pricing_result = $this->feeCalculator->calculateServiceFees($order_items, request('coupon'), $mobileUser);

        $order = Order::create([
            'mobile_user_id' => $mobileUser->id,
            'location_address_id' => request('address_id'),
            'is_direct_service' => request('is_direct_service'),
            'reserve_datetime' => request('reserve_datetime'),
            'payment_method' => request('payment_method'),
            'total_cost' => $pricing_result['total_cost'],
            'fees' => $pricing_result['fees'],
            'pay_at_cashier' => $pricing_result['pay_at_cashier'],
            'status' => OrderStatus::PENDING,
            'description' => request('description'),
            'coupon_id' => $pricing_result['coupon_id'],
        ]);

        foreach ($pricing_result['items'] as $item) {

            $order->orderItems()->create([
                'service_id' => $item['service_id'],
                'number_of_workers' => $item['number_of_workers'],
                'cost' => $item['cost'],
            ]);
        }

        if ($images) {
            foreach ($images as $image) {
                $order->storeFile($image, 'images');
            }
        }

        return $order->load('orderItems', 'orderItems.service', 'locationAddress', 'mobileUser', 'files');
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
