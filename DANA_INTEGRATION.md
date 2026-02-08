# DANA Payment Gateway Integration

This document describes the DANA Hosted Checkout integration implementation.

## Overview

The integration follows a clean architecture pattern with:
- **Contracts**: `PaymentGateway` interface for extensibility
- **Gateways**: `DanaHostedCheckoutGateway` implementation
- **Helpers**: DANA-specific utilities (Client, Signature, Timestamp)
- **DTOs**: Value objects for payment results

## Architecture

```
app/Payments/
├── Contracts/
│   └── PaymentGateway.php          # Interface for all payment gateways
├── DTOs/
│   ├── PaymentInitResult.php       # Result from payment initiation
│   ├── PaymentReturnResult.php     # Result from customer redirect return
│   └── WebhookResult.php           # Result from webhook processing
├── Dana/
│   ├── DanaClient.php              # HTTP client for DANA API
│   ├── DanaSignature.php           # Signature generation/verification
│   ├── DanaTimestamp.php           # Jakarta GMT+7 timestamp helper
│   └── DanaKeyHelper.php           # Key loading helper
└── Gateways/
    └── DanaHostedCheckoutGateway.php # DANA implementation
```

## Configuration

### Environment Variables

Add these to your `.env` file:

```env
# DANA Payment Gateway Configuration
DANA_BASE_URL=https://api-sandbox.dana.id
DANA_PARTNER_ID=your_partner_id
DANA_MERCHANT_ID=your_merchant_id
DANA_CHANNEL_ID=your_channel_id
DANA_ORIGIN=https://your-app.com

# Private key for signing requests (PEM format)
# Can be direct content or file path (e.g., storage/dana/private_key.pem)
DANA_PRIVATE_KEY="-----BEGIN PRIVATE KEY-----\n...\n-----END PRIVATE KEY-----"

# Public key for verifying webhooks (PEM format)
# Can be direct content or file path (e.g., storage/dana/public_key.pem)
DANA_PUBLIC_KEY="-----BEGIN PUBLIC KEY-----\n...\n-----END PUBLIC KEY-----"

# URLs (optional, defaults to APP_URL + routes)
DANA_RETURN_URL=https://your-app.com/api/payments/dana/return
DANA_NOTIFY_URL=https://your-app.com/api/payments/dana/webhook/finish-notify
```

### Key Storage Options

The integration supports two ways to store keys:

1. **Direct Content** (for small keys or development):
   ```env
   DANA_PRIVATE_KEY="-----BEGIN PRIVATE KEY-----\nMIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQC...\n-----END PRIVATE KEY-----"
   ```

