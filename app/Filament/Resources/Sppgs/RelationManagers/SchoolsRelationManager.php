<?php

namespace App\Filament\Resources\Sppgs\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Schema;

class SchoolsRelationManager extends RelationManager
{
    protected static string $relationship = 'schools';

    protected static ?string $title = 'Daftar Penerima MBM';

    protected static ?string $modelLabel = 'Penerima MBM';

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
            ->recordTitleAttribute('nama_sekolah')
            ->columns([
                Tables\Columns\TextColumn::make('nama_sekolah')
                    ->label('Nama Penerima')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('alamat')
                    ->label('Alamat')
                    ->wrap()
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
