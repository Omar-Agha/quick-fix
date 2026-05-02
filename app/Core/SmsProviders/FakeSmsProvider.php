<?php

namespace App\Core\SmsProviders;

use Illuminate\Support\Facades\Log;

class FakeSmsProvider implements ISmsProvider
{
    /**
     * Simulate sending an SMS and log the action.
     *
     * @param string $recipient
     * @param string $message
     * @return bool
     */
    public function sendSms(string $recipient, string $message): bool
    {
        Log::debug('FakeSmsProvider: SMS not sent. Would have sent SMS.', [
            'recipient' => $recipient,
            'message' => $message,
        ]);
        return true;
    }
}
