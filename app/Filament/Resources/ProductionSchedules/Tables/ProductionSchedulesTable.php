<?php

namespace App\Filament\Resources\ProductionSchedules\Tables;

use App\Models\ProductionSchedule;
use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'Direncanakan' => 'Direncanakan',
                        'Menunggu ACC Kepala SPPG' => 'Menunggu ACC Kepala SPPG',
                        'Terverifikasi' => 'Terverifikasi',
                        'Didistribusikan' => 'Didistribusikan',
                        'Selesai' => 'Selesai',
                    ])
                    ->multiple()
                    ->searchable(),
                
                SelectFilter::make('sppg_id')
                    ->label('SPPG')
                    ->relationship('sppg', 'nama_sppg')
                    ->searchable()
                    ->preload()
                    ->multiple(),
                
                Filter::make('tanggal')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('tanggal_single')
                            ->label('Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['tanggal_single'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal', '=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (!$data['tanggal_single']) {
                            return null;
                        }
                        return 'Tanggal: ' . \Carbon\Carbon::parse($data['tanggal_single'])->format('d/m/Y');
                    }),
                
                Filter::make('tanggal_range')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('dari_tanggal')
                            ->label('Dari Tanggal'),
                        \Filament\Forms\Components\DatePicker::make('sampai_tanggal')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (!$data['dari_tanggal'] && !$data['sampai_tanggal']) {
                            return null;
                        }
                        $from = $data['dari_tanggal'] ? \Carbon\Carbon::parse($data['dari_tanggal'])->format('d/m/Y') : '-';
                        $to = $data['sampai_tanggal'] ? \Carbon\Carbon::parse($data['sampai_tanggal'])->format('d/m/Y') : '-';
                        return "Rentang: {$from} - {$to}";
                    }),
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

                            $schema[] = \Filament\Forms\Components\Radio::make("checklist_results.{$key}")
                                ->label($label)
                                ->options([
                                    'Sesuai' => '✅ Sesuai',
                                    'Tidak Sesuai' => '❌ Tidak Sesuai',
                                    'Perlu Perbaikan' => '⚠️ Perlu Perbaikan'
                                ])
                                ->required()
                                ->inline();
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

                        // Update status to indicate evaluation is done, waiting for Head of SPPG approval
                        $record->update(['status' => 'Menunggu ACC Kepala SPPG']);

                        \Filament\Notifications\Notification::make()
                            ->title('Evaluasi Berhasil Disimpan')
                            ->success()
                            ->send();
                    }),
                \Filament\Actions\Action::make('approve')
                    ->label('Setujui Rencana')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn(ProductionSchedule $record) => \Illuminate\Support\Facades\Auth::user()->hasRole('Kepala SPPG') && $record->status === 'Menunggu ACC Kepala SPPG')
                    ->action(function (ProductionSchedule $record) {
                        $record->update(['status' => 'Terverifikasi']);

                        \Filament\Notifications\Notification::make()
                            ->title('Rencana Distribusi Disetujui')
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
