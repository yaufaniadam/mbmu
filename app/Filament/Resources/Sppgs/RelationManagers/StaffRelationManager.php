<?php

namespace App\Filament\Resources\Sppgs\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Schema;

class StaffRelationManager extends RelationManager
{
    protected static string $relationship = 'staff';

    protected static ?string $title = 'Pejabat / Staff SPPG';

    protected static ?string $modelLabel = 'Staff';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                //
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Role')
                    ->badge()
                    ->color('warning')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pivot.sk_path')
                    ->label('SK')
                    ->formatStateUsing(fn () => '')
                    ->icon('heroicon-m-arrow-down-tray')
                    ->color('primary')
                    ->url(fn ($record) => $record->pivot->sk_path)
                    ->openUrlInNewTab()
                    ->placeholder('-')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                //
            ]);
    }
}
