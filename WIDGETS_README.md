# Business Dashboard Widgets

This document describes the business-related widgets that have been added to the Filament admin dashboard.

## Overview

The following widgets have been created to provide comprehensive business insights:

### Stats Overview Widgets

1. **TotalCustomersWidget** - Displays the total number of registered customers
2. **TotalFoodItemsWidget** - Shows the total number of available food items
3. **TotalOrdersWidget** - Displays the total number of orders placed
4. **TotalRevenueWidget** - Shows the total revenue generated from all orders
5. **BusinessStatsOverview** - Comprehensive widget showing all key metrics including current month stats

### Chart Widgets

1. **MonthlyRevenueChart** - Line chart showing revenue trends over the last 12 months
2. **FoodItemsSoldChart** - Bar chart showing daily food items sold over the last 30 days
3. **TopSellingFoodItemsChart** - Doughnut chart showing the top 10 selling food items

## Widget Details

### BusinessStatsOverview
- **Purpose**: Provides a comprehensive overview of all key business metrics
- **Metrics Included**:
  - Total Customers
  - Total Food Items
  - Total Orders
  - Total Revenue
  - This Month Orders
  - This Month Revenue
- **Color Scheme**: Uses different colors for different metric types

### MonthlyRevenueChart
- **Type**: Line chart
- **Data**: Last 12 months of revenue data
- **Features**: Smooth line with tension for better visualization
- **Color**: Blue theme

### FoodItemsSoldChart
- **Type**: Bar chart
- **Data**: Last 30 days of food items sold
- **Features**: Daily breakdown of items sold
- **Color**: Green theme

### TopSellingFoodItemsChart
- **Type**: Doughnut chart
- **Data**: Top 10 selling food items in the last 30 days
- **Features**: Color-coded segments for different food items
- **Color**: Multiple colors for better distinction

## Installation

All widgets are automatically discovered by Filament and registered in the `AdminPanelProvider`. They will appear on the dashboard once the application is refreshed.

## Usage

1. Navigate to the admin dashboard (`/admin`)
2. The widgets will be displayed on the dashboard page
3. You can drag and drop widgets to rearrange them
4. Widgets will automatically refresh their data

## Data Sources

- **Customers**: `App\Models\Customer`
- **Food Items**: `App\Models\FoodItem`
- **Orders**: `App\Models\Order`
- **Order Items**: `App\Models\OrderItem`

## Customization

To customize the widgets:

1. Edit the widget files in `app/Filament/Widgets/`
2. Modify colors, descriptions, or data calculations as needed
3. Refresh the dashboard to see changes

## Notes

- All revenue calculations use the `total_amount` field from orders
- Food items sold calculations aggregate quantities from the JSON `items` field in order items
- Charts use Chart.js for rendering
- Widgets are responsive and will adapt to different screen sizes 