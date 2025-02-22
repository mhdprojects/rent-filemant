<?php

namespace App\Filament\App\Resources\TypeModelResource\Pages;

use App\Filament\App\Resources\TypeModelResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTypeModel extends EditRecord
{
    protected static string $resource = TypeModelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
