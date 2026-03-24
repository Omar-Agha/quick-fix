<?php

namespace App\Filament\Pages;

use App\Settings\SocialMediaLinksSettings;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ManageSocialMediaLinks extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGlobeAlt;

    protected static string $settings = SocialMediaLinksSettings::class;

    protected static string|UnitEnum|null $navigationGroup = 'Contact Info';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('facebook')
                    ->required(),
                TextInput::make('twitter')
                    ->required(),
                TextInput::make('linkedin')
                    ->required(),
                TextInput::make('instagram')
                    ->required(),
            ]);
    }
}
