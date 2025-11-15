<?php

namespace App\Filament\Resources\ProductionSchedules\Schemas;

use App\Models\ProductionSchedule;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class ProductionScheduleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tanggal')
                    ->label('Tanggal')
                    ->date(),
                TextEntry::make('menu_hari_ini')
                    ->label('Menu Hari Ini'),
                ViewEntry::make('verification.checklist_data')
                    ->label('Hasil Verifikasi Ceklis')
                    ->columnSpanFull()
                    // Tentukan path ke file Blade kustom Anda
                    ->view('infolists.components.checklist-items'),
                TextEntry::make('status')
                    ->label('Status')
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
                    })
                    ->icon(static function (string $state): string {
                        return match ($state) {
                            'Direncanakan' => 'heroicon-o-clock',
                            'Menunggu ACC Kepala SPPG' => 'heroicon-o-arrow-path',
                            'Terverifikasi' => 'heroicon-o-paper-airplane',
                            'Didistribusikan' => 'heroicon-o-truck',
                            'Selesai' => 'heroicon-o-check-circle',
                            'Dibatalkan' => 'heroicon-o-x-circle',
                            default => 'heroicon-o-question-mark-circle',
                        };
                    }),

                Actions::make([
                    Action::make('markAsVerified')
                        ->label('Setujui')
                        ->icon('heroicon-o-check-circle')
                        ->color('info')
                        ->action(function (ProductionSchedule $record) {
                            $record->update(['status' => 'Terverifikasi']);
                            Notification::make()
                                ->title('Status Diperbarui')
                                ->body('Jadwal produksi telah ditandai sebagai "Terverifikasi".')
                                ->success()
                                ->send();

                            return redirect(request()->header('Referer'));
                        })
                        ->authorize(function (ProductionSchedule $record): bool {
                            $statusCondition = $record->status !== 'Terverifikasi';

                            if (! $statusCondition) {
                                return false; // Hide if status is Terverifikasi
                            }

                            // Role Condition
                            $user = Auth::user();
                            if (! $user) {
                                return false; // Hide if no user
                            }

                            // Only display when one of either Kepala SPPG or PJ Pelaksana
                            return $user->hasRole('Kepala SPPG') || $user->hasRole('PJ Pelaksana');
                        }),
                ])
                    ->columnSpanFull(),
            ])
            ->columns(1);
    }
}
