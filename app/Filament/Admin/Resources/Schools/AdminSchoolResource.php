<?php

namespace App\Filament\Admin\Resources\Schools;

use App\Filament\Admin\Resources\Schools\Pages\CreateSchool;
use App\Filament\Admin\Resources\Schools\Pages\EditSchool;
use App\Filament\Admin\Resources\Schools\Pages\ListSchools;
use App\Filament\Admin\Resources\Schools\Pages\ViewSchool;
use App\Filament\Admin\Resources\Schools\Schemas\SchoolForm;
use App\Filament\Admin\Resources\Schools\Tables\SchoolsTable;
use App\Models\School;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class AdminSchoolResource extends Resource
{
    protected static ?string $model = School::class;

    protected static ?string $slug = 'admin-schools';

    protected static ?string $navigationLabel = 'Sekolah Mitra';

    protected static string|UnitEnum|null $navigationGroup = 'Kelembagaan';

    protected static ?int $navigationSort = 2;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $pluralLabel = 'Sekolah Mitra';

    public static function form(Schema $schema): Schema
    {
        return SchoolForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SchoolsTable::configure($table);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes();
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
            'index' => ListSchools::route('/'),
            'create' => CreateSchool::route('/create'),
            'view' => ViewSchool::route('/{record}'),
            'edit' => EditSchool::route('/{record}/edit'),
        ];
    }
}
