<?php

namespace App\Filament\Resources\Companies;

use App\Filament\Resources\Companies\Pages\ManageCompanies;
use App\Models\Company;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('user');
    }
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                FileUpload::make('logo')
                    ->image()
                    ->avatar()
                    ->dehydrated(fn($state) => filled($state))
                    ->disk('public')
                    ->directory('companies')
                    ->required(fn(string $operation): bool => $operation === 'create'),
                TextInput::make('phone')
                    ->tel(),
                TextInput::make('address'),

                Section::make('User')
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->readOnly(),
                        TextInput::make('email')
                            ->readOnly(fn(string $operation): bool => $operation === 'edit')
                            ->formatStateUsing(fn($state, $record) => $record?->user?->email ?? $state)
                            ->required(),

                        TextInput::make('password')
                            ->required()
                            ->revealable()
                            ->visible(fn(string $operation): bool => $operation === 'create')
                            ->password()

                            ->prefixAction(
                                Action::make("generate-password")
                                    ->label("Generate Password")
                                    ->icon(Heroicon::OutlinedKey)
                                    ->action(fn($set) => $set('password', fake()->password(10)))

                            ),
                    ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('name'),
                ImageEntry::make('logo')
                    ->placeholder('-')

                    ->circular(),
                TextEntry::make('phone')
                    ->placeholder('-'),
                TextEntry::make('address')
                    ->placeholder('-'),
                // TextEntry::make('user.name')
                //     ->label('User')
                //     ->placeholder('-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                ImageColumn::make('logo')
                    ->circular(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('user.email'),
                TextColumn::make('phone')
                    ->searchable(),
                TextColumn::make('address')
                    ->searchable(),

            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                    Action::make('reset-password')
                        ->label('Reset Password')
                        ->icon(Heroicon::OutlinedKey)
                        ->schema([
                            TextInput::make('password')
                                ->required()
                                ->password()
                                ->revealable()
                                ->prefixAction(
                                    Action::make("generate-password")
                                        ->label("Generate Password")
                                        ->icon(Heroicon::OutlinedKey)
                                        ->action(fn($set) => $set('password', fake()->password(10)))

                                ),

                        ])
                        ->action(function (array $data, Company $record): void {
                            $record->user()->update(['password' => Hash::make($data['password'])]);
                            $record->save();
                        }),
                ])
                // show modal to reset password

            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageCompanies::route('/'),
        ];
    }
}
