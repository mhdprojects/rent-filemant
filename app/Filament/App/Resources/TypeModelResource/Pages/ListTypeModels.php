<?php

namespace App\Filament\App\Resources\TypeModelResource\Pages;

use App\Filament\App\Resources\TypeModelResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTypeModels extends ListRecords
{
    protected static string $resource = TypeModelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
