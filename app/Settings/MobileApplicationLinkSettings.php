<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class MobileApplicationLinkSettings extends Settings
{
    public string $android_link;
    public string $ios_link;
    public static function group(): string
    {
        return 'mobile_application_links';
    }
}
