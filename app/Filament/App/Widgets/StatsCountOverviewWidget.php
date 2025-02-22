<?php

namespace App\Filament\App\Widgets;

use App\Models\Asset;
use App\Models\Contact;
use App\Models\Expense;
use App\Models\Order;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsCountOverviewWidget extends BaseWidget{
    use InteractsWithPageFilters;

    protected static ?int $sort = 0;

    protected function getStats(): array{
        $startDate = ! is_null($this->filters['startDate'] ?? null) ?
            Carbon::parse($this->filters['startDate']) :
            null;

        $endDate = ! is_null($this->filters['endDate'] ?? null) ?
            Carbon::parse($this->filters['endDate']) :
            now();

        $tenantId = Filament::getTenant()->id;

        $assetCount = Asset::query()
            ->where('tenant_id', $tenantId)
            ->whereNull('deleted_at')
            ->count();

        $custCount = Contact::query()
            ->where('tenant_id', $tenantId)
            ->whereNull('deleted_at')
            ->where('is_customer', true)
            ->count();

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
            Stat::make('Jumlah Asset', number_format($assetCount)),
            Stat::make('Jumlah Customer', number_format($custCount)),
            Stat::make('Jumlah Order', number_format($orderQuery->count())),
            Stat::make('Jumlah Expense', number_format($expenseQuery->count())),
        ];
    }
}
