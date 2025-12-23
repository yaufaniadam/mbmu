<?php

namespace App\Filament\Admin\Resources\Volunteers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VolunteersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_relawan')
                    ->label('Nama Relawan')
                    ->description(fn($record) => "NIK: " . ($record->nik ?? '-'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('posisi')
                    ->label('Jabatan')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Masak', 'Koordinator Masak' => 'warning',
                        'Asisten Dapur', 'Persiapan' => 'info',
                        'Pengantaran', 'Kurir' => 'success',
                        'Kebersihan' => 'gray',
                        'Keamanan' => 'danger',
                        'Administrasi', 'Koordinator Bahan' => 'primary',
                        'Asisten Lapangan' => 'success',
                        default => 'gray',
                    })
                    ->sortable()
                    ->searchable(),
                TextColumn::make('daily_rate')
                    ->label('Upah/Hari')
                    ->money('IDR', locale: 'id')
                    ->sortable(),
                TextColumn::make('sppg.nama_sppg')
                    ->label('Unit SPPG')
                    ->sortable(),
                TextColumn::make('kontak')
                    ->label('Kontak'),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('category')
                    ->label('Kategori')
                    ->options([
                        'Masak' => 'Juru Masak',
                        'Asisten Dapur' => 'Asisten Dapur',
                        'Pengantaran' => 'Staf Pengantaran',
                        'Kebersihan' => 'Tenaga Kebersihan',
                        'Keamanan' => 'Tenaga Keamanan',
                        'Administrasi' => 'Staf Administrasi',
                        'Lainnya' => 'Lainnya',
                    ]),
                \Filament\Tables\Filters\SelectFilter::make('sppg_id')
                    ->label('Unit SPPG')
                    ->relationship('sppg', 'nama_sppg'),
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
