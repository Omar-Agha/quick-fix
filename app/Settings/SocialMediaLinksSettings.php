<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class SocialMediaLinksSettings extends Settings
{
    public string $facebook;

    public string $twitter;

    public string $linkedin;

    public string $instagram;

    public static function group(): string
    {
        return 'social_media_links';
    }
}
