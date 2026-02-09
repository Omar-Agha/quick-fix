<?php

namespace App\Payments\DTOs;


class WebhookResult
{
    public function __construct(
        public readonly bool $verified,
        public readonly WebhookStatus $status,
        public readonly ?string $orderId = null,
        public readonly ?string $paymentReference = null,
        public readonly ?string $transactionId = null,
        public readonly ?string $message = null,
        public readonly ?array $rawPayload = null,
    ) {}

    public static function verified(
        WebhookStatus $status,
        string $orderId,
        ?string $paymentReference = null,
        ?string $transactionId = null,
        ?string $message = null,
        ?array $rawPayload = null
    ): self {
        return new self(
            verified: true,
            status: $status,
            orderId: $orderId,
            paymentReference: $paymentReference,
            transactionId: $transactionId,
            message: $message,
            rawPayload: $rawPayload,
        );
    }

    public static function unverified(?string $message = null, ?array $rawPayload = null): self
    {
        return new self(
            verified: false,
            status: WebhookStatus::PENDING,
            message: $message ?? 'Webhook signature verification failed',
            rawPayload: $rawPayload,
        );
    }
}
