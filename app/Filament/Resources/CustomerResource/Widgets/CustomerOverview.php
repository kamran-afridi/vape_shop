<?php

namespace App\Filament\Resources\CustomerResource\Widgets;

use Filament\Widgets\ChartWidget;

class CustomerOverview extends ChartWidget
{
    protected static ?string $heading = 'Profit';
    protected static string $color = 'info';
    public function getChartData(): array
    {
        // Example chart data
        return [
            'datasets' => [
                [
                    'label' => 'Blog posts created',
                    'data' => [0, 10, 5, 2, 21, 32, 45, 74, 65, 45, 77, 89],
                    'backgroundColor' => '#36A2EB',
                    'borderColor' => '#9BD0F5',
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
