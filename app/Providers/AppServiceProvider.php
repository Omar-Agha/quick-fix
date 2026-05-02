<?php

namespace App\Providers;

use App\Enums\UserRole;
use App\Models\User;
use App\Payments\Contracts\PaymentGateway;
use App\Payments\Dana\DanaClient;
use App\Payments\Dana\DanaKeyHelper;
use App\Payments\Gateways\DanaHostedCheckoutGateway;
use App\Payments\Gateways\MidtransSnapGateway;
use App\Payments\Midtrans\MidtransClient;
use App\Settings\ContactInfoSettings;
use App\Settings\MobileApplicationLinkSettings;
use App\Settings\SocialMediaLinksSettings;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
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
        Gate::define('viewPulse', function (User $user) {
            return $user->role == UserRole::DEBUG_ADMIN;
        });




        $settingsTable = config('settings.repositories.database.table') ?: 'settings';

        if (! Schema::hasTable($settingsTable)) {
            Inertia::share([
                'app_config' => config('app.app_config'),
                'contact_info' => [
                    'email' => '',
                    'phone' => '',
                    'address' => '',
                ],
                'social_media_links' => [
                    'facebook' => '#',
                    'twitter' => '#',
                    'linkedin' => '#',
                    'instagram' => '#',
                ],
                'mobile_application_links' => [
                    'android_link' => '#',
                    'ios_link' => '#',
                ],
            ]);

            return;
        }

        Inertia::share([
            'app_config' => config('app.app_config'),
            'contact_info' => [
                'email' => app(ContactInfoSettings::class)->email,
                'phone' => app(ContactInfoSettings::class)->phone,
                'address' => app(ContactInfoSettings::class)->address,
            ],
            'social_media_links' => [
                'facebook' => app(SocialMediaLinksSettings::class)->facebook,
                'twitter' => app(SocialMediaLinksSettings::class)->twitter,
                'linkedin' => app(SocialMediaLinksSettings::class)->linkedin,
                'instagram' => app(SocialMediaLinksSettings::class)->instagram,
            ],
            'mobile_application_links' => [
                'android_link' => app(MobileApplicationLinkSettings::class)->android_link,
                'ios_link' => app(MobileApplicationLinkSettings::class)->ios_link,
            ],
        ]);
    }
}
