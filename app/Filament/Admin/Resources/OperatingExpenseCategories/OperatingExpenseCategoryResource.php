<?php

namespace App\Filament\Admin\Resources\OperatingExpenseCategories;

use App\Filament\Admin\Resources\OperatingExpenseCategories\Pages;
use App\Models\OperatingExpenseCategory;
use BackedEnum;
use Filament\Forms;
use Filament\Schemas\Schema; // Changed from Form
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;
use UnitEnum;

class OperatingExpenseCategoryResource extends Resource
{
    protected static ?string $model = OperatingExpenseCategory::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationLabel = 'Kategori Biaya Operasional';

    protected static string|UnitEnum|null $navigationGroup = 'Master Keuangan';

    protected static ?int $navigationSort = 2;

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
            'index' => Pages\ListOperatingExpenseCategories::route('/'),
            'create' => Pages\CreateOperatingExpenseCategory::route('/create'),
            'edit' => Pages\EditOperatingExpenseCategory::route('/{record}/edit'),
        ];
    }
}
