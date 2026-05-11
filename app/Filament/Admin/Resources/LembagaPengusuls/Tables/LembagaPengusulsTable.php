<?php

namespace App\Filament\Admin\Resources\LembagaPengusuls\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LembagaPengusulsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_lembaga')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('pimpinan.name')
                    ->label('Perwakilan Yayasan')
                    ->sortable()
                    ->searchable()
                    ->wrap(),
                TextColumn::make('pimpinan.last_login_at')
                    ->label('Terakhir Login')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Belum pernah login'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                \Filament\Tables\Filters\Filter::make('never_logged_in')
                    ->label('Belum pernah login')
                    ->query(fn (\Illuminate\Database\Eloquent\Builder $query) => $query->whereHas('pimpinan', fn ($q) => $q->whereNull('last_login_at'))),
            ])
            ->recordActions([
                ViewAction::make()
                    ->visible(fn () => auth()->user()->hasAnyRole(['Superadmin', 'Ketua Kornas', 'Pimpinan Lembaga Pengusul', 'PJ Pelaksana'])),
                EditAction::make()
                    ->visible(fn () => auth()->user()->hasRole('Superadmin')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
