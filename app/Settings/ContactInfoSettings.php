<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class ContactInfoSettings extends Settings
{
    public string $email = 'info@bam.com.sa';

    public string $phone = '+966 11 444 4444';

    public string $address = 'Riyadh, Saudi Arabia';

    public static function group(): string
    {
        return 'contact_info';
    }
}
