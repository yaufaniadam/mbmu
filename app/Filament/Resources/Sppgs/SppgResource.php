<?php

namespace App\Filament\Resources\Sppgs;

use App\Filament\Resources\Sppgs\Pages\CreateSppg;
use App\Filament\Resources\Sppgs\Pages\EditSppg;
use App\Filament\Resources\Sppgs\Pages\ListSppgs;
use App\Filament\Resources\Sppgs\Schemas\SppgForm;
use App\Filament\Resources\Sppgs\Tables\SppgsTable;
use App\Models\Sppg;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SppgResource extends Resource
{
    protected static ?string $model = Sppg::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return SppgForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SppgsTable::configure($table);
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
            'index' => ListSppgs::route('/'),
            'create' => CreateSppg::route('/create'),
            'edit' => EditSppg::route('/{record}/edit'),
        ];
    }
}
