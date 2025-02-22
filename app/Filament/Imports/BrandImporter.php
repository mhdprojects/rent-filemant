<?php

namespace App\Filament\Imports;

use App\Models\Brand;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Filament\Facades\Filament;

class BrandImporter extends Importer
{
    protected static ?string $model = Brand::class;

    public function getData(): array
    {
        return parent::getData(); // TODO: Change the autogenerated stub
    }

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('tenant')
                ->example(Filament::getTenant()->id)
                ->helperText('Tenant ID (Isikan ID ini untuk import)')
                ->requiredMapping()
                ->relationship()
                ->rules(['required']),
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:100']),
        ];
    }

    public function resolveRecord(): ?Brand {
         $data = Brand::firstOrNew([
//             'tenant_id'    => Filament::getTenant()->id,
             'tenant_id'    => $this->data['tenant'],
             'name'         => $this->data['name'],
         ]);

//         $data->tenant_id   = Filament::getTenant()->id;

         return $data;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your brand import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
