<?php

namespace App\Filament\Widgets;

use App\Enums\VehicleStatus;
use App\Models\Vehicle;
use Filament\Widgets\ChartWidget;

class FleetStatusChart extends ChartWidget
{
    protected ?string $heading = 'Fleet Status';
    
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 1;

    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $available = Vehicle::where('status', VehicleStatus::AVAILABLE)->count();
        $booked = Vehicle::where('status', VehicleStatus::BOOKED)->count();
        $maintenance = Vehicle::where('status', VehicleStatus::MAINTENANCE)->count();

        return [
            'datasets' => [
                [
                    'label' => 'Vehicles',
                    'data' => [$available, $booked, $maintenance],
                    'backgroundColor' => [
                        'rgba(34, 197, 94, 0.8)',   // Green - Available
                        'rgba(59, 130, 246, 0.8)', // Blue - Booked
                        'rgba(234, 179, 8, 0.8)',   // Yellow - Maintenance
                    ],
                    'borderColor' => [
                        'rgb(34, 197, 94)',
                        'rgb(59, 130, 246)',
                        'rgb(234, 179, 8)',
                    ],
                    'borderWidth' => 1,
                ],
            ],
            'labels' => ['Available', 'Booked', 'Maintenance'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
            ],
        ];
    }
}
