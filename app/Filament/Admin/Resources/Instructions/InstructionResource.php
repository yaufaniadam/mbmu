<?php

namespace App\Filament\Admin\Resources\Instructions;

use App\Filament\Admin\Resources\Instructions\Pages\CreateInstruction;
use App\Filament\Admin\Resources\Instructions\Pages\EditInstruction;
use App\Filament\Admin\Resources\Instructions\Pages\ListInstructions;
use App\Filament\Admin\Resources\Instructions\Schemas\InstructionForm;
use App\Filament\Admin\Resources\Instructions\Tables\InstructionTable;
use App\Models\Instruction;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class InstructionResource extends Resource
{
    protected static ?string $model = Instruction::class;

    protected static ?string $modelLabel = 'Instruksi';
    
    protected static ?string $pluralModelLabel = 'Instruksi';

    protected static ?string $navigationLabel = 'Instruksi';

    protected static string|UnitEnum|null $navigationGroup = null;

    protected static ?int $navigationSort = 9;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-megaphone';

    public static function shouldRegisterNavigation(): bool
    {
        if (Auth::user()?->hasRole('Staf Akuntan Kornas')) {
            return false;
        }

        // Hide from Lembaga Pengusul as they are recipients, not admins
        if (Auth::user()?->hasAnyRole(['Pimpinan Lembaga Pengusul', 'PJ Pelaksana'])) {
            return false;
        }

        return true;
    }

    public static function form(Schema $schema): Schema
    {
        return InstructionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InstructionTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInstructions::route('/'),
            'create' => CreateInstruction::route('/create'),
            'edit' => EditInstruction::route('/{record}/edit'),
        ];
    }
}
