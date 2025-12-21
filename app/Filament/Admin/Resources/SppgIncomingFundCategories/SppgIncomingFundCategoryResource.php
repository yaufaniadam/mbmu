<?php

namespace App\Filament\Admin\Resources\SppgIncomingFundCategories;

use App\Filament\Admin\Resources\SppgIncomingFundCategories\Pages;
use App\Models\SppgIncomingFundCategory;
use BackedEnum;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;
use UnitEnum;

class SppgIncomingFundCategoryResource extends Resource
{
    protected static ?string $model = SppgIncomingFundCategory::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-tag';
    
    protected static ?string $navigationLabel = 'Kategori Dana Masuk';
    
    protected static string|UnitEnum|null $navigationGroup = 'Master Keuangan';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Actions\EditAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSppgIncomingFundCategories::route('/'),
            'create' => Pages\CreateSppgIncomingFundCategory::route('/create'),
            'edit' => Pages\EditSppgIncomingFundCategory::route('/{record}/edit'),
        ];
    }
}
