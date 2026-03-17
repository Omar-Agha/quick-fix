<?php

namespace App\Filament\Resources\Orders;

use App\Enums\OrderStatus;
use App\Filament\Resources\Orders\Pages\ManageOrders;
use App\Models\Order;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInbox;
    //add menu badge 
    public static function getNavigationBadge(): ?string
    {
        return Order::where('status', OrderStatus::PAYMENT_SUCCESS)->where('is_completed', false)->count();
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['mobileUser', 'locationAddress', 'orderItems.service', 'files'])
            ->orderBy('is_completed')
            ->orderByDesc('created_at');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Order Details')
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->columns(['md' => 2])
                    ->schema([
                        TextEntry::make('status')
                            ->badge()
                            ->formatStateUsing(fn(OrderStatus $state, ?Order $order) => $order->is_completed ? 'Completed' : $state->name)
                            ->color(
                                fn(OrderStatus $state, ?Order $record) => match ($state) {
                                    OrderStatus::PAYMENT_SUCCESS => $record->is_completed ? 'success' : 'info',
                                    OrderStatus::PAYMENT_FAILED => 'danger',
                                    OrderStatus::PAYMENT_PENDING => 'warning',
                                    default => 'warning'
                                }
                            ),
                        TextEntry::make('created_at')
                            ->label('Ordered')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('mobileUser.phone_number')
                            ->label('Customer')
                            ->placeholder('-'),
                        TextEntry::make('locationAddress.full_address')
                            ->label('Address')
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('total_cost')
                            ->money(config('app.currency')),
                        TextEntry::make('fees')
                            ->money(config('app.currency')),
                        TextEntry::make('pay_at_cashier')
                            ->label('Pay at cashier')
                            ->money(config('app.currency')),
                        TextEntry::make('discount')
                            ->money(config('app.currency')),
                        TextEntry::make('payment_method')
                            ->placeholder('-'),
                        IconEntry::make('is_direct_service')
                            ->label('Direct service')
                            ->boolean(),
                        TextEntry::make('reserve_datetime')
                            ->dateTime()
                            ->placeholder('-'),

                    ]),
                Section::make('Order Items')
                    ->icon(Heroicon::OutlinedQueueList)
                    ->schema([
                        RepeatableEntry::make('orderItems')
                            ->hiddenLabel()

                            ->schema([
                                TextEntry::make('service.name')
                                    ->hiddenLabel()
                                    ->weight(FontWeight::SemiBold),
                                ImageEntry::make('service.image')
                                    ->circular()
                                    ->hiddenLabel(),
                                TextEntry::make('number_of_workers')
                                    ->label('Workers')
                                    ->numeric()
                                    ->hiddenLabel(),
                                TextEntry::make('cost')
                                    ->money(config('app.currency')),
                            ])
                            ->columns(2)
                            ->contained(false)
                            ->grid(1),
                    ])
                    ->collapsible(),
                Section::make('Order Images')
                    ->icon(Heroicon::OutlinedPhoto)
                    ->schema([
                        RepeatableEntry::make('files')
                            ->label('')
                            ->schema([

                                ImageEntry::make('path')
                                    ->disk('public')

                                    ->imageWidth(100)
                                    ->imageHeight(100)
                                    ->hiddenLabel()
                            ])
                            ->contained(false)
                            ->grid(['sm' => 2, 'md' => 3, 'lg' => 4])
                            ->columns(4),
                    ])
                    ->visible(fn(?Order $record) => $record?->files?->isNotEmpty() ?? false)
                    ->collapsible()
                    ->collapsed(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table

            ->columns([
                Grid::make(1)
                    ->schema([
                        Split::make([
                            ImageColumn::make('mobileUser.avatar')
                                ->circular()
                                ->defaultImageUrl(fn() => 'https://ui-avatars.com/api/?background=94a3b8&color=fff&name=User')
                                ->grow(false),
                            Stack::make([
                                TextColumn::make('mobileUser.phone_number')
                                    ->label('Customer')
                                    ->weight(FontWeight::SemiBold)
                                    ->searchable(),
                                TextColumn::make('created_at')
                                    ->label('Ordered')
                                    ->dateTime('M j, Y g:i A')
                                    ->sortable()
                                    ->color('gray')
                                    ->size('sm'),
                            ])->space(1),
                            TextColumn::make('status')
                                ->badge()
                                ->formatStateUsing(fn(OrderStatus $state, ?Order $order) => $order->is_completed ? 'Completed' : $state->name)
                                ->color(fn(OrderStatus $state, ?Order $record) => match ($state) {
                                    OrderStatus::PAYMENT_SUCCESS => $record->is_completed ? 'success' : 'info',
                                    OrderStatus::PAYMENT_FAILED => 'danger',
                                    OrderStatus::PAYMENT_PENDING => 'warning',
                                    default => 'warning',
                                }),
                        ])->from('sm'),
                        Stack::make([
                            TextColumn::make('locationAddress.full_address')
                                ->label('Address')
                                ->icon(Heroicon::OutlinedMapPin)
                                ->placeholder('-')
                                ->limit(50)
                                ->tooltip(fn(?Order $record) => $record?->locationAddress?->full_address),
                        ])->space(1),
                        Split::make([
                            Stack::make([
                                TextColumn::make('pay_at_cashier')
                                    ->label('Pay at cashier')
                                    ->tooltip('Pay at cashier of the order')
                                    ->money(config('app.currency'))

                                    ->size('sm')
                                    ->weight(FontWeight::SemiBold),

                                TextColumn::make('fees')
                                    ->label('Fees')
                                    ->tooltip('Fees of the order')
                                    ->money(config('app.currency'))
                                    ->color('gray')
                                    ->size('sm'),
                            ])->space(1),
                            Stack::make([
                                TextColumn::make('total_cost')
                                    ->label('Total')
                                    ->color('gray')
                                    ->tooltip('Total cost of the order details')
                                    ->money(config('app.currency')),
                                TextColumn::make('discount')
                                    ->label('Discount')
                                    ->tooltip('Discount of the order')
                                    ->money(config('app.currency'))
                                    ->color(fn(?Order $record) => ($record?->discount ?? 0) > 0 ? 'success' : 'gray')
                                    ->size('sm'),
                            ])->space(1)->alignment(Alignment::End),
                        ])->from('md'),
                        Split::make([
                            TextColumn::make('reserve_datetime')
                                ->label('Reserved')
                                ->dateTime('M j, g:i A')
                                ->placeholder('-')
                                ->icon(Heroicon::OutlinedCalendarDays)
                                ->color('gray')
                                ->size('sm'),
                            IconColumn::make('is_direct_service')
                                ->label('Direct')
                                ->boolean()
                                ->tooltip('Is Service Direct')
                                ->trueIcon(Heroicon::OutlinedCheckCircle)
                                ->falseIcon(Heroicon::OutlinedXCircle)
                                ->trueColor('success')
                                ->falseColor('gray'),
                        ])->from('sm'),
                        Panel::make([

                            Grid::make(1)
                                ->schema([
                                    TextColumn::make('orderItems.service.name')
                                        ->listWithLineBreaks(),
                                    // TextColumn::make('orderItems.service.name')
                                    //     ->label('Service')
                                    //     ->placeholder('-')
                                    //     ->limit(100)
                                    //     ->wrap(),
                                ]),

                        ])->collapsible(),
                        // Panel::make([
                        TextColumn::make('description')
                            ->placeholder('No description')
                            ->limit(100)
                            ->color('gray')
                            ->wrap(),
                        // ])->collapsible()->collapsed(),
                    ]),
            ])
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])

            ->filters([
                SelectFilter::make('status')
                    ->options(OrderStatus::class)
                    ->native(false),
                TernaryFilter::make('is_completed')
                    ->options([
                        true => 'Completed',
                        false => 'Pending',
                    ])
                    ->queries(
                        true: fn(Builder $query) => $query->where('is_completed', true),
                        false: fn(Builder $query) => $query->where('is_completed', false)->where('status', OrderStatus::PAYMENT_SUCCESS),
                        blank: fn(Builder $query) => $query, // In this example, we do not want to filter the query when it is blank.
                    )
                    ->native(false),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('markAsCompleted')
                    ->label('Mark as Completed')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->label('Complete order')
                    ->requiresConfirmation()
                    ->visible(fn(Order $record) => $record->status == OrderStatus::PAYMENT_SUCCESS && !$record->is_completed)

                    ->action(fn(Order $record) => $record->update(['is_completed' => true])),
                // EditAction::make(),
                // DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageOrders::route('/'),
        ];
    }
}
