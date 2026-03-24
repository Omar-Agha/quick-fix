<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('contact_info.email', 'info@bam.com.sa');
        $this->migrator->add('contact_info.phone', '+966 11 444 4444');
        $this->migrator->add('contact_info.address', 'Riyadh, Saudi Arabia');
    }
};
