<?php

namespace App\Filament\Lembaga\Resources;

use App\Filament\Lembaga\Resources\SppgResource\Pages;
use App\Models\Sppg;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use BackedEnum;
use UnitEnum;

class SppgResource extends Resource
{
    protected static ?string $model = Sppg::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';
    
    protected static UnitEnum|string|null $navigationGroup = 'Kelembagaan';
    
    protected static ?string $pluralModelLabel = 'SPPG';
    protected static ?string $modelLabel = 'SPPG';
    
    protected static ?int $navigationSort = 1;

    public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return SppgResource\Schemas\SppgForm::configure($schema);
    }

    public static function infolist(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Tabs::make('Informasi Unit')
                    ->columnSpanFull()
                    ->tabs([
                        \Filament\Schemas\Components\Tabs\Tab::make('Profil SPPG')
                            ->icon('heroicon-m-building-office')
                            ->schema([
                                \Filament\Schemas\Components\Section::make('Data Dasar')
                                    ->columns(3)
                                    ->schema([
                                        \Filament\Infolists\Components\TextEntry::make('nama_sppg')->label('Nama SPPG')->color('gray'),
                                        \Filament\Infolists\Components\TextEntry::make('kode_sppg')->label('ID SPPG')->color('gray'),
                                        \Filament\Infolists\Components\TextEntry::make('kepalaSppg.name')->label('Kepala SPPG')->color('gray'),
                                        \Filament\Infolists\Components\TextEntry::make('pjSppg.name')->label('PJ Pelaksana')->color('gray'),
                                        \Filament\Infolists\Components\TextEntry::make('nama_bank')->label('Bank')->color('gray'),
                                        \Filament\Infolists\Components\TextEntry::make('nomor_va')->label('Nomor VA')->color('gray'),
                                        \Filament\Infolists\Components\TextEntry::make('tanggal_operasional_pertama')->label('Operasional Pertama')->date()->color('gray'),
                                        \Filament\Infolists\Components\TextEntry::make('alamat')->label('Alamat')->columnSpanFull()->color('gray'),
                                    ]),
                            ]),

                        \Filament\Schemas\Components\Tabs\Tab::make('Lembaga Pengusul')
                            ->icon('heroicon-m-briefcase')
                            ->schema([
                                \Filament\Schemas\Components\Section::make('Identitas Lembaga Pengusul')
                                    ->schema([
                                        \Filament\Schemas\Components\Grid::make(2)
                                            ->schema([
                                                \Filament\Infolists\Components\TextEntry::make('lembagaPengusul.nama_lembaga')->label('Nama Lembaga'),
                                                \Filament\Infolists\Components\TextEntry::make('lembagaPengusul.pimpinan.name')->label('Nama Pimpinan'),
                                                \Filament\Infolists\Components\TextEntry::make('lembagaPengusul.pimpinan.email')->label('Email Pimpinan'),
                                                \Filament\Infolists\Components\TextEntry::make('lembagaPengusul.alamat_lembaga')->label('Alamat Lembaga'),
                                            ]),
                                    ]),
                            ]),

                        \Filament\Schemas\Components\Tabs\Tab::make('Dokumen')
                            ->icon('heroicon-m-document-text')
                            ->schema([
                                \Filament\Schemas\Components\Section::make('Dokumen SPPG')
                                    ->columns(2)
                                    ->schema([
                                        \Filament\Infolists\Components\TextEntry::make('izin_operasional_path')
                                            ->label('Dokumen Verval')
                                            ->formatStateUsing(fn ($state) => $state ? 'Download' : 'Belum ada')
                                            ->icon(fn ($state) => $state ? 'heroicon-m-arrow-down-tray' : null)
                                            ->color(fn ($state) => $state ? 'primary' : 'gray')
                                            ->url(fn ($state) => $state ? \Illuminate\Support\Facades\Storage::disk('public')->url($state) : null)
                                            ->openUrlInNewTab(),
                                        \Filament\Infolists\Components\TextEntry::make('sertifikat_halal_path')
                                            ->label('Sertifikat Halal')
                                            ->formatStateUsing(fn ($state) => $state ? 'Download' : 'Belum ada')
                                            ->icon(fn ($state) => $state ? 'heroicon-m-arrow-down-tray' : null)
                                            ->color(fn ($state) => $state ? 'primary' : 'gray')
                                            ->url(fn ($state) => $state ? \Illuminate\Support\Facades\Storage::disk('public')->url($state) : null)
                                            ->openUrlInNewTab(),
                                        \Filament\Infolists\Components\TextEntry::make('slhs_path')
                                            ->label('SLHS')
                                            ->formatStateUsing(fn ($state) => $state ? 'Download' : 'Belum ada')
                                            ->icon(fn ($state) => $state ? 'heroicon-m-arrow-down-tray' : null)
                                            ->color(fn ($state) => $state ? 'primary' : 'gray')
                                            ->url(fn ($state) => $state ? \Illuminate\Support\Facades\Storage::disk('public')->url($state) : null)
                                            ->openUrlInNewTab(),
                                        \Filament\Infolists\Components\TextEntry::make('lhaccp_path')
                                            ->label('HACCP')
                                            ->formatStateUsing(fn ($state) => $state ? 'Download' : 'Belum ada')
                                            ->icon(fn ($state) => $state ? 'heroicon-m-arrow-down-tray' : null)
                                            ->color(fn ($state) => $state ? 'primary' : 'gray')
                                            ->url(fn ($state) => $state ? \Illuminate\Support\Facades\Storage::disk('public')->url($state) : null)
                                            ->openUrlInNewTab(),
                                        \Filament\Infolists\Components\TextEntry::make('iso_path')
                                            ->label('ISO')
                                            ->formatStateUsing(fn ($state) => $state ? 'Download' : 'Belum ada')
                                            ->icon(fn ($state) => $state ? 'heroicon-m-arrow-down-tray' : null)
                                            ->color(fn ($state) => $state ? 'primary' : 'gray')
                                            ->url(fn ($state) => $state ? \Illuminate\Support\Facades\Storage::disk('public')->url($state) : null)
                                            ->openUrlInNewTab(),
                                        \Filament\Infolists\Components\TextEntry::make('sertifikat_lahan_path')
                                            ->label('Sertifikat Lahan')
                                            ->formatStateUsing(fn ($state) => $state ? 'Download' : 'Belum ada')
                                            ->icon(fn ($state) => $state ? 'heroicon-m-arrow-down-tray' : null)
                                            ->color(fn ($state) => $state ? 'primary' : 'gray')
                                            ->url(fn ($state) => $state ? \Illuminate\Support\Facades\Storage::disk('public')->url($state) : null)
                                            ->openUrlInNewTab(),
                                        \Filament\Infolists\Components\TextEntry::make('dokumen_lain_path')
                                            ->label('Dokumen Lain-lain')
                                            ->formatStateUsing(fn ($state) => $state ? 'Download' : 'Belum ada')
                                            ->icon(fn ($state) => $state ? 'heroicon-m-arrow-down-tray' : null)
                                            ->color(fn ($state) => $state ? 'primary' : 'gray')
                                            ->url(fn ($state) => $state ? \Illuminate\Support\Facades\Storage::disk('public')->url($state) : null)
                                            ->openUrlInNewTab(),
                                        \Filament\Infolists\Components\TextEntry::make('pks_path')
                                            ->label('Perjanjian Kerjasama')
                                            ->formatStateUsing(fn ($state) => $state ? 'Download' : 'Belum ada')
                                            ->icon(fn ($state) => $state ? 'heroicon-m-arrow-down-tray' : null)
                                            ->color(fn ($state) => $state ? 'primary' : 'gray')
                                            ->url(fn ($state) => $state ? \Illuminate\Support\Facades\Storage::disk('public')->url($state) : null)
                                            ->openUrlInNewTab(),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_sppg')
                    ->label('Nama SPPG')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                
                Tables\Columns\TextColumn::make('kepala.name')
                    ->label('Kepala SPPG')
                    ->searchable()
                    ->default('-'),
                
                Tables\Columns\TextColumn::make('alamat_sppg')
                    ->label('Alamat')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->alamat_sppg),
                
                Tables\Columns\TextColumn::make('city.name')
                    ->label('Kota/Kabupaten')
                    ->searchable()
                    ->sortable()
                    ->default('-'),
                
                Tables\Columns\TextColumn::make('province.name')
                    ->label('Provinsi')
                    ->searchable()
                    ->sortable()
                    ->default('-'),
            ])
            ->filters([
                // Filters removed since province/city data comes from relations
            ])
            ->recordActions([
                \Filament\Actions\ViewAction::make(),
            ])
            ->defaultSort('nama_sppg', 'asc');
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        // Filter hanya SPPG yang dibawahi lembaga ini
        $lembaga = $user->lembagaDipimpin;
        
        if ($lembaga) {
            return $query->where('lembaga_pengusul_id', $lembaga->id);
        }

        // Fallback: tidak ada SPPG
        return $query->whereRaw('1 = 0');
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Lembaga\Resources\SppgResource\RelationManagers\StaffRelationManager::class,
            \App\Filament\Lembaga\Resources\SppgResource\RelationManagers\VolunteersRelationManager::class,
            \App\Filament\Lembaga\Resources\SppgResource\RelationManagers\SchoolsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSppgs::route('/'),
            'view' => Pages\ViewSppg::route('/{record}'),
        ];
    }
    
    public static function canViewAny(): bool
    {
        return auth()->user()->hasAnyRole(['Pimpinan Lembaga Pengusul', 'PJ Pelaksana']);
    }
    
    public static function canView($record): bool
    {
        return auth()->user()->hasAnyRole(['Pimpinan Lembaga Pengusul', 'PJ Pelaksana']);
    }
    
    public static function canCreate(): bool
    {
        return false; // Lembaga tidak bisa create SPPG
    }
}
