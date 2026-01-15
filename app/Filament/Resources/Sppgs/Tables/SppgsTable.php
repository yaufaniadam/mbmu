<?php

namespace App\Filament\Resources\Sppgs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SppgsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_sppg')
                    ->label('ID SPPG')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),
                TextColumn::make('nama_sppg')
                    ->label('Nama')
                    ->sortable()
                    ->searchable()
                    ->wrap(),
                TextColumn::make('lembagaPengusul.nama_lembaga')
                    ->label('Lembaga Pengusul')
                    ->sortable()
                    ->searchable()
                    ->wrap(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'Operasional / Siap Berjalan' => 'success',
                        'Proses Persiapan' => 'warning',
                        'Verifikasi dan Validasi' => 'info',
                        default => 'gray',
                    })
                    ->sortable(),
                \Filament\Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('grade')->label('Akreditasi')->sortable(),
                TextColumn::make('kepalaSppg.name')
                    ->label('Kepala SPPG')
                    ->searchable()
                    ->wrap(),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('province_code')
                    ->label('Provinsi')
                    ->relationship('province', 'name')
                    ->searchable()
                    ->preload(),
                \Filament\Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'Operasional / Siap Berjalan' => 'Operasional / Siap Berjalan',
                        'Proses Persiapan' => 'Proses Persiapan',
                        'Verifikasi dan Validasi' => 'Verifikasi dan Validasi',
                    ]),
                \Filament\Tables\Filters\SelectFilter::make('grade')
                    ->label('Akreditasi')
                    ->options([
                        'A' => 'A',
                        'B' => 'B',
                        'C' => 'C',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
