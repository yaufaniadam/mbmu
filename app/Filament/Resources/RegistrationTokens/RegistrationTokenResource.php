<?php

namespace App\Filament\Resources\RegistrationTokens;

use App\Filament\Resources\RegistrationTokens\Pages\CreateRegistrationToken;
use App\Filament\Resources\RegistrationTokens\Pages\EditRegistrationToken;
use App\Filament\Resources\RegistrationTokens\Pages\ListRegistrationTokens;
use App\Filament\Resources\RegistrationTokens\Schemas\PimpinanTokenForm;
use App\Filament\Resources\RegistrationTokens\Tables\PimpinanTokensTable;
use App\Models\RegistrationToken;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RegistrationTokenResource extends Resource
{
    protected static ?string $model = RegistrationToken::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedKey;

    protected static ?string $recordTitleAttribute = 'token';

    protected static ?string $navigationLabel = 'Token Pimpinan';
    
    protected static ?string $modelLabel = 'Token Pimpinan';
    
    protected static ?string $pluralModelLabel = 'Token Pimpinan';
    
    protected static string|\UnitEnum|null $navigationGroup = 'Pengaturan';
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->where('role', 'kepala_lembaga');
    }

    public static function form(Schema $schema): Schema
    {
        return PimpinanTokenForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PimpinanTokensTable::configure($table, showRoleFilter: false);
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
            'index' => ListRegistrationTokens::route('/'),
            'create' => CreateRegistrationToken::route('/create'),
            'edit' => EditRegistrationToken::route('/{record}/edit'),
        ];
    }
}
