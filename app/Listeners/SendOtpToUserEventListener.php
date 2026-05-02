<?php

namespace App\Listeners;

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
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(SendOtpToUser $event): void
    {
        Log::info("sending " . $event->otp . " to " . $event->user->phone_number);
        $result = app(MoceanSmsProvider::class)
            ->sendSms($event->user->phone_number, 'Code: ' . $event->otp);
    }
}
