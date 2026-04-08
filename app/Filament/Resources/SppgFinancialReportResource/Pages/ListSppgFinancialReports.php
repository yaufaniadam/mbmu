<?php

namespace App\Filament\Resources\SppgFinancialReportResource\Pages;

use App\Filament\Resources\SppgFinancialReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSppgFinancialReports extends ListRecords
{
    protected static string $resource = SppgFinancialReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getSubheading(): ?string
    {
        return 'Harap mengunggah Laporan Pelaksanaan/Kegiatan dan Laporan Keuangan SPPG Muhammadiyah secara lengkap, mulai dari periode awal operasional hingga periode berjalan saat ini. Seluruh laporan, baik periode sebelumnya maupun periode berjalan, wajib diunggah.';
    }
}
