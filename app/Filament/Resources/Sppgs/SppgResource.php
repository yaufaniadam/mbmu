<?php

namespace App\Filament\Resources\Sppgs;

use App\Filament\Resources\Sppgs\Pages\CreateSppg;
use App\Filament\Resources\Sppgs\Pages\EditSppg;
use App\Filament\Resources\Sppgs\Pages\ListSppgs;
use App\Filament\Resources\Sppgs\Pages\ViewSppg;
use App\Filament\Resources\Sppgs\Schemas\SppgForm;
use App\Filament\Resources\Sppgs\Tables\SppgsTable;
use App\Models\Sppg;
use App\Models\User;
use BackedEnum;
use UnitEnum;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class SppgResource extends Resource
{
    protected static ?string $model = Sppg::class;

    protected static ?string $navigationLabel = 'Unit SPPG';

    protected static string|UnitEnum|null $navigationGroup = 'Kelembagaan';

    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return auth()->user()->hasRole('Superadmin');
    }

    public static function canView(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return auth()->user()->hasAnyRole(['Superadmin', 'Direktur Kornas', 'Staf Akuntan Kornas', 'Staf Kornas']);
    }

    public static function form(Schema $schema): Schema
    {
        return SppgForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SppgsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Informasi Unit')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Profil SPPG')
                            ->icon('heroicon-m-building-office')
                            ->schema([
                                Section::make('Data Dasar')
                                    ->columns(3)
                                    ->schema([
                                        TextEntry::make('nama_sppg')->label('Nama SPPG'),
                                        TextEntry::make('kode_sppg')->label('ID SPPG'),
                                        TextEntry::make('kepalaSppg.name')->label('Kepala SPPG'),
                                        TextEntry::make('nama_bank')->label('Bank'),
                                        TextEntry::make('nomor_va')->label('Nomor VA'),
                                        TextEntry::make('tanggal_operasional_pertama')->label('Operasional Pertama')->date(),
                                        TextEntry::make('alamat')->columnSpanFull(),

                                        TextEntry::make('ba_verval_path')
                                            ->label('BA Verval')
                                            ->formatStateUsing(fn () => '')
                                            ->icon('heroicon-m-arrow-down-tray')
                                            ->color('primary')
                                            ->url(fn ($state) => $state)
                                            ->openUrlInNewTab()
                                            ->visible(fn ($state) => filled($state)),

                                        TextEntry::make('permohonan_pengusul_path')
                                            ->label('Surat Permohonan')
                                            ->formatStateUsing(fn () => '')
                                            ->icon('heroicon-m-arrow-down-tray')
                                            ->color('primary')
                                            ->url(fn ($state) => $state)
                                            ->openUrlInNewTab()
                                            ->visible(fn ($state) => filled($state)),
                                    ]),
                            ]),

                        Tab::make('Lembaga Pengusul')
                            ->icon('heroicon-m-briefcase')
                            ->schema([
                                Section::make('Identitas Lembaga Pengusul')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextEntry::make('lembagaPengusul.nama_lembaga')->label('Nama Lembaga'),
                                                TextEntry::make('lembagaPengusul.pimpinan.name')->label('Nama Pimpinan'),
                                                TextEntry::make('lembagaPengusul.pimpinan.email')->label('Email Pimpinan'),
                                                TextEntry::make('lembagaPengusul.alamat_lembaga')->label('Alamat Lembaga'),
                                            ]),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\StaffRelationManager::class,
            RelationManagers\VolunteersRelationManager::class,
            RelationManagers\SchoolsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSppgs::route('/'),
            'create' => CreateSppg::route('/create'),
            'view' => ViewSppg::route('/{record}'),
            'edit' => EditSppg::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();
        if ($user->hasRole('Pimpinan Lembaga Pengusul')) {
            $sppg = User::find($user->id)->lembagaDipimpin;

            return parent::getEloquentQuery()
                ->where('lembaga_pengusul_id', $sppg->id);
        }

        return parent::getEloquentQuery();
    }
}
