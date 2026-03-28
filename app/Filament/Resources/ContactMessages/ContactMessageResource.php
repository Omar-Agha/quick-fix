<?php

namespace App\Filament\Resources\ContactMessages;

use App\Filament\Resources\ContactMessages\Pages\ManageContactMessages;
use App\Models\ContactMessage;
use BackedEnum;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->orderByDesc('read_at');
    }
    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::query()->unread()->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'danger';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('subject')
                    ->required(),
                Textarea::make('message')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('sender')
                    ->hiddenLabel()
                    ->getStateUsing(fn(ContactMessage $record) => $record->name . ' | ' . $record->email),
                TextEntry::make('subject')
                    ->columnSpanFull()
                    ->hiddenLabel(),

                TextEntry::make('message')
                    ->hiddenLabel()
                    ->columnSpanFull()
                    ->getStateUsing(fn(ContactMessage $record) => $record->message),
                TextEntry::make('created_at')
                    ->label('Sent at')

                    ->getStateUsing(fn(ContactMessage $record) => $record->created_at),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('subject')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->getStateUsing(fn(ContactMessage $record): string => $record->isUnread() ? 'Read' : 'Unread')

                    ->badge()
                    ->color(fn(ContactMessage $record): string => $record->isUnread() ? 'success' : 'danger'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_read')
                    ->options([
                        true => 'Read',
                        false => 'Unread',
                        null => 'All',
                    ])
                    ->native(false)
                    ->queries(
                        true: fn(Builder $query) => $query->whereNotNull('read_at'),
                        false: fn(Builder $query) => $query->whereNull('read_at'),
                        blank: fn(Builder $query) => $query,
                    ),
            ])
            ->recordActions([
                ViewAction::make()
                    ->afterFormFilled(function (ContactMessage $record): void {
                        if ($record->isUnread()) {
                            $record->markAsRead();
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('markAsRead')
                        ->label('Mark as Read')
                        ->icon(Heroicon::OutlinedCheckCircle)
                        ->color('success')
                        ->action(function (Collection $records): void {
                            $records->each->markAsRead();
                        }),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageContactMessages::route('/'),
        ];
    }
}
