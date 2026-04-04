<?php

namespace App\Filament\Admin\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Models\LembagaPengusul;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama')->sortable()->searchable(),
                TextColumn::make('email')->label('Email')->sortable()->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('lembagaDipimpin.nama_lembaga')
                    ->label('Nama Lembaga')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('-'),
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

                        $sppgName = $record->sppgDiKepalai?->nama_sppg 
                            ?? $record->sppgDiPj?->nama_sppg
                            ?? $record->unitTugas->first()?->nama_sppg
                            ?? $record->sppg?->nama_sppg;

                        if ($sppgName) {
                            return $sppgName;
                        }

                        // Pimpinan Lembaga Pengusul tidak terkait SPPG unless they have another role
                        if ($record->hasRole('Pimpinan Lembaga Pengusul')) {
                            return '-';
                        }

                        return '-';
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
                \Filament\Tables\Filters\Filter::make('lembaga_filter')
                    ->form([
                        \Filament\Forms\Components\Select::make('lembaga_id')
                            ->label('Filter Lembaga Pengusul')
                            ->options(LembagaPengusul::pluck('nama_lembaga', 'id'))
                            ->searchable()
                            ->preload(),
                    ])
                    ->query(function (\Illuminate\Database\Eloquent\Builder $query, array $data): \Illuminate\Database\Eloquent\Builder {
                        return $query->when(
                            $data['lembaga_id'],
                            fn ($query, $lembagaId) => $query->whereHas('lembagaDipimpin', fn ($sq) => $sq->where('id', $lembagaId))
                        );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (! $data['lembaga_id']) return null;
                        $lembaga = LembagaPengusul::find($data['lembaga_id']);
                        return 'Lembaga Pengusul: ' . ($lembaga?->nama_lembaga ?? 'Unknown');
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
