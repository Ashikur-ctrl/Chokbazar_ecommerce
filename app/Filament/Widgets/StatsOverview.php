<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected ?string $heading = 'Overview';

    protected function getStats(): array
    {
        $todayOrders = Order::whereDate('created_at', Carbon::today())->count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $lowStockCount = Product::whereColumn('stock', '<=', 'low_stock_threshold')->count();
        $revenueThisWeek = Order::where('status', 'delivered')
            ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()])
            ->sum('total_amount');

        return [
            Stat::make('Today\'s Orders', $todayOrders)
                ->icon('heroicon-o-shopping-cart')
                ->description('Orders placed today')
                ->color('primary'),
            Stat::make('Pending Orders', $pendingOrders)
                ->icon('heroicon-o-clock')
                ->description('Orders awaiting processing')
                ->color('warning'),
            Stat::make('Low Stock Products', $lowStockCount)
                ->icon('heroicon-o-exclamation-triangle')
                ->description('Products below threshold')
                ->color('danger'),
            Stat::make('Revenue This Week', '৳' . number_format($revenueThisWeek, 2))
                ->icon('heroicon-o-banknotes')
                ->description('Delivered orders this week')
                ->color('success'),
        ];
    }
}
