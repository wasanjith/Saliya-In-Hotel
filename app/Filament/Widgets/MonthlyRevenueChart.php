<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class MonthlyRevenueChart extends ChartWidget
{
    protected static ?string $heading = 'Monthly Revenue';

    protected function getData(): array
    {
        $months = collect();
        $revenue = collect();

        // Get data for the last 12 months
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->format('M Y');
            
            $monthlyRevenue = Order::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('total_amount');

            $months->push($monthName);
            $revenue->push($monthlyRevenue);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Revenue (Rs.)',
                    'data' => $revenue->toArray(),
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 2,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $months->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
} 