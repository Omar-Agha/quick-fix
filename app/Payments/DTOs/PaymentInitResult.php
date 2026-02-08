<?php

namespace App\Payments\DTOs;

class PaymentInitResult
{
    public function __construct(
        public readonly bool $success,
        public readonly ?string $redirectUrl = null,
        public readonly ?string $paymentReference = null,
        public readonly ?string $errorMessage = null,
        public readonly ?array $rawResponse = null,
    ) {}

    public static function success(string $redirectUrl, string $paymentReference, ?array $rawResponse = null): self
    {
        return new self(
            success: true,
            redirectUrl: $redirectUrl,
            paymentReference: $paymentReference,
            rawResponse: $rawResponse,
        );
    }

    public static function failure(string $errorMessage, ?array $rawResponse = null): self
    {
        return new self(
            success: false,
            errorMessage: $errorMessage,
            rawResponse: $rawResponse,
        );
    }
}
