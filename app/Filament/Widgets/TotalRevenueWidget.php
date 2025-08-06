<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TotalRevenueWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalRevenue = Order::sum('total_amount');
        
        return [
            Stat::make('Total Revenue', 'Rs. ' . number_format($totalRevenue, 0))
                ->description('Total revenue generated from all orders')
                ->descriptionIcon('heroicon-m-currency-rupee')
                ->color('success'),
        ];
    }
} 