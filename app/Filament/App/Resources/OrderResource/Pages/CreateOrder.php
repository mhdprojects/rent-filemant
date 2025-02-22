<?php

namespace App\Filament\App\Resources\OrderResource\Pages;

use App\Filament\App\Resources\OrderResource;
use App\Helper\AutoCode;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateOrder extends CreateRecord{
    protected static string $resource = OrderResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array{
        $data['tenant_id']  = Filament::getTenant()->id;
        $data['number']     = AutoCode::orderNumber($data['tenant_id']);
        $data['user_id']    = Auth::user()->id;

        return $data;
    }
}
