<?php

namespace App\Filament\App\Widgets;

use App\Models\Expense;
use App\Models\Order;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsTotalOverviewWidget extends BaseWidget{
    use InteractsWithPageFilters;

    protected static ?int $sort = 0;

    protected function getColumns(): int{
        return 2;
    }

    protected function getStats(): array{
        $startDate = ! is_null($this->filters['startDate'] ?? null) ?
            Carbon::parse($this->filters['startDate']) :
            null;

        $endDate = ! is_null($this->filters['endDate'] ?? null) ?
            Carbon::parse($this->filters['endDate']) :
            now();

        $tenantId = Filament::getTenant()->id;

        $orderQuery = Order::query()
            ->where('tenant_id', $tenantId)
            ->whereNull('deleted_at');

        $expenseQuery = Expense::query()
            ->where('tenant_id', $tenantId)
            ->whereNull('deleted_at');

        if ($startDate && $endDate){
            $orderQuery->whereBetween('tgl', [
                $startDate->format('Y-m-d'),
                $endDate->format('Y-m-d')
            ]);

            $expenseQuery->whereBetween('tgl', [
                $startDate->format('Y-m-d'),
                $endDate->format('Y-m-d')
            ]);
        }

        return [
            Stat::make('Total Pemasukan', number_format($orderQuery->sum('paid'))),
            Stat::make('Total Pengeluaran', number_format($expenseQuery->sum('nominal'))),
        ];
    }
}
