<?php

namespace App\Filament\Imports;

use App\Models\Holiday;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class HolidayImporter extends Importer
{
    protected static ?string $model = Holiday::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('tanggal')
                ->requiredMapping()
                ->rules(['required', 'date']),
            ImportColumn::make('nama')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
        ];
    }

    public function resolveRecord(): Holiday
    {
        return new Holiday();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Import hari libur selesai. ' . Number::format($import->successful_rows) . ' baris berhasil diimport.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' baris gagal diimport.';
        }

        return $body;
    }
}
