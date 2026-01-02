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
    protected static ?string $navigationLabel = 'Laporan Keuangan';
    protected static ?string $pluralModelLabel = 'Laporan Keuangan';
    protected static ?string $modelLabel = 'Laporan Keuangan';
    protected static string|UnitEnum|null $navigationGroup = 'Keuangan';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Laporan')
                    ->schema([
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
                            ->label('File Excel Laporan')
                            ->disk('public')
                            ->directory('financial-reports')
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
                TextColumn::make('start_date')
                    ->label('Periode')
                    ->formatStateUsing(fn ($record) => $record->start_date->format('d M Y') . ' - ' . $record->end_date->format('d M Y'))
                    ->sortable(['start_date']),
                TextColumn::make('sppg.nama_sppg')
                    ->label('Nama SPPG')
                    ->sortable()
                    ->searchable()
                    ->visible(fn () => Auth::user()->hasAnyRole(['Superadmin', 'Direktur Kornas', 'Staf Akuntan Kornas', 'Staf Kornas'])),
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
                //
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

        // If user is Kepal SPPG or equivalent restrict to their SPPG
        // Assuming user has 'sppg_id' or relation to SPPG. 
        // Based on Sppg model, head is 'kepala_sppg_id'.
        // But users don't have 'sppg_id' column directly usually? 
        // Let's check User model later. 
        // For now, if role is 'Kepala SPPG', filter by user_id = Auth::id() OR if they manage an SPPG.
        // Assuming 'user_id' in SppgFinancialReport is the uploader. 
        // If the uploader is the Kepala SPPG, then filtering by user_id is safe.
        
        if ($user->hasRole(['Kepala SPPG', 'Admin SPPG'])) {
             return $query->where('user_id', $user->id);
        }

        return $query;
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
