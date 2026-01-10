<?php

namespace App\Filament\Resources\RegistrationTokens;

use App\Filament\Resources\RegistrationTokens\Pages\CreateRegistrationToken;
use App\Filament\Resources\RegistrationTokens\Pages\EditRegistrationToken;
use App\Filament\Resources\RegistrationTokens\Pages\ListRegistrationTokens;
use App\Filament\Resources\RegistrationTokens\Schemas\RegistrationTokenForm;
use App\Filament\Resources\RegistrationTokens\Tables\RegistrationTokensTable;
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

    protected static ?string $navigationLabel = 'Kode Registrasi';
    
    protected static ?string $modelLabel = 'Kode Registrasi';
    
    protected static ?string $pluralModelLabel = 'Kode Registrasi';
    
    protected static string|\UnitEnum|null $navigationGroup = 'Pengaturan';

    public static function form(Schema $schema): Schema
    {
        return RegistrationTokenForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RegistrationTokensTable::configure($table);
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
