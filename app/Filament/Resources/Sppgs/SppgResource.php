<?php

namespace App\Filament\Resources\Sppgs;

use App\Filament\Resources\Sppgs\Pages\CreateSppg;
use App\Filament\Resources\Sppgs\Pages\EditSppg;
use App\Filament\Resources\Sppgs\Pages\ListSppgs;
use App\Filament\Resources\Sppgs\Schemas\SppgForm;
use App\Filament\Resources\Sppgs\Tables\SppgsTable;
use App\Models\Sppg;
use App\Models\User;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class SppgResource extends Resource
{
    protected static ?string $model = Sppg::class;

    protected static ?string $navigationLabel = 'Unit SPPG';

    protected static string|UnitEnum|null $navigationGroup = 'Kelembagaan';

    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    public static function canCreate(): bool
    {
        return false;
    }

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

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();
        if ($user->hasRole('Pimpinan Lembaga Pengusul')) {
            $sppg = User::find($user->id)->lembagaDipimpin;

            return parent::getEloquentQuery()
                ->where('lembaga_pengusul_id', $sppg->id);
        }

        return parent::getEloquentQuery();
    }
}
