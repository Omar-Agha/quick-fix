<?php

namespace App\Payments\DTOs;

enum WebhookStatus: string
{
    case SUCCESS = 'success';
    case FAILED = 'failed';
    case PENDING = 'pending';
    case EXPIRED = 'expired';
}
