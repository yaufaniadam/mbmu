<?php

namespace App\Filament\Resources\ProductionSchedules\Tables;

use App\Models\ProductionSchedule;
use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductionSchedulesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->getStateUsing(
                        fn($record) =>
                        Carbon::parse($record->tanggal)
                            ->locale('id')
                            ->translatedFormat('l, d F Y')
                    )
                    ->sortable(),
                // TextColumn::make('jumlah_porsi_besar')
                //     ->label('porsi besar')
                //     ->getStateUsing(function (ProductionSchedule $record): int|float {
                //         return $record->total_porsi_besar;
                //     }),
                // TextColumn::make('jumlah_porsi_kecil')
                //     ->label('porsi kecil')
                //     ->getStateUsing(function (ProductionSchedule $record): int|float {
                //         return $record->total_porsi_kecil;
                //     }),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Direncanakan' => 'warning',
                        'Menunggu ACC Kepala SPPG' => 'info',
                        'Terverifikasi' => 'success',
                        'Didistribusikan' => 'warning',
                        'Selesai' => 'success',
                        default => 'gray',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'Direncanakan' => 'heroicon-m-clipboard-document-list',
                        'Menunggu ACC Kepala SPPG' => 'heroicon-m-clock',
                        'Terverifikasi' => 'heroicon-m-document-check',
                        'Didistribusikan' => 'heroicon-m-truck',
                        'Selesai' => 'heroicon-m-check-circle',
                        default => null,
                    }),
                TextColumn::make('sppg.nama_sppg')->label('SPPG'),
                TextColumn::make('menu_hari_ini')->label('Menu')->sortable()->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->hidden(fn () => \Filament\Facades\Filament::getCurrentPanel()->getId() === 'admin'),
                \Filament\Actions\Action::make('evaluate')
                    ->label('Evaluasi')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->color('success')
                    ->visible(fn(ProductionSchedule $record) => \Illuminate\Support\Facades\Auth::user()->hasAnyRole(['Ahli Gizi', 'Staf Gizi']) && !$record->verification)
                    ->form(function () {
                        $settings = \App\Models\ProductionVerificationSetting::first();
                        $checklist = $settings?->checklist_data ?? [];

                        $schema = [];
                        foreach ($checklist as $item) {
                            $label = $item['item_name'] ?? 'Kriteria';
                            $key = \Illuminate\Support\Str::slug($label); 

                            $schema[] = \Filament\Forms\Components\Select::make("checklist_results.{$key}")
                                ->label($label)
                                ->options([
                                    'Sesuai' => 'Sesuai',
                                    'Tidak Sesuai' => 'Tidak Sesuai',
                                    'Perlu Perbaikan' => 'Perlu Perbaikan'
                                ])
                                ->required()
                                ->native(false);
                        }
                        
                        $schema[] = \Filament\Forms\Components\Textarea::make('notes')
                            ->label('Catatan Tambahan');

                        return $schema;
                    })
                    ->action(function (ProductionSchedule $record, array $data) {
                        $settings = \App\Models\ProductionVerificationSetting::first();
                        $originalChecklist = $settings?->checklist_data ?? [];
                        
                        $formattedResults = [];
                        foreach ($originalChecklist as $item) {
                            $label = $item['item_name'];
                            $key = \Illuminate\Support\Str::slug($label);
                            $status = $data['checklist_results'][$key] ?? null;
                            
                            $formattedResults[] = [
                                'item' => $label,
                                'status' => $status,
                                'keterangan' => null
                            ];
                        }

                        \App\Models\ProductionVerification::create([
                            'production_schedule_id' => $record->id,
                            'sppg_id' => $record->sppg_id,
                            'user_id' => \Illuminate\Support\Facades\Auth::id(),
                            'date' => now(),
                            'checklist_results' => $formattedResults,
                            'notes' => $data['notes'] ?? null,
                        ]);

                        // Update status to indicate evaluation is done
                        $record->update(['status' => 'Terverifikasi']);

                        \Filament\Notifications\Notification::make()
                            ->title('Evaluasi Berhasil Disimpan')
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
