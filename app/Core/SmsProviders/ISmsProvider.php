<?php

namespace App\Core\SmsProviders;

interface ISmsProvider
{
    public function sendSms(string $recipient, string $message): bool;
}
