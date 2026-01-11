<?php

namespace App\Filament\Admin\Resources\HomeFeatures;

use App\Filament\Admin\Resources\HomeFeatures\Pages\CreateHomeFeature;
use App\Filament\Admin\Resources\HomeFeatures\Pages\EditHomeFeature;
use App\Filament\Admin\Resources\HomeFeatures\Pages\ListHomeFeatures;
use App\Filament\Admin\Resources\HomeFeatures\Schemas\HomeFeatureForm;
use App\Filament\Admin\Resources\HomeFeatures\Tables\HomeFeaturesTable;
use App\Models\HomeFeature;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class HomeFeatureResource extends Resource
{
    protected static ?string $model = HomeFeature::class;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['Superadmin', 'Staf Kornas']) ?? false;
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static ?string $recordTitleAttribute = 'title';

    protected static string|\UnitEnum|null $navigationGroup = 'Pengaturan Situs';

    protected static ?string $navigationLabel = 'Fitur Homepage';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return HomeFeatureForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HomeFeaturesTable::configure($table);
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
            'index' => ListHomeFeatures::route('/'),
            'create' => CreateHomeFeature::route('/create'),
            'edit' => EditHomeFeature::route('/{record}/edit'),
        ];
    }
}
