<?php

namespace App\Contracts;

use Spatie\LaravelData\Data;

class OrderItemRequestDto extends Data
{
    public function __construct(
        public int $service_id,
        public int $number_of_workers,
    ) {}
}
