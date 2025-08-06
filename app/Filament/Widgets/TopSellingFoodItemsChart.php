<?php

namespace App\Filament\Widgets;

use App\Models\OrderItem;
use App\Models\FoodItem;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class TopSellingFoodItemsChart extends ChartWidget
{
    protected static ?string $heading = 'Top Selling Food Items (Last 30 Days)';

    protected function getData(): array
    {
        $thirtyDaysAgo = Carbon::now()->subDays(30);
        
        // Get all order items from the last 30 days
        $orderItems = OrderItem::where('created_at', '>=', $thirtyDaysAgo)->get();
        
        // Count food items sold
        $foodItemCounts = [];
        
        foreach ($orderItems as $orderItem) {
            foreach ($orderItem->items ?? [] as $item) {
                $foodItemId = $item['food_item_id'] ?? null;
                $quantity = $item['quantity'] ?? 0;
                
                if ($foodItemId) {
                    if (!isset($foodItemCounts[$foodItemId])) {
                        $foodItemCounts[$foodItemId] = 0;
                    }
                    $foodItemCounts[$foodItemId] += $quantity;
                }
            }
        }
        
        // Sort by quantity and get top 10
        arsort($foodItemCounts);
        $topItems = array_slice($foodItemCounts, 0, 10, true);
        
        // Get food item names
        $foodItemNames = [];
        $quantities = [];
        
        foreach ($topItems as $foodItemId => $quantity) {
            $foodItem = FoodItem::find($foodItemId);
            if ($foodItem) {
                $foodItemNames[] = $foodItem->name;
                $quantities[] = $quantity;
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Quantity Sold',
                    'data' => $quantities,
                    'backgroundColor' => [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(139, 92, 246, 0.8)',
                        'rgba(236, 72, 153, 0.8)',
                        'rgba(14, 165, 233, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(251, 146, 60, 0.8)',
                        'rgba(168, 85, 247, 0.8)',
                    ],
                    'borderColor' => [
                        'rgb(59, 130, 246)',
                        'rgb(16, 185, 129)',
                        'rgb(245, 158, 11)',
                        'rgb(239, 68, 68)',
                        'rgb(139, 92, 246)',
                        'rgb(236, 72, 153)',
                        'rgb(14, 165, 233)',
                        'rgb(34, 197, 94)',
                        'rgb(251, 146, 60)',
                        'rgb(168, 85, 247)',
                    ],
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $foodItemNames,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
} 