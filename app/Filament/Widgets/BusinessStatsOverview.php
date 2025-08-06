<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Models\FoodItem;
use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BusinessStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalCustomers = Customer::count();
        $totalFoodItems = FoodItem::count();
        $totalOrders = Order::count();
        $totalRevenue = Order::sum('total_amount');
        
        // Calculate this month's stats
        $thisMonthOrders = Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        $thisMonthRevenue = Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');

        return [
            Stat::make('Total Customers', $totalCustomers)
                ->description('Registered customers in the system')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
            
            Stat::make('Total Food Items', $totalFoodItems)
                ->description('Available food items in the menu')
                ->descriptionIcon('heroicon-m-cake')
                ->color('success'),
            
            Stat::make('Total Orders', $totalOrders)
                ->description('Total orders placed in the system')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('warning'),
            
            Stat::make('Total Revenue', 'Rs. ' . number_format($totalRevenue, 0))
                ->description('Total revenue generated from all orders')
                ->descriptionIcon('heroicon-m-currency-rupee')
                ->color('success'),
            
            Stat::make('This Month Orders', $thisMonthOrders)
                ->description('Orders placed this month')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),
            
            Stat::make('This Month Revenue', 'Rs. ' . number_format($thisMonthRevenue, 0))
                ->description('Revenue generated this month')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('success'),
        ];
    }
} 