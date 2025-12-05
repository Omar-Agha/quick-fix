<?php

namespace App\Core;

use App\Contracts\OrderItemRequestDto;
use App\Models\Coupon;
use App\Models\MobileUser;
use App\Models\Service;
use Illuminate\Support\Collection;
use Spatie\LaravelData\DataCollection;

class FeeCalculator
{
    /**
     * @param Collection<OrderItemRequestDto> $order_items
     */
    public function calculateServiceFees($order_items, string $coupons = null, MobileUser $mobileUser)
    {
        $service_ids = $order_items->pluck('service_id')->unique()->toArray();
        $services = Service::whereIn('id', $service_ids)->get();
        $items = [];
        foreach ($order_items as $item) {
            $service = $services->firstWhere('id', $item->service_id);
            $items[] = [
                'service_id' => $item->service_id,
                'service_name' => $service->name,
                'number_of_workers' => $item->number_of_workers,
                'cost_per_worker' => $service->price,
                'cost' => $service->price * $item->number_of_workers,
            ];
        }
        $total_cost = collect($items)->sum('cost');
        $discount = $this->calculateDiscount($coupons, $mobileUser, $total_cost);
        $total_cost = $total_cost - $discount['discount'];
        $fees = $total_cost * 0.03;
        $pay_at_cashier = $total_cost + $fees;
        return [
            'fees' => $fees,
            'total_cost' => $total_cost,
            'discount' => $discount,
            'pay_at_cashier' => $pay_at_cashier,
            'items' => $items,
            'coupon_id' => $discount['coupon_id'],
        ];
    }
    public function calculateDiscount(string $code = null, MobileUser $mobileUser, int $amount)
    {
        if (!$code) {
            return [
                'discount' => 0,
                'coupon_id' => null,
            ];
        }
        $is_valid = $this->verifyCoupon($code, $mobileUser,);
        if (!$is_valid) {
            return [
                'discount' => 0,
                'coupon_id' => null,
            ];
        }
        $coupon = Coupon::whereCode($code)->first();
        $discount = $amount * $coupon->discount_percentage / 100;
        return [
            'discount' => $discount,
            'coupon_id' => $coupon->id,
        ];
    }

    public function verifyCoupon(string $coupon, MobileUser $user)
    {

        $valid_coupon = Coupon::whereCode($coupon)->exists();
        if (!$valid_coupon) {
            throw new \Exception("invalid coupon");
        }
        $user_coupons = $user->coupons()->where('code', $coupon)->exists();

        if ($user_coupons) {
            throw new \Exception("coupon already used");
        }
        return true;
    }
}
