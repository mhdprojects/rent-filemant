<?php

namespace App\Filament\App\Resources\BrandResource\Pages;

use App\Filament\App\Resources\BrandResource;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateBrand extends CreateRecord{
    protected static string $resource = BrandResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array{
        $data['tenant_id']  = Filament::getTenant()->id;

        return $data;
    }
}
