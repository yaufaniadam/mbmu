<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SppgFinancialReportResource\Pages;
use App\Models\SppgFinancialReport;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;
use BackedEnum;

class SppgFinancialReportResource extends Resource
{
    protected static ?string $model = SppgFinancialReport::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationLabel = 'Laporan';
    protected static ?string $pluralModelLabel = 'Laporan';
    protected static ?string $modelLabel = 'Laporan';
    protected static string|UnitEnum|null $navigationGroup = 'Keuangan';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Laporan')
                    ->schema([
                        \Filament\Forms\Components\Select::make('category')
                            ->label('Jenis Laporan')
                            ->options([
                                'keuangan' => 'Laporan Keuangan',
                                'kegiatan' => 'Laporan Kegiatan',
                            ])
                            ->default('keuangan')
                            ->live()
                            ->required(),
                        \Filament\Forms\Components\TextInput::make('realization_amount')
                            ->label('Realisasi Anggaran')
                            ->prefix('Rp')
                            ->numeric()
                            ->required(fn ($get) => $get('category') === 'keuangan')
                            ->visible(fn ($get) => $get('category') === 'keuangan'),
                        DatePicker::make('start_date')
                            ->label('Tanggal Awal Periode')
                            ->native(false)
                            ->displayFormat('d M Y')
                            ->required(),
                        DatePicker::make('end_date')
                            ->label('Tanggal Akhir Periode')
                            ->native(false)
                            ->displayFormat('d M Y')
                            ->required(),
                        FileUpload::make('file_path')
                            ->label('File Laporan (Excel/PDF)')
                            ->disk('public')
                            ->directory('financial-reports')
                            ->maxSize(10240)
                            ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel', 'application/pdf'])
                            ->required()
                            ->downloadable()
                            ->openable(),
                        Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(3)
                            ->nullable(),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'keuangan' => 'success',
                        'kegiatan' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'keuangan' => 'Keuangan',
                        'kegiatan' => 'Kegiatan',
                        default => $state,
                    })
                    ->sortable(),
                TextColumn::make('start_date')
                    ->label('Periode')
                    ->formatStateUsing(fn ($record) => $record->start_date->format('d M Y') . ' - ' . $record->end_date->format('d M Y'))
                    ->sortable(['start_date']),
                TextColumn::make('sppg.nama_sppg')
                    ->label('Nama SPPG')
                    ->sortable()
                    ->searchable()
                    ->visible(fn () => Auth::user()->hasAnyRole(['Superadmin', 'Ketua Kornas', 'Staf Akuntan Kornas', 'Staf Kornas'])),
                TextColumn::make('file_path')
                    ->label('File')
                    ->formatStateUsing(fn () => 'Download')
                    ->url(fn ($record) => \Illuminate\Support\Facades\Storage::disk('public')->url($record->file_path))
                    ->openUrlInNewTab()
                    ->color('primary'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Uploaded' => 'info',
                        'Processed' => 'success',
                        'Rejected' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('Diupload')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('category')
                    ->label('Jenis Laporan')
                    ->options([
                        'keuangan' => 'Laporan Keuangan',
                        'kegiatan' => 'Laporan Kegiatan',
                    ]),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make()->visible(fn ($record) => $record->user_id === Auth::id()),
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = Auth::user();

        // 1. National Level: Can see everything
        if ($user->hasAnyRole(['Superadmin', 'Ketua Kornas', 'Staf Akuntan Kornas', 'Staf Kornas'])) {
            return $query;
        }

        // 2. Local Level: Restrict to SPPG
        // Check for Kepala SPPG
        if ($user->hasRole('Kepala SPPG')) {
            $sppg = $user->sppgDikepalai;
            if ($sppg) {
                return $query->where('sppg_id', $sppg->id);
            }
        }

        // Check for Staff assigned to SPPG (including Staf Akuntan, Admin SPPG, PJ Pelaksana)
        if ($user->hasAnyRole(['Staf Akuntan', 'Admin SPPG', 'PJ Pelaksana', 'Staf Administrator SPPG'])) {
            $unitTugas = $user->unitTugas->first();
            if ($unitTugas) {
                return $query->where('sppg_id', $unitTugas->id);
            }
        }

        // Pimpinan Lembaga Pengusul
        if ($user->hasRole('Pimpinan Lembaga Pengusul')) {
            $lembaga = $user->lembagaDipimpin;
            if ($lembaga) {
                $sppgIds = $lembaga->sppgs->pluck('id');
                return $query->whereIn('sppg_id', $sppgIds);
            }
        }

        // Fallback: See nothing if no role matches or no assignment found
        return $query->whereRaw('1 = 0');
    }

    public static function shouldRegisterNavigation(): bool
    {
        $panelId = \Filament\Facades\Filament::getCurrentPanel()?->getId();
        $user = Auth::user();

        // Show in Admin panel for Kornas staff to monitor all SPPG reports
        if ($panelId === 'admin') {
            return $user?->hasAnyRole(['Superadmin', 'Ketua Kornas', 'Staf Akuntan Kornas', 'Staf Kornas']);
        }

        // Show in SPPG panel for Local roles
        if ($panelId === 'sppg') {
            return $user?->hasAnyRole(['Kepala SPPG', 'Admin SPPG', 'Staf Akuntan', 'PJ Pelaksana']);
        }

        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSppgFinancialReports::route('/'),
            'create' => Pages\CreateSppgFinancialReport::route('/create'),
            'edit' => Pages\EditSppgFinancialReport::route('/{record}/edit'),
        ];
    }
}
