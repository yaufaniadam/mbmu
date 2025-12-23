<?php

namespace App\Filament\Admin\Resources\LembagaPengusuls;

use App\Filament\Admin\Resources\LembagaPengusuls\Pages\CreateLembagaPengusul;
use App\Filament\Admin\Resources\LembagaPengusuls\Pages\EditLembagaPengusul;
use App\Filament\Admin\Resources\LembagaPengusuls\Pages\ListLembagaPengusuls;
use App\Filament\Admin\Resources\LembagaPengusuls\Pages\ViewLembagaPengusul;
use App\Filament\Admin\Resources\LembagaPengusuls\Schemas\LembagaPengusulForm;
use App\Filament\Admin\Resources\LembagaPengusuls\Tables\LembagaPengusulsTable;
use App\Models\LembagaPengusul;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;

class LembagaPengusulResource extends Resource
{
    protected static ?string $model = LembagaPengusul::class;

    protected static ?string $navigationLabel = 'Lembaga Pengusul';

    protected static string|UnitEnum|null $navigationGroup = 'Kelembagaan';

    protected static ?int $navigationSort = 3;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-briefcase';

    public static function form(Schema $schema): Schema
    {
        return LembagaPengusulForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LembagaPengusulsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Lembaga Pengusuls')
                    ->tabs([
                        Tab::make('Informasi Lembaga')
                            ->icon('heroicon-m-information-circle')
                            ->schema([
                                TextEntry::make('nama_lembaga')->label('Nama Lembaga'),
                                TextEntry::make('alamat_lembaga')->label('Alamat Lembaga'),
                            ]),
                        Tab::make('Profil Pimpinan')
                            ->icon('heroicon-m-user')
                            ->schema([
                                TextEntry::make('pimpinan.name')->label('Nama Pimpinan'),
                                TextEntry::make('pimpinan.email')->label('Email'),
                                TextEntry::make('pimpinan.nik')->label('NIK / Identitas'),
                                TextEntry::make('pimpinan.telepon')->label('Nomor Telepon'),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Admin\Resources\LembagaPengusuls\RelationManagers\SppgsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLembagaPengusuls::route('/'),
            'create' => CreateLembagaPengusul::route('/create'),
            'view' => ViewLembagaPengusul::route('/{record}'),
            'edit' => EditLembagaPengusul::route('/{record}/edit'),
        ];
    }
}
