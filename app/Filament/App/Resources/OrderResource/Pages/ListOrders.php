<?php

namespace App\Filament\App\Resources\OrderResource\Pages;

use App\Filament\App\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            null => Tab::make('All'),
            'Baru' => Tab::make()->query(fn ($query) => $query->where('status', 'new')),
            'Proses' => Tab::make()->query(fn ($query) => $query->where('status', 'processing')),
            'Sebagian' => Tab::make()->query(fn ($query) => $query->where('status', 'partial')),
            'Selesai' => Tab::make()->query(fn ($query) => $query->where('status', 'done')),
            'Batal' => Tab::make()->query(fn ($query) => $query->where('status', 'cancelled')),
        ];
    }
}
