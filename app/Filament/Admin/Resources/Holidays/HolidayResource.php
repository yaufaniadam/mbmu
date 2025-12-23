<?php

namespace App\Filament\Admin\Resources\Holidays;

use App\Models\Holiday;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class HolidayResource extends Resource
{
    protected static ?string $model = Holiday::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar';

    protected static string|UnitEnum|null $navigationGroup = 'Sistem';

    protected static ?string $navigationLabel = 'Hari Libur Nasional';

    protected static ?int $navigationSort = 99;

    public static function getModelLabel(): string
    {
        return 'Hari Libur';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Hari Libur Nasional';
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('Superadmin') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            DatePicker::make('tanggal')
                ->label('Tanggal')
                ->required()
                ->unique(ignoreRecord: true),
            TextInput::make('nama')
                ->label('Nama Hari Libur')
                ->required()
                ->placeholder('Contoh: Hari Kemerdekaan RI'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('nama')
                    ->label('Nama Hari Libur')
                    ->searchable(),
            ])
            ->defaultSort('tanggal', 'asc')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHolidays::route('/'),
            'create' => Pages\CreateHoliday::route('/create'),
            'edit' => Pages\EditHoliday::route('/{record}/edit'),
        ];
    }
}
