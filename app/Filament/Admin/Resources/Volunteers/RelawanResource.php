<?php

namespace App\Filament\Admin\Resources\Volunteers;

use App\Filament\Admin\Resources\Volunteers\Pages\CreateRelawan;
use App\Filament\Admin\Resources\Volunteers\Pages\EditRelawan;
use App\Filament\Admin\Resources\Volunteers\Pages\ListRelawan;
use App\Filament\Admin\Resources\Volunteers\Pages\ViewRelawan;
use App\Filament\Admin\Resources\Volunteers\Schemas\VolunteerForm;
use App\Filament\Admin\Resources\Volunteers\Tables\VolunteersTable;
use App\Models\Volunteer;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class RelawanResource extends Resource
{
    protected static ?string $model = Volunteer::class;

    protected static ?string $slug = 'volunteers';

    protected static ?string $navigationLabel = 'Relawan SPPG';

    protected static string|UnitEnum|null $navigationGroup = 'SDM & Pengguna';

    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $pluralLabel = 'Relawan SPPG';

    public static function form(Schema $schema): Schema
    {
        return VolunteerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VolunteersTable::configure($table);
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
            'index' => ListRelawan::route('/'),
            'create' => CreateRelawan::route('/create'),
            'view' => ViewRelawan::route('/{record}'),
            'edit' => EditRelawan::route('/{record}/edit'),
        ];
    }
}
