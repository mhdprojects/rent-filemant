<?php

namespace App\Filament\App\Widgets;

use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class ChartOrderWidget extends ChartWidget{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Chart';

    protected static ?int $sort = 2;

    public function getColumnSpan(): int|string|array{
        return 2;
    }

    protected function getData(): array{
        $startDate = ! is_null($this->filters['startDate'] ?? null) ?
            Carbon::parse($this->filters['startDate']) :
            null;

        $endDate = ! is_null($this->filters['endDate'] ?? null) ?
            Carbon::parse($this->filters['endDate']) :
            now();

        $tenantId = Filament::getTenant()->id;

        return [
            'datasets' => [
                [
                    'label' => 'Orders',
                    'data' => [2433, 3454, 4566, 3300, 5545, 5765, 6787, 8767, 7565, 8576, 9686, 8996],
                    'backgroundColor' => '#36A2EB',
                    'borderColor' => '#9BD0F5',
                ],
                [
                    'label' => 'Expense',
                    'data' => [1456, 2404, 1752, 1925, 2014, 2600, 7500, 5325, 1869, 3057, 4025, 1999],
                    'backgroundColor' => '#cc0000',
                    'borderColor' => '#e06666',
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
