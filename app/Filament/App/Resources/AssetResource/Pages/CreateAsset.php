<?php

namespace App\Filament\App\Resources\AssetResource\Pages;

use App\Filament\App\Resources\AssetResource;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateAsset extends CreateRecord{
    protected static string $resource = AssetResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array{
        $data['tenant_id']  = Filament::getTenant()->id;

        return $data;
    }
}
