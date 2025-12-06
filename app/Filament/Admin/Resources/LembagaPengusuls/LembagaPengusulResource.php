<?php

namespace App\Filament\Admin\Resources\LembagaPengusuls;

use App\Filament\Admin\Resources\LembagaPengusuls\Pages\CreateLembagaPengusul;
use App\Filament\Admin\Resources\LembagaPengusuls\Pages\EditLembagaPengusul;
use App\Filament\Admin\Resources\LembagaPengusuls\Pages\ListLembagaPengusuls;
use App\Filament\Admin\Resources\LembagaPengusuls\Schemas\LembagaPengusulForm;
use App\Filament\Admin\Resources\LembagaPengusuls\Tables\LembagaPengusulsTable;
use App\Models\LembagaPengusul;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LembagaPengusulResource extends Resource
{
    protected static ?string $model = LembagaPengusul::class;

    protected static ?string $navigationLabel = 'Lembaga Pengusul';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return LembagaPengusulForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LembagaPengusulsTable::configure($table);
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
            'index' => ListLembagaPengusuls::route('/'),
            'create' => CreateLembagaPengusul::route('/create'),
            'edit' => EditLembagaPengusul::route('/{record}/edit'),
        ];
    }
}
