<?php

namespace App\Filament\Pages;

use App\Settings\MobileApplicationLinkSettings;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ManageMobileAppLink extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDevicePhoneMobile;

    protected static string $settings = MobileApplicationLinkSettings::class;
    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('android_link')
                    ->required(),
                TextInput::make('ios_link')
                    ->required(),
            ]);
    }
}
