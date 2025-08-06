<?php

namespace App\Filament\Widgets;

use App\Models\OrderItem;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class FoodItemsSoldChart extends ChartWidget
{
    protected static ?string $heading = 'Food Items Sold (Last 30 Days)';

    protected function getData(): array
    {
        $days = collect();
        $itemsSold = collect();

        // Get data for the last 30 days
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dayName = $date->format('M d');
            
            $dailyItemsSold = OrderItem::whereDate('created_at', $date)
                ->get()
                ->sum(function ($orderItem) {
                    return collect($orderItem->items ?? [])->sum('quantity');
                });

            $days->push($dayName);
            $itemsSold->push($dailyItemsSold);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Items Sold',
                    'data' => $itemsSold->toArray(),
                    'backgroundColor' => 'rgba(34, 197, 94, 0.2)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $days->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
} 