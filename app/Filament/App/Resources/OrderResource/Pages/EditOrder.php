<?php

namespace App\Filament\App\Resources\OrderResource\Pages;

use App\Filament\App\Resources\OrderResource;
use App\Models\Order;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Livewire\Attributes\On;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('Pdf')
                ->label('Download PDF')
                ->action(fn(Order $record) => OrderResource::pdf($record))
                ->icon('heroicon-o-printer')
                ->color('warning'),
        ];
    }

    #[On('refreshOrders')]
    public function refresh(): void
    {
    }
}
