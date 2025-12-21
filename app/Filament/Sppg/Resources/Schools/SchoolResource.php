<?php

namespace App\Filament\Sppg\Resources\Schools;

use App\Filament\Sppg\Resources\Schools\Pages\CreateSchool;
use App\Filament\Sppg\Resources\Schools\Pages\EditSchool;
use App\Filament\Sppg\Resources\Schools\Pages\ListSchools;
use App\Filament\Sppg\Resources\Schools\Pages\ViewSchool;
use App\Filament\Sppg\Resources\Schools\Schemas\SchoolForm;
use App\Filament\Sppg\Resources\Schools\Schemas\SchoolInfolist;
use App\Filament\Sppg\Resources\Schools\Tables\SchoolsTable;
use App\Models\School;
use App\Models\User;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class SchoolResource extends Resource
{
    protected static ?string $model = School::class;

    protected static ?string $navigationLabel = 'Daftar Sekolah Penerima SPPG';

    protected static string|UnitEnum|null $navigationGroup = 'Data Master';

    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function canAccess(): bool
    {
        $user = Auth::user();
        return $user && $user->hasAnyRole([
            'Kepala SPPG',
            'PJ Pelaksana',
            'Staf Administrator SPPG',
            'Ahli Gizi',
            'Staf Gizi',
            'Staf Akuntan',
            'Staf Pengantaran'
        ]);
    }

    public static function canCreate(): bool
    {
        $user = Auth::user();
        return $user && $user->hasAnyRole([
            'Kepala SPPG',
            'PJ Pelaksana',
            'Staf Administrator SPPG'
        ]);
    }

    public static function form(Schema $schema): Schema
    {
        return SchoolForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SchoolInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SchoolsTable::configure($table);
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

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();
        if ($user->hasRole('Kepala SPPG')) {
            $sppg = User::find($user->id)->sppgDikepalai;

            if (!$sppg) {
                return parent::getEloquentQuery()->whereRaw('1 = 0');
            }

            return parent::getEloquentQuery()->where('sppg_id', $sppg->id);
        }

        if ($user->hasAnyRole(['PJ Pelaksana', 'Ahli Gizi', 'Staf Administrator SPPG', 'Staf Akuntan', 'Staf Gizi', 'Staf Pengantaran'])) {
            $unitTugas = User::find($user->id)->unitTugas->first();

            if (!$unitTugas) {
                return parent::getEloquentQuery()->whereRaw('1 = 0');
            }

            return parent::getEloquentQuery()->where('sppg_id', $unitTugas->id);
        }

        return parent::getEloquentQuery();
    }
}
