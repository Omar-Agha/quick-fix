<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('social_media_links.facebook', 'https://www.facebook.com/');
        $this->migrator->add('social_media_links.twitter', 'https://www.x.com/');
        $this->migrator->add('social_media_links.linkedin', 'https://www.linkedin.com/');
        $this->migrator->add('social_media_links.instagram', 'https://www.instagram.com/');
    }
};
