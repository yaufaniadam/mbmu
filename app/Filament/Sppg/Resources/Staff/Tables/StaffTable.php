<?php

namespace App\Filament\Sppg\Resources\Staff\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class StaffTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama')->sortable()->searchable(),
                TextColumn::make('roles.name')
                    ->label('Role')
                    ->badge()
                    // ->sortable()
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('roles')
                    ->label('Role')
                    ->options(DB::table('roles')->pluck('name', 'name')->toArray())
                    ->query(function ($query, $data) {
                        if ($data['value']) {
                            $query->whereHas('roles', fn($q) => $q->where('name', $data['value']));
                        }
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
