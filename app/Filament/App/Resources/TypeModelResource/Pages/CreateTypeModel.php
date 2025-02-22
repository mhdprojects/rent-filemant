<?php

namespace App\Filament\App\Resources\TypeModelResource\Pages;

use App\Filament\App\Resources\TypeModelResource;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateTypeModel extends CreateRecord{
    protected static string $resource = TypeModelResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array{
        $data['tenant_id']  = Filament::getTenant()->id;

        return $data;
    }
}
