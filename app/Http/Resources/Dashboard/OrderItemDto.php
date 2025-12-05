<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemDto extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'service_id' => $this->service_id,
            'service_name' => $this->service->name,
            'service_image' => $this->service->image,
            'number_of_workers' => $this->number_of_workers,
            'cost' => $this->cost,
        ];
    }
}
