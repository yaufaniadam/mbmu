<?php

namespace App\Filament\Sppg\Resources\Volunteers;

use App\Filament\Sppg\Resources\Volunteers\Pages\CreateVolunteer;
use App\Filament\Sppg\Resources\Volunteers\Pages\EditVolunteer;
use App\Filament\Sppg\Resources\Volunteers\Pages\ListVolunteers;
use App\Filament\Sppg\Resources\Volunteers\Pages\ViewVolunteer;
use App\Filament\Sppg\Resources\Volunteers\Schemas\VolunteerForm;
use App\Filament\Sppg\Resources\Volunteers\Schemas\VolunteerInfolist;
use App\Filament\Sppg\Resources\Volunteers\Tables\VolunteersTable;
use App\Models\User;
use App\Models\Volunteer;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class VolunteerResource extends Resource
{
    protected static ?string $model = Volunteer::class;

    protected static ?string $navigationLabel = 'Relawan';

    protected static string|UnitEnum|null $navigationGroup = 'Data Master';

    protected static ?int $navigationSort = 3;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function getModelLabel(): string
    {
        return 'Relawan';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Relawan';
    }

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
        return VolunteerForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return VolunteerInfolist::configure($schema);
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
            'index' => ListVolunteers::route('/'),
            'create' => CreateVolunteer::route('/create'),
            'view' => ViewVolunteer::route('/{record}'),
            'edit' => EditVolunteer::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        $query = parent::getEloquentQuery()->with(['user']);

        if ($user->hasRole('Kepala SPPG')) {
            $sppg = $user->sppgDiKepalai;

            if (!$sppg) {
                return $query->whereRaw('1 = 0');
            }

            return $query->where('sppg_id', $sppg->id);
        }

        if ($user->hasAnyRole(['PJ Pelaksana', 'Ahli Gizi', 'Staf Administrator SPPG', 'Staf Akuntan', 'Staf Gizi', 'Staf Pengantaran'])) {
            $unitTugas = $user->unitTugas->first();

            if (!$unitTugas) {
                return $query->whereRaw('1 = 0');
            }

            return $query->where('sppg_id', $unitTugas->id);
        }

        return $query;
    }
}
