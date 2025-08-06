<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Filament\Widgets\TotalCustomersWidget;
use App\Filament\Widgets\TotalFoodItemsWidget;
use App\Filament\Widgets\TotalOrdersWidget;
use App\Filament\Widgets\TotalRevenueWidget;
use App\Filament\Widgets\MonthlyRevenueChart;
use App\Filament\Widgets\FoodItemsSoldChart;
use App\Filament\Widgets\BusinessStatsOverview;
use App\Filament\Widgets\TopSellingFoodItemsChart;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->navigationItems([
                \Filament\Navigation\NavigationItem::make('POS System')
                    ->url('/pos')
                    ->icon('heroicon-o-shopping-cart')
                    ->sort(100),
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                TotalCustomersWidget::class,
                TotalFoodItemsWidget::class,
                TotalOrdersWidget::class,
                TotalRevenueWidget::class,
                MonthlyRevenueChart::class,
                FoodItemsSoldChart::class,
                BusinessStatsOverview::class,
                TopSellingFoodItemsChart::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
