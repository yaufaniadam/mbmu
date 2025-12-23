<?php

namespace App\Filament\Sppg\Resources\VolunteerDailyAttendances\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;

class VolunteerDailyAttendancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('attendance_date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable()
                    ->searchable(),
                
                TextColumn::make('volunteer.nama_relawan')
                    ->label('Nama Relawan')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('volunteer.posisi')
                    ->label('Jabatan')
                    ->badge()
                    ->color('gray')
                    ->searchable(),
                
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Hadir' => 'success',
                        'Izin' => 'warning',
                        'Sakit' => 'danger',
                        'Alpha' => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'Hadir' => 'heroicon-o-check-circle',
                        'Izin' => 'heroicon-o-document-text',
                        'Sakit' => 'heroicon-o-heart',
                        'Alpha' => 'heroicon-o-x-circle',
                    })
                    ->sortable(),
                
                TextColumn::make('notes')
                    ->label('Catatan')
                    ->limit(30)
                    ->toggleable(),
                
                TextColumn::make('recordedBy.name')
                    ->label('Dicatat Oleh')
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'Hadir' => 'Hadir',
                        'Izin' => 'Izin',
                        'Sakit' => 'Sakit',
                        'Alpha' => 'Alpha',
                    ]),
                
                SelectFilter::make('volunteer_id')
                    ->label('Relawan')
                    ->relationship('volunteer', 'nama_relawan')
                    ->searchable()
                    ->preload(),
                
                Filter::make('attendance_date')
                    ->form([
                        DatePicker::make('date_from')->label('Dari Tanggal'),
                        DatePicker::make('date_until')->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('attendance_date', '>=', $date),
                            )
                            ->when(
                                $data['date_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('attendance_date', '<=', $date),
                            );
                    }),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('attendance_date', 'desc');
    }
}
