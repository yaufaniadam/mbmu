<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KepalaSppgTokenResource\Pages;
use App\Filament\Resources\RegistrationTokens\Schemas\RegistrationTokenForm;
use App\Filament\Resources\RegistrationTokens\Tables\KepalaSppgTokensTable;
use App\Models\RegistrationToken;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class KepalaSppgTokenResource extends Resource
{
    protected static ?string $model = RegistrationToken::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedKey;

    protected static ?string $recordTitleAttribute = 'token';

    protected static ?string $navigationLabel = 'Token Kepala SPPG';
    
    protected static ?string $modelLabel = 'Token Kepala SPPG';
    
    protected static ?string $pluralModelLabel = 'Token Kepala SPPG';
    
    protected static string|\UnitEnum|null $navigationGroup = 'SDM & Pengguna';

    // Ensure unique slug so it doesn't conflict with the main resource
    protected static ?string $slug = 'registration-tokens/kepala-sppg';

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->where('role', 'kepala_sppg');
    }

    public static function form(Schema $schema): Schema
    {
        return RegistrationTokenForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KepalaSppgTokensTable::configure($table, showRoleFilter: false);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\KepalaSppgTokenResource\Pages\ListKepalaSppgTokens::route('/'),
            'edit' => \App\Filament\Resources\KepalaSppgTokenResource\Pages\EditKepalaSppgToken::route('/{record}/edit'),
        ];
    }
}
