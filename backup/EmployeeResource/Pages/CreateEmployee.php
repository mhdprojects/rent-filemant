<?php

namespace backup\EmployeeResource\Pages;

use backup\EmployeeResource;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateEmployee extends CreateRecord{
    protected static string $resource = EmployeeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array{
        $data['tenant_id']  = Filament::getTenant()->id;

        return $data;
    }
}
