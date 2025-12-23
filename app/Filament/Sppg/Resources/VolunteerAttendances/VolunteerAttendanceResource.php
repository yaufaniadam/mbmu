<?php

namespace App\Filament\Sppg\Resources\VolunteerAttendances;

use App\Filament\Sppg\Resources\VolunteerAttendances\Pages\CreateVolunteerAttendance;
use App\Filament\Sppg\Resources\VolunteerAttendances\Pages\EditVolunteerAttendance;
use App\Filament\Sppg\Resources\VolunteerAttendances\Pages\ListVolunteerAttendances;
use App\Filament\Sppg\Resources\VolunteerAttendances\Schemas\VolunteerAttendanceForm;
use App\Filament\Sppg\Resources\VolunteerAttendances\Tables\VolunteerAttendancesTable;
use App\Models\VolunteerAttendance;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class VolunteerAttendanceResource extends Resource
{
    protected static ?string $model = VolunteerAttendance::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';
    
    protected static ?string $navigationLabel = 'Payroll Relawan';
    
    protected static string|UnitEnum|null $navigationGroup = 'Keuangan';
    
    protected static ?int $navigationSort = 3;
    
    public static function getModelLabel(): string
    {
        return 'Payroll Relawan';
    }
    
    public static function getPluralModelLabel(): string
    {
        return 'Payroll Relawan';
    }
    
    public static function canViewAny(): bool
    {
        return Auth::user()->hasAnyRole([
            'Kepala SPPG',
            'PJ Pelaksana',
            'Staf Akuntan',
        ]);
    }

    public static function form(Schema $schema): Schema
    {
        return VolunteerAttendanceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VolunteerAttendancesTable::configure($table);
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
            'index' => ListVolunteerAttendances::route('/'),
            'create' => CreateVolunteerAttendance::route('/create'),
            'edit' => EditVolunteerAttendance::route('/{record}/edit'),
        ];
    }
}
