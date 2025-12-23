<?php

namespace App\Filament\Sppg\Resources\VolunteerAttendances\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;

class VolunteerAttendancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('volunteer.nama_relawan')
                    ->label('Nama Relawan')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('volunteer.posisi')
                    ->label('Jabatan')
                    ->badge()
                    ->searchable(),
                
                TextColumn::make('period_start')
                    ->label('Periode')
                    ->date('d M Y')
                    ->description(fn ($record) => 'sampai ' . $record->period_end->format('d M Y'))
                    ->sortable(),
                
                TextColumn::make('days_present')
                    ->label('Hadir')
                    ->alignCenter()
                    ->suffix(' hari')
                    ->sortable(),
                
                TextColumn::make('late_minutes')
                    ->label('Terlambat')
                    ->alignCenter()
                    ->suffix(' mnt')
                    ->sortable()
                    ->toggleable(),
                
                TextColumn::make('daily_rate')
                    ->label('Upah/Hari')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->toggleable(),
                
                TextColumn::make('gross_salary')
                    ->label('Gaji Kotor')
                    ->money('IDR', locale: 'id')
                    ->sortable(),
                
                TextColumn::make('late_deduction')
                    ->label('Potongan')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->toggleable(),
                
                TextColumn::make('net_salary')
                    ->label('Gaji Bersih')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->weight('bold')
                    ->color('success'),
                
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('volunteer_id')
                    ->label('Filter Relawan')
                    ->relationship('volunteer', 'nama_relawan')
                    ->searchable()
                    ->preload(),
            ])
            ->defaultSort('period_start', 'desc')
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
