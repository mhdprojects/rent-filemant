<?php

namespace App\Filament\Imports;

use App\Models\TypeModel;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class TypeModelImporter extends Importer
{
    protected static ?string $model = TypeModel::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('tenant')
                ->requiredMapping()
                ->relationship()
                ->rules(['required']),
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:100']),
            ImportColumn::make('brand')
                ->requiredMapping()
                ->relationship()
                ->rules(['required']),
        ];
    }

    public function resolveRecord(): ?TypeModel
    {
        // return TypeModel::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new TypeModel();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your type model import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
