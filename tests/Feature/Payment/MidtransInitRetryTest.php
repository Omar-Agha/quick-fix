<?php

namespace Tests\Feature\Payment;

use App\Enums\OrderStatus as OrderStatusEnum;
use App\Enums\PaymentStatus;
use App\Models\LocationAddress;
use App\Models\MobileUser;
use App\Models\Order;
use App\Models\Payment;
use App\Payments\Gateways\MidtransSnapGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MidtransInitRetryTest extends TestCase
{
    use RefreshDatabase;

    public function test_init_after_expired_creates_new_attempt_with_unique_gateway_reference(): void
    {
        $mobileUser = MobileUser::create([
            'phone_number' => '+6281234567890',
            'password' => bcrypt('secret'),
        ]);
        $address = LocationAddress::create([
            'mobile_user_id' => $mobileUser->id,
            'address_type' => 1,
            'address' => 'Jl. Test',
            'full_address' => 'Jl. Test, Jakarta',
        ]);
        $order = Order::create([
            'mobile_user_id' => $mobileUser->id,
            'location_address_id' => $address->id,
            'is_direct_service' => true,
            'total_cost' => 100000,
            'fees' => 5000,
            'pay_at_cashier' => 105000,
            'status' => OrderStatusEnum::PENDING,
        ]);

        $order->payments()->create([
            'gateway' => 'midtrans',
            'gateway_reference' => 'order-'.$order->id,
            'gateway_transaction_id' => null,
            'status' => PaymentStatus::EXPIRED,
            'amount' => 105000,
            'metadata' => [],
            'initiated_at' => now()->subHour(),
            'redirect_url' => 'https://old-expired.url',
        ]);

        $newRedirectUrl = 'https://app.sandbox.midtrans.com/snap/retry-token';
        $baseUrl = config('payments.midtrans.base_url', 'https://app.sandbox.midtrans.com');
        Http::fake([
            $baseUrl.'/snap/v1/transactions' => Http::response([
                'redirect_url' => $newRedirectUrl,
                'token' => 'retry-token-123',
            ], 200),
        ]);

        $gateway = app(MidtransSnapGateway::class);
        $result = $gateway->createPaymentIntent($order->fresh());

        $this->assertTrue($result->success);
        $this->assertSame($newRedirectUrl, $result->redirectUrl);

        $payments = Payment::where('order_id', $order->id)->where('gateway', 'midtrans')->orderBy('id')->get();
        $this->assertCount(2, $payments);
        $this->assertSame('order-'.$order->id, $payments[0]->gateway_reference);
        $this->assertSame(PaymentStatus::EXPIRED, $payments[0]->status);
        $this->assertSame('order-'.$order->id.'-2', $payments[1]->gateway_reference);
        $this->assertSame(PaymentStatus::PENDING, $payments[1]->status);
        $this->assertSame($newRedirectUrl, $payments[1]->redirect_url);
    }

    public function test_init_when_already_completed_returns_already_paid(): void
    {
        $mobileUser = MobileUser::create([
            'phone_number' => '+6281234567891',
            'password' => bcrypt('secret'),
        ]);
        $address = LocationAddress::create([
            'mobile_user_id' => $mobileUser->id,
            'address_type' => 1,
            'address' => 'Jl. Test',
            'full_address' => 'Jl. Test, Jakarta',
        ]);
        $order = Order::create([
            'mobile_user_id' => $mobileUser->id,
            'location_address_id' => $address->id,
            'is_direct_service' => true,
            'total_cost' => 100000,
            'fees' => 5000,
            'pay_at_cashier' => 105000,
            'status' => OrderStatusEnum::COMPLETED,
        ]);

        $order->payments()->create([
            'gateway' => 'midtrans',
            'gateway_reference' => 'order-'.$order->id,
            'gateway_transaction_id' => 'mt-123',
            'status' => PaymentStatus::COMPLETED,
            'amount' => 105000,
            'metadata' => [],
            'initiated_at' => now()->subHour(),
            'redirect_url' => 'https://completed.url',
        ]);

        Http::fake(fn () => Http::response([], 500));

        $gateway = app(MidtransSnapGateway::class);
        $result = $gateway->createPaymentIntent($order->fresh());

        $this->assertTrue($result->success);
        $this->assertSame('https://completed.url', $result->redirectUrl);
        $this->assertTrue(($result->rawResponse['already_paid'] ?? false));
        $this->assertSame(1, Payment::where('order_id', $order->id)->where('gateway', 'midtrans')->count());
    }
}
