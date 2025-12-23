<?php

namespace App\Filament\Sppg\Resources\VolunteerDailyAttendances;

use App\Filament\Sppg\Resources\VolunteerDailyAttendances\Pages\CreateVolunteerDailyAttendance;
use App\Filament\Sppg\Resources\VolunteerDailyAttendances\Pages\EditVolunteerDailyAttendance;
use App\Filament\Sppg\Resources\VolunteerDailyAttendances\Pages\ListVolunteerDailyAttendances;
use App\Filament\Sppg\Resources\VolunteerDailyAttendances\Schemas\VolunteerDailyAttendanceForm;
use App\Filament\Sppg\Resources\VolunteerDailyAttendances\Tables\VolunteerDailyAttendancesTable;
use App\Models\VolunteerDailyAttendance;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class VolunteerDailyAttendanceResource extends Resource
{
    protected static ?string $model = VolunteerDailyAttendance::class;
    
    protected static ?string $navigationLabel = 'Data Presensi';
    
    protected static string|\UnitEnum|null $navigationGroup = 'Keuangan';
    
    protected static ?int $navigationSort = 3;
    
    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-calendar-days';
    }
    
    public static function getModelLabel(): string
    {
        return 'Presensi Harian';
    }
    
    public static function getPluralModelLabel(): string
    {
        return 'Presensi Harian';
    }
    
    public static function canViewAny(): bool
    {
        return Auth::user()->hasAnyRole([
            'Kepala SPPG',
            'PJ Pelaksana',
        ]);
    }

    public static function form(Schema $schema): Schema
    {
        return VolunteerDailyAttendanceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VolunteerDailyAttendancesTable::configure($table);
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
            'index' => ListVolunteerDailyAttendances::route('/'),
            'create' => CreateVolunteerDailyAttendance::route('/create'),
            'edit' => EditVolunteerDailyAttendance::route('/{record}/edit'),
        ];
    }
}
