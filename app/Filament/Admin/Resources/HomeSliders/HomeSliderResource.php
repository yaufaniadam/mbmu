<?php

namespace App\Filament\Admin\Resources\HomeSliders;

use App\Filament\Admin\Resources\HomeSliders\Pages\CreateHomeSlider;
use App\Filament\Admin\Resources\HomeSliders\Pages\EditHomeSlider;
use App\Filament\Admin\Resources\HomeSliders\Pages\ListHomeSliders;
use App\Filament\Admin\Resources\HomeSliders\Schemas\HomeSliderForm;
use App\Filament\Admin\Resources\HomeSliders\Tables\HomeSlidersTable;
use App\Models\HomeSlider;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class HomeSliderResource extends Resource
{
    protected static ?string $model = HomeSlider::class;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['Superadmin', 'Staf Kornas']) ?? false;
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'title';

    protected static string|\UnitEnum|null $navigationGroup = 'Situs & Konten';

    protected static ?string $navigationLabel = 'Slider Homepage';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return HomeSliderForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HomeSlidersTable::configure($table);
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
            'index' => ListHomeSliders::route('/'),
            'create' => CreateHomeSlider::route('/create'),
            'edit' => EditHomeSlider::route('/{record}/edit'),
        ];
    }
}
