<?php

namespace App\Filament\Widgets;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Service;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;
    protected function getStats(): array
    {
        return [
            Stat::make('Services', Service::whereIsActive(true)->count())
                ->description('Total services')
                ->color('info'),
            Stat::make('Pending Orders', Order::whereStatus(OrderStatus::PAYMENT_SUCCESS)->count())
                ->description('Total pending orders')
                ->descriptionIcon('heroicon-o-clock')
                ->color('warning'),
            Stat::make('Completed Orders', Order::whereIsCompleted(true)->count())
                ->description('Total completed orders')
                ->color('success')
                ->descriptionIcon('heroicon-o-check-circle'),
            Stat::make('Revenue', Order::whereStatus(OrderStatus::PAYMENT_SUCCESS)->sum('pay_at_cashier') . config('app.currency'))
                ->description('Total revenue')
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('success')

                ->descriptionIcon('heroicon-o-currency-dollar'),

        ];
    }
}
