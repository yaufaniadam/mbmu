<?php

namespace App\Filament\Lembaga\Resources;

use App\Filament\Lembaga\Resources\ComplaintResource\Pages;
use App\Models\Complaint;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use UnitEnum;
use BackedEnum;

class ComplaintResource extends Resource
{
    protected static ?string $model = Complaint::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationLabel = 'Pengaduan';
    protected static ?string $pluralModelLabel = 'Pengaduan';
    protected static ?string $modelLabel = 'Pengaduan';
    protected static string|UnitEnum|null $navigationGroup = null;
    protected static ?int $navigationSort = 3;

    public static function shouldRegisterNavigation(): bool
    {
        if (Auth::user()?->hasRole('Staf Akuntan Kornas')) {
            return false;
        }

        return true;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Pengaduan')
                    ->schema([
                         Select::make('subject')
                            ->label('Subjek Pengaduan')
                            ->options(function () {
                                $baseOptions = [
                                    'Pencairan' => 'Pencairan',
                                    'Akuntan' => 'Akuntan',
                                    'Ahli Gizi' => 'Ahli Gizi',
                                    'Virtual Account' => 'Virtual Account',
                                ];
                                
                                $user = Auth::user();
                                
                                // Kepala SPPG specific options
                                if ($user->hasRole('Kepala SPPG')) {
                                    $baseOptions['Lembaga Pengusul'] = 'Lembaga Pengusul';
                                    $baseOptions['Lain-lain'] = 'Lain-lain';
                                } 
                                // Lembaga Pengusul specific options
                                elseif ($user->hasAnyRole(['Pimpinan Lembaga Pengusul', 'PJ Pelaksana'])) {
                                    $baseOptions['Kepala'] = 'Kepala'; // Report about Kepala SPPG
                                    $baseOptions['Sarpras'] = 'Sarpras';
                                }
                                
                                return $baseOptions;
                            })
                            ->required()
                            ->disabled(fn ($record) => $record && $record->status !== 'Open'),
                        Textarea::make('content')
                            ->label('Isi Pengaduan')
                            ->rows(5)
                            ->required()
                            ->disabled(fn ($record) => $record && $record->status !== 'Open'),
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'Open' => 'Open',
                                'Responded' => 'Responded',
                                'Closed' => 'Closed',
                            ])
                            ->default('Open')
                            ->disabled()
                            ->hidden(fn ($operation) => $operation === 'create'),
                    ])
                    ->columnSpan(fn ($record) => $record && $record->feedback ? 1 : 2),

                Section::make('Feedback Kornas')
                    ->visible(fn ($record) => $record && ($record->feedback || Auth::user()->hasAnyRole(['Superadmin', 'Direktur Kornas', 'Staf Akuntan Kornas', 'Staf Kornas'])))
                    ->schema([
                        Textarea::make('feedback')
                            ->label('Tanggapan / Rekomendasi')
                            ->rows(5)
                            ->disabled(fn () => !Auth::user()->hasAnyRole(['Superadmin', 'Direktur Kornas', 'Staf Akuntan Kornas', 'Staf Kornas'])),
                        Placeholder::make('feedback_info')
                            ->label('Ditanggapi Oleh')
                            ->content(fn ($record) => $record->responder->name . ' (' . $record->feedback_at->format('d M Y H:i') . ')')
                            ->visible(fn ($record) => $record && $record->feedback_by),
                    ])
                    ->columnSpan(1),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Pengirim')
                    ->searchable()
                    ->sortable()
                    ->visible(fn () => Auth::user()->hasAnyRole(['Superadmin', 'Direktur Kornas', 'Staf Akuntan Kornas', 'Staf Kornas'])),
                TextColumn::make('subject')
                    ->label('Subjek')
                    ->searchable()
                    ->badge()
                    ->color('info'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Open' => 'danger',
                        'Responded' => 'warning',
                        'Closed' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('updated_at')
                    ->label('Update Terakhir')
                    ->since(),
            ])
            ->filters([
                //
            ])
            ->actions([
                \Filament\Actions\ViewAction::make()
                    ->label('Lihat')
                    ->icon('heroicon-o-eye'),
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = Auth::user();

        // LP only sees their own LP complaints
        if ($user->hasAnyRole(['Pimpinan Lembaga Pengusul', 'PJ Pelaksana'])) {
            return $query->where('user_id', $user->id)
                         ->where('source_type', 'lembaga_pengusul');
        }

        // Kepala SPPG only sees their own SPPG complaints
        if ($user->hasRole('Kepala SPPG')) {
            return $query->where('user_id', $user->id)
                         ->where('source_type', 'sppg');
        }

        // Kornas/admin sees all
        return $query;
    }

    public static function canCreate(): bool
    {
        return Auth::user()->hasAnyRole(['Pimpinan Lembaga Pengusul', 'Kepala SPPG', 'PJ Pelaksana']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListComplaints::route('/'),
            'create' => Pages\CreateComplaint::route('/create'),
            'view' => Pages\ViewComplaint::route('/{record}'),
            'edit' => Pages\EditComplaint::route('/{record}/edit'),
        ];
    }
}
