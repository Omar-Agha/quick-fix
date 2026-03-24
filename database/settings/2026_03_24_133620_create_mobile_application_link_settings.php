<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('mobile_application_links.android_link', 'https://play.google.com/store/games?hl=en&pli=1');
        $this->migrator->add('mobile_application_links.ios_link', 'https://www.apple.com/app-store/');
    }
};
