<?php

namespace App\Filament\App\Resources\ExpenseCategoryResource\Pages;

use App\Filament\App\Resources\ExpenseCategoryResource;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateExpenseCategory extends CreateRecord{
    protected static string $resource = ExpenseCategoryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array{
        $data['tenant_id']  = Filament::getTenant()->id;

        return $data;
    }
}