2. **File Path** (recommended for production):
   ```env
   DANA_PRIVATE_KEY=storage/dana/private_key.pem
   ```
   Store the key file in `storage/app/dana/private_key.pem` (ensure it's in `.gitignore`)

## Payment Flow

### 1. Initiate Payment

**Endpoint**: `POST /api/payments/initiate`

**Request**:
```json
{
  "order_id": 123
}
```

**Response**:
```json
{
  "success": true,
  "redirect_url": "https://m.dana.id/...",
  "payment_reference": "MERCHANT_123"
}
```

**Implementation Notes**:
- Generates `partnerReferenceNo` as `{merchantId}_{orderId}` for idempotency
- Calculates total amount: `total_cost + fees - discount`
- Returns `webRedirectUrl` for customer to complete payment

### 2. Customer Return Handler

**Endpoint**: `GET /api/payments/dana/return`

DANA redirects customer here with query parameters:
- `resultCode`: Payment result
- `partnerReferenceNo`: Our reference
- `referenceNo`: DANA transaction reference

**Response**:
```json
{
  "status": "success|failed|cancelled|expired|pending",
  "order_id": "123",
  "payment_reference": "DANA_REF_123",
  "message": "Payment completed successfully"
}
```

**Status Mapping**:
- `SUCCESS` / `00` → `success`
- `CANCEL` / `CANCELLED` → `cancelled`
- `EXPIRED` / `05` → `expired`
- Others → `pending` (wait for webhook)

### 3. Webhook Handler

**Endpoint**: `POST /api/payments/dana/webhook/finish-notify`

**Headers Required**:
- `X-PARTNER-ID`: Partner ID
- `X-TIMESTAMP`: Jakarta GMT+7 timestamp
- `X-SIGNATURE`: Base64 encoded signature

**Signature Verification**:
- Constructs signature string: `partnerId + timestamp + requestBody`
- Verifies using RSA-SHA256 with public key
- Returns 400 if verification fails

**Status Codes**:
- `00`: Success → `WebhookStatus::SUCCESS`
- `05`: Expired/Cancelled → `WebhookStatus::EXPIRED`
- Others → `WebhookStatus::FAILED`

**Response** (DANA expects):
```json
{
  "responseCode": "0000",
  "responseMessage": "SUCCESS"
}
```

## Database Integration (TODO)

The following fields need to be added to your `orders` table (or a separate `payments` table):

### Required Fields

```php
// In orders table migration or payments table:
$table->string('payment_gateway')->nullable(); // 'dana', etc.
$table->string('payment_reference_no')->nullable(); // DANA referenceNo
$table->string('payment_status')->nullable(); // 'pending', 'completed', 'failed', etc.
$table->timestamp('payment_initiated_at')->nullable();
$table->timestamp('payment_completed_at')->nullable();
$table->json('payment_metadata')->nullable(); // Store raw responses
```

### Implementation Points

1. **In `DanaHostedCheckoutGateway::createPaymentIntent()`**:
   ```php
   // TODO: Persist payment_reference_no to payments table
   $order->payment_reference_no = $response['referenceNo'] ?? $partnerReferenceNo;
   $order->payment_gateway = 'dana';
   $order->payment_status = 'pending';
   $order->payment_initiated_at = now();
   $order->save();
   ```

2. **In `DanaHostedCheckoutGateway::handleWebhook()`**:
   ```php
   // TODO: Implement idempotency check
   if ($order->payment_status === 'completed' && $status === WebhookStatus::SUCCESS) {
       // Already processed, skip
       return;
   }

   // TODO: Update order status
   if ($status === WebhookStatus::SUCCESS) {
       $order->payment_status = 'completed';
       $order->payment_completed_at = now();
       $order->status = OrderStatus::CONFIRMED;
   } elseif ($status === WebhookStatus::EXPIRED) {
       $order->payment_status = 'expired';
       $order->status = OrderStatus::CANCELLED;
   }
   $order->save();
   ```

## Testing

Run the test suite:

```bash
php artisan test tests/Feature/Payment/DanaWebhookTest
```

Tests cover:
- Signature generation and verification
- Timestamp format validation
- Status mapping logic

## Security Considerations

1. **Webhook Verification**: Always verify webhook signatures before processing
2. **Idempotency**: Prevent duplicate webhook processing
3. **Key Storage**: Store private keys securely (use file storage in production)
4. **HTTPS**: Ensure all endpoints use HTTPS in production
5. **Rate Limiting**: Consider adding rate limiting to webhook endpoint

## Error Handling

The integration includes comprehensive error handling:
- Invalid signatures are logged and rejected
- Missing headers return appropriate error responses
- Failed API calls are logged with context
- All exceptions are caught and returned as user-friendly messages

## Extending for Other Gateways

To add another payment gateway:

1. Implement `PaymentGateway` interface
2. Create gateway-specific helpers (if needed)
3. Register in `AppServiceProvider` with a different key
4. Update `config/payments.php` with new gateway config

Example:
```php
$this->app->when(SomeOtherGateway::class)
    ->needs(PaymentGateway::class)
    ->give(function () {
        return new SomeOtherGateway(...);
    });
```

## Support

For DANA API documentation:
- Hosted Checkout: https://dashboard.dana.id/api-docs-v2/guide/payment-gateway/hosted-checkout
- Create Order API: `/payment-gateway/v1.0/debit/payment-host-to-host.htm`
- Finish Notify: `POST /v1.0/debit/notify`

