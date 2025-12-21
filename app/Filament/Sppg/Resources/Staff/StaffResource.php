<?php

namespace App\Filament\Sppg\Resources\Staff;

use App\Filament\Sppg\Resources\Staff\Pages\CreateStaff;
use App\Filament\Sppg\Resources\Staff\Pages\EditStaff;
use App\Filament\Sppg\Resources\Staff\Pages\ListStaff;
use App\Filament\Sppg\Resources\Staff\Schemas\StaffForm;
use App\Filament\Sppg\Resources\Staff\Tables\StaffTable;
use App\Models\User;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class StaffResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationLabel = 'Staff'; // ✅ label shown in sidebar

    protected static string|UnitEnum|null $navigationGroup = 'Data Master';

    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'staff';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function canAccess(): bool
    {
        $user = Auth::user();
        return $user && $user->hasAnyRole([
            'Kepala SPPG',
            'Staf Administrator SPPG'
        ]);
    }

    public static function canCreate(): bool
    {
        $user = Auth::user();
        return $user && $user->hasAnyRole([
            'Kepala SPPG',
            'Staf Administrator SPPG'
        ]);
    }

    public static function form(Schema $schema): Schema
    {
        return StaffForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StaffTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStaff::route('/'),
            'create' => CreateStaff::route('/create'),
            'edit' => EditStaff::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return 'Staff'; // ✅ used in page titles like “Edit Staff”
    }

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();
        if ($user->hasRole('Kepala SPPG')) {
            $sppg = User::find($user->id)->sppgDikepalai;

            if (!$sppg) {
                return parent::getEloquentQuery()->whereRaw('1 = 0');
            }

            return parent::getEloquentQuery()
                ->whereHas('unitTugas', function (Builder $query) use ($sppg) {
                    $query->where('sppg_id', $sppg->id);
                });
        }

        if ($user->hasRole('Staf Administrator SPPG')) {
            $unitTugas = User::find($user->id)->unitTugas->first();

            if (!$unitTugas) {
                return parent::getEloquentQuery()->whereRaw('1 = 0');
            }

            return parent::getEloquentQuery()
                ->whereHas('unitTugas', function (Builder $query) use ($unitTugas) {
                    $query->where('sppg_id', $unitTugas->id);
                });
        }

        return parent::getEloquentQuery();
    }
}
