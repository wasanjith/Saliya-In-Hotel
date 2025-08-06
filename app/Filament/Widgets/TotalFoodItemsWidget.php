<?php

namespace App\Filament\Widgets;

use App\Models\FoodItem;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TotalFoodItemsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Food Items', FoodItem::count())
                ->description('Available food items in the menu')
                ->descriptionIcon('heroicon-m-cake')
                ->color('success'),
        ];
    }
} 