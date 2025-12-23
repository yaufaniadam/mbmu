<?php

namespace App\Filament\Resources\ProductionSchedules\Schemas;

use App\Models\ProductionSchedule;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class ProductionScheduleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)
                    ->schema([
                        TextEntry::make('tanggal')
                            ->label('Tanggal Produksi')
                            ->date('d F Y')
                            ->icon('heroicon-o-calendar'),
                        TextEntry::make('menu_hari_ini')
                            ->label('Menu Hari Ini')
                            ->icon('heroicon-o-document-text'),
                        TextEntry::make('status')
                            ->label('Status Jadwal')
                            ->badge()
                            ->color(static function (string $state): string {
                                return match ($state) {
                                    'Direncanakan' => 'warning',
                                    'Menunggu ACC Kepala SPPG' => 'info',
                                    'Terverifikasi' => 'info',
                                    'Didistribusikan' => 'info',
                                    'Selesai' => 'success',
                                    'Dibatalkan' => 'danger',
                                    default => 'gray',
                                };
                            }),
                    ]),

                Section::make('Hasil Evaluasi Mandiri')
                    ->icon('heroicon-m-clipboard-document-check')
                    ->schema([
                        ViewEntry::make('verification.checklist_results')
                            ->label('')
                            ->view('infolists.components.checklist-items'),
                        
                        TextEntry::make('verification.notes')
                            ->label('Catatan Evaluasi')
                            ->placeholder('Tidak ada catatan tambahan.')
                            ->visible(fn($record) => $record->verification?->notes !== null)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                // Section 'Status Distribusi' removed to avoid redundancy

            ])
            ->columns(1);
    }
}
