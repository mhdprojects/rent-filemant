<?php

namespace App\Filament\App\Resources\PaymentMethodResource\Pages;

use App\Filament\App\Resources\PaymentMethodResource;
use App\Models\PaymentMethod;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\EditRecord;

class EditPaymentMethod extends EditRecord
{
    protected static string $resource = PaymentMethodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array{
        $data['tenant_id']  = Filament::getTenant()->id;

        if ($data['is_default']){
            $select = PaymentMethod::query()
                ->where('tenant_id', $data['tenant_id'])
                ->whereNull('deleted_at')
                ->where('is_default', true)
                ->get();

            foreach ($select as $item) {
                $item->is_default = false;
                $item->save();
            }
        }

        return $data;
    }
}
