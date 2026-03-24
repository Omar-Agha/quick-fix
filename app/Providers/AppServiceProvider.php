<?php

namespace App\Providers;

use App\Payments\Contracts\PaymentGateway;
use App\Payments\Dana\DanaClient;
use App\Payments\Dana\DanaKeyHelper;
use App\Payments\Gateways\DanaHostedCheckoutGateway;
use App\Payments\Gateways\MidtransSnapGateway;
use App\Payments\Midtrans\MidtransClient;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register payment gateway implementation
        $this->app->singleton(PaymentGateway::class, function ($app) {
            $config = config('payments.dana');

            // Load keys (supports both file paths and direct content)
            $privateKey = DanaKeyHelper::loadKey($config['private_key']);
            $publicKey = DanaKeyHelper::loadKey($config['public_key']);

            $client = new DanaClient(
                baseUrl: $config['base_url'],
                partnerId: $config['partner_id'],
                merchantId: $config['merchant_id'],
                channelId: $config['channel_id'],
                origin: $config['origin'],
                privateKey: $privateKey,
            );

            return new DanaHostedCheckoutGateway(
                client: $client,
                publicKey: $publicKey,
                returnUrl: $config['return_url'],
                notifyUrl: $config['notify_url'],
            );
        });

        // Midtrans Snap gateway (resolved by class name for Midtrans-specific routes)
        $this->app->singleton(MidtransSnapGateway::class, function ($app) {
            $config = config('payments.midtrans');
            $client = new MidtransClient(
                baseUrl: $config['base_url'],
                serverKey: $config['server_key'],
            );

            return new MidtransSnapGateway(
                client: $client,
                serverKey: $config['server_key'],
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Inertia::share([
            'app_config' => config('app.app_config'),
        ]);
    }
}
