<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class RevenueChart extends ChartWidget
{
    protected ?string $heading = 'Revenue (Last 14 Days)';

    protected function getData(): array
    {
        $data = collect();
        $today = Carbon::today();

        for ($i = 13; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);
            $revenue = Order::where('status', 'delivered')
                ->whereDate('created_at', $date)
                ->sum('total_amount');
            $data->push([
                'date' => $date->format('M d'),
                'revenue' => (float) $revenue,
            ]);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => $data->pluck('revenue')->toArray(),
                    'borderColor' => '#8f3c1f',
                    'backgroundColor' => 'rgba(143, 60, 31, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $data->pluck('date')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
