<?php

namespace App\Filament\Admin\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama')->sortable()->searchable(),
                TextColumn::make('roles.name')
                    ->label('Jabatan')
                    ->badge()
                    ->searchable(),
                TextColumn::make('sppg_column')
                    ->label('SPPG')
                    ->state(function (\App\Models\User $record): string {
                        if ($record->hasAnyRole(['Superadmin', 'Staf Kornas', 'Staf Akuntan Kornas', 'Ketua Kornas'])) {
                            return 'Kornas';
                        }

                        // Pimpinan Lembaga Pengusul tidak terkait SPPG
                        if ($record->hasRole('Pimpinan Lembaga Pengusul')) {
                            return '-';
                        }

                        $sppgName = $record->sppgDiKepalai?->nama_sppg 
                            ?? $record->sppgDiPj?->nama_sppg
                            ?? $record->unitTugas->first()?->nama_sppg
                            ?? $record->sppg?->nama_sppg;

                        return $sppgName ?? '-';
                    })
                    ->badge()
                    ->color(fn (string $state): string => $state === 'Kornas' ? 'info' : 'success'),
                TextColumn::make('lembaga_pengusul_column')
                    ->label('Lembaga Pengusul')
                    ->state(function (\App\Models\User $record): string {
                        // Kornas tidak ada Lembaga Pengusul
                        if ($record->hasAnyRole(['Superadmin', 'Staf Kornas', 'Staf Akuntan Kornas', 'Ketua Kornas'])) {
                            return '-';
                        }

                        // Hanya Pimpinan Lembaga Pengusul yang tampilkan Lembaga Pengusul
                        if ($record->hasRole('Pimpinan Lembaga Pengusul')) {
                            return $record->lembagaDipimpin?->nama_lembaga ?? '-';
                        }

                        // User terkait SPPG (PJ, Kepala, Staf) tidak tampilkan Lembaga Pengusul
                        return '-';
                    })
                    ->badge()
                    ->color('warning'),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('roles')
                    ->label('Filter Jabatan')
                    ->relationship('roles', 'name')
                    ->preload()
                    ->searchable(),
                \Filament\Tables\Filters\Filter::make('sppg_filter')
                    ->form([
                        \Filament\Forms\Components\Select::make('sppg_id')
                            ->label('Filter SPPG')
                            ->options(\App\Models\Sppg::pluck('nama_sppg', 'id'))
                            ->searchable()
                            ->preload(),
                    ])
                    ->query(function (\Illuminate\Database\Eloquent\Builder $query, array $data): \Illuminate\Database\Eloquent\Builder {
                        return $query->when(
                            $data['sppg_id'],
                            fn ($query, $sppgId) => $query->where(function ($q) use ($sppgId) {
                                $q->whereHas('unitTugas', fn ($sq) => $sq->where('sppg.id', $sppgId))
                                  ->orWhereHas('sppgDiKepalai', fn ($sq) => $sq->where('id', $sppgId));
                            })
                        );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (! $data['sppg_id']) return null;
                        $sppg = \App\Models\Sppg::find($data['sppg_id']);
                        return 'SPPG: ' . ($sppg?->nama_sppg ?? 'Unknown');
                    }),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
