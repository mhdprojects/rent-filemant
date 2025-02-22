<?php

namespace App\Filament\App\Widgets;

use App\Filament\App\Resources\OrderResource;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget as BaseWidget;

class OrderNewWidget extends BaseWidget{
    use InteractsWithPageFilters;

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 4;

    public function table(Table $table): Table{
        $startDate = ! is_null($this->filters['startDate'] ?? null) ?
            Carbon::parse($this->filters['startDate']) :
            now();

        $endDate = ! is_null($this->filters['endDate'] ?? null) ?
            Carbon::parse($this->filters['endDate']) :
            now();

        $tenantId = Filament::getTenant()->id;

        return $table
            ->query(
                OrderResource::getGlobalSearchEloquentQuery()
                    ->whereNull('deleted_at')
                    ->where('tenant_id', $tenantId)
                    ->whereBetween('tgl', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            )
            ->defaultPaginationPageOption(10)
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->label('No. Trx')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tgl')
                    ->date()
                    ->toggleable()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('contact.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->toggleable()
                    ->badge(),
                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Pembayaran')
                    ->toggleable()
                    ->badge(),
                Tables\Columns\TextColumn::make('subtotal')
                    ->currency()
                    ->searchable()
                    ->sortable(),
            ]);
    }
}
