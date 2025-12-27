<?php

namespace App\Filament\Widgets;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class RevenueChart extends ChartWidget
{
    protected ?string $heading = 'Revenue (Last 7 Days)';
    
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 1;

    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $data = [];
        $labels = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('D');
            
            $dailyRevenue = Payment::where('status', PaymentStatus::PAID)
                ->whereDate('updated_at', $date)
                ->sum('amount');
            
            $data[] = round($dailyRevenue / 1000, 1); // Convert to thousands for readability
        }

        return [
            'datasets' => [
                [
                    'label' => 'Revenue (₦k)',
                    'data' => $data,
                    'fill' => true,
                    'backgroundColor' => 'rgba(255, 107, 53, 0.2)',
                    'borderColor' => 'rgb(255, 107, 53)',
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
        ];
    }
}
