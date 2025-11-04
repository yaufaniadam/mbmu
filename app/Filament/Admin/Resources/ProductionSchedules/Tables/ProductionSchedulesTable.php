<?php

namespace App\Filament\Admin\Resources\ProductionSchedules\Tables;

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
                TextColumn::make('jumlah')->label('Jumlah')->sortable()->searchable(),
                TextColumn::make('menu_hari_ini')->label('Menu')->sortable()->searchable(),
            ])
            ->filters([
                //
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
