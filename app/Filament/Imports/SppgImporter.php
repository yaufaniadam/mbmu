<?php

namespace App\Filament\Imports;

use App\Models\Sppg;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class SppgImporter extends Importer
{
    protected static ?string $model = Sppg::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('kepalaSppg')
                ->relationship(),
            ImportColumn::make('pj_id')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('lembagaPengusul')
                ->relationship(),
            ImportColumn::make('nama_sppg')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('kode_sppg')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('nama_bank')
                ->rules(['max:255']),
            ImportColumn::make('nomor_va')
                ->rules(['max:255']),
            ImportColumn::make('balance')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('alamat')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('is_active')
                ->requiredMapping()
                ->boolean()
                ->rules(['required', 'boolean']),
            ImportColumn::make('status')
                ->rules(['max:255']),
            ImportColumn::make('tanggal_mulai_sewa')
                ->rules(['datetime']),
            ImportColumn::make('tanggal_operasional_pertama')
                ->rules(['date']),
            ImportColumn::make('nomor_ba_verval')
                ->rules(['max:255']),
            ImportColumn::make('tanggal_ba_verval')
                ->rules(['date']),
            ImportColumn::make('ba_verval_path')
                ->rules(['max:255']),
            ImportColumn::make('permohonan_pengusul_path')
                ->rules(['max:255']),
            ImportColumn::make('province_code')
                ->rules(['max:2']),
            ImportColumn::make('city_code')
                ->rules(['max:4']),
            ImportColumn::make('district_code')
                ->rules(['max:7']),
            ImportColumn::make('village_code')
                ->rules(['max:10']),
            ImportColumn::make('latitude'),
            ImportColumn::make('longitude'),
            ImportColumn::make('photo_path')
                ->rules(['max:255']),
            ImportColumn::make('grade'),
            ImportColumn::make('izin_operasional_path')
                ->rules(['max:255']),
            ImportColumn::make('sertifikat_halal_path')
                ->rules(['max:255']),
            ImportColumn::make('slhs_path')
                ->rules(['max:255']),
            ImportColumn::make('lhaccp_path')
                ->rules(['max:255']),
            ImportColumn::make('iso_path')
                ->rules(['max:255']),
            ImportColumn::make('sertifikat_lahan_path')
                ->rules(['max:255']),
            ImportColumn::make('dokumen_lain_path')
                ->rules(['max:255']),
            ImportColumn::make('facebook')
                ->rules(['max:255']),
            ImportColumn::make('instagram')
                ->rules(['max:255']),
            ImportColumn::make('youtube')
                ->rules(['max:255']),
            ImportColumn::make('tiktok')
                ->rules(['max:255']),
        ];
    }

    public function resolveRecord(): Sppg
    {
        return Sppg::firstOrNew([
            'kode_sppg' => $this->data['kode_sppg'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your sppg import has completed and ' . Number::format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
