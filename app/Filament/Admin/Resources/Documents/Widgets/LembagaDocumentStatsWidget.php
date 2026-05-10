<?php

namespace App\Filament\Admin\Resources\Documents\Widgets;

use App\Models\LembagaPengusul;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LembagaDocumentStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $allLembaga = LembagaPengusul::withCount('documents')->get();
        $totalLembaga = $allLembaga->count();
        
        $unfilledLembaga = $allLembaga->filter(fn($l) => $l->documents_count === 0)->count();
        $filledLembaga = $totalLembaga - $unfilledLembaga;

        return [
            Stat::make('Total Lembaga Pengusul', $totalLembaga)
                ->description('Total pendaftaran yayasan')
                ->icon('heroicon-m-building-library'),
            Stat::make('Belum Upload Dokumen', $unfilledLembaga)
                ->description('Yayasan yang belum upload berkas')
                ->color('danger')
                ->icon('heroicon-m-document-minus'),
            Stat::make('Sudah Upload Dokumen', $filledLembaga)
                ->description('Yayasan yang sudah ada berkas')
                ->color('success')
                ->icon('heroicon-m-document-check'),
        ];
    }
}
