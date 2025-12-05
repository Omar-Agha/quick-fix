<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDto extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'user' => [
                'phone_number' => $this->mobileUser->phone_number,
                'avatar' => $this->mobileUser->avatar,
            ],

            'location_address' => [
                'address' => $this->locationAddress->address,
                'full_address' => $this->locationAddress->full_address,
            ],
            'images' => $this->files->map(fn($file) => asset('storage/' . $file->path)),
            'price_summary' => [
                'total_cost' => $this->total_cost,
                'fees' => $this->fees,
                'pay_at_cashier' => $this->pay_at_cashier,
                'discount' => $this->discount
            ],
            'order_items' => OrderItemDto::collection($this->orderItems),
            'is_direct_service' => $this->is_direct_service,
            'reserve_datetime' => $this->reserve_datetime,
            'description' => $this->description,

            'status' => $this->status,
            'created_at' => $this->created_at,


        ];
    }
}
