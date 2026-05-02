<?php

namespace App\Listeners;

use App\Core\SmsProviders\ISmsProvider;
use App\Core\SmsProviders\MoceanSmsProvider;
use App\Events\SendOtpToUser;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

use function Laravel\Prompts\info;

class SendOtpToUserEventListener implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct(public ISmsProvider $sms_provider)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(SendOtpToUser $event): void
    {
        Log::debug("sending " . $event->otp . " to " . $event->user->phone_number);
        try {
            $result = $this->sms_provider->sendSms($event->user->phone_number, 'Code: ' . $event->otp);
            Log::debug('SMS provider result: ' . var_export($result, true));
        } catch (\Throwable $e) {
            Log::error('Exception while logging SMS provider result: ' . $e->getMessage());
        }
    }
}
