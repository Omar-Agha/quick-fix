<?php

namespace App\Enums;

enum OrderStatus: int
{
    case PAYMENT_PENDING = 1;
    case PAYMENT_SUCCESS = 2;
    case PAYMENT_FAILED = 3;
}
