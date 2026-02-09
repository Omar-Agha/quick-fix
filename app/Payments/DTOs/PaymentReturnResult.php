<?php

namespace App\Payments\DTOs;

use App\Enums\PaymentStatus;

// enum PaymentReturnStatus: string
// {
//     case SUCCESS = 'success';
//     case FAILED = 'failed';
//     case CANCELLED = 'cancelled';
//     case EXPIRED = 'expired';
//     case PENDING = 'pending';
// }

class PaymentReturnResult
{
    public function __construct(
        public readonly PaymentStatus $status,
        public readonly ?string $orderId = null,
        public readonly ?string $paymentReference = null,
        public readonly ?string $message = null,
        public readonly ?array $rawData = null,
    ) {}

    public static function success(string $orderId, ?string $paymentReference = null, ?array $rawData = null): self
    {
        return new self(
            status: PaymentStatus::COMPLETED,
            orderId: $orderId,
            paymentReference: $paymentReference,
            message: 'Payment completed successfully',
            rawData: $rawData,
        );
    }

    public static function failed(string $orderId, ?string $message = null, ?array $rawData = null): self
    {
        return new self(
            status: PaymentStatus::FAILED,
            orderId: $orderId,
            message: $message ?? 'Payment failed',
            rawData: $rawData,
        );
    }

    public static function cancelled(string $orderId, ?string $message = null, ?array $rawData = null): self
    {
        return new self(
            status: PaymentStatus::CANCELLED,
            orderId: $orderId,
            message: $message ?? 'Payment was cancelled',
            rawData: $rawData,
        );
    }

    public static function expired(string $orderId, ?string $message = null, ?array $rawData = null): self
    {
        return new self(
            status: PaymentStatus::EXPIRED,
            orderId: $orderId,
            message: $message ?? 'Payment expired',
            rawData: $rawData,
        );
    }

    public static function pending(string $orderId, ?string $message = null, ?array $rawData = null): self
    {
        return new self(
            status: PaymentStatus::PENDING,
            orderId: $orderId,
            message: $message ?? 'Payment is pending verification',
            rawData: $rawData,
        );
    }
}
