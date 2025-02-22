<?php

namespace App\Filament\App\Resources\ExpenseResource\Pages;

use App\Filament\App\Resources\ExpenseResource;
use App\Helper\AutoCode;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateExpense extends CreateRecord{
    protected static string $resource = ExpenseResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array{
        $data['tenant_id']  = Filament::getTenant()->id;
        $data['number']     = AutoCode::expenseNumber($data['tenant_id']);

        return $data;
    }
}
