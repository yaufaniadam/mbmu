<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ComplaintResource\Pages;
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
    protected static string|UnitEnum|null $navigationGroup = 'Operasional';
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
                                $user = Auth::user();
                                $options = [
                                    'Pencairan' => 'Pencairan',
                                    'Akuntan' => 'Akuntan',
                                    'Ahli Gizi' => 'Ahli Gizi',
                                    'Virtual Account' => 'Virtual Account',
                                ];
                                
                                if ($user?->hasRole('Kepala SPPG')) {
                                    $options['Lembaga Pengusul'] = 'Lembaga Pengusul';
                                    $options['Kecelakaan Kerja/Accident'] = 'Kecelakaan Kerja/Accident';
                                    $options['Kasus'] = 'Kasus';
                                    $options['Bencana'] = 'Bencana';
                                    $options['Lain-lain'] = 'Lain-lain';
                                } elseif ($user?->hasAnyRole(['Pimpinan Lembaga Pengusul', 'PJ Pelaksana'])) {
                                    $options['Kepala'] = 'Kepala';
                                    $options['Sarpras'] = 'Sarpras';
                                    $options['Kecelakaan Kerja/Accident'] = 'Kecelakaan Kerja/Accident';
                                    $options['Kasus'] = 'Kasus';
                                    $options['Bencana'] = 'Bencana';
                                }
                                
                                return $options;
                            })
                            ->required()
                            ->disabled(fn ($record) => $record && $record->status !== 'Open'),
                        Textarea::make('content')
                            ->label('Isi Pengaduan')
                            ->rows(5)
                            ->required()
                            ->disabled(fn ($record) => $record && $record->status !== 'Open'),
                        \Filament\Forms\Components\FileUpload::make('supporting_document')->disk('public')
                            ->label('Dokumen Pendukung')
                            ->disk('public')
                            ->directory('complaint-documents')
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                            ->maxSize(5120) // 5MB
                            ->downloadable()
                            ->openable()
                            ->hidden(fn ($operation) => $operation === 'view')
                            ->disabled(fn ($record) => $record && $record->status !== 'Open'),
                        Placeholder::make('download_document')
                            ->label('Download Dokumen')
                            ->visible(fn ($record) => !empty($record?->supporting_document))
                            ->content(function ($record) {
                                if (!$record || !$record->supporting_document) return null;
                                $url = \Illuminate\Support\Facades\Storage::disk('public')->url($record->supporting_document);
                                return new \Illuminate\Support\HtmlString("
                                    <a href='{$url}' target='_blank' class='inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500'>
                                        <svg class='-ml-1 mr-2 h-5 w-5' xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='currentColor'>
                                            <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3' />
                                        </svg>
                                        Download Dokumen Pendukung
                                    </a>
                                ");
                            }),
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
                    ->visible(fn ($record) => $record && ($record->feedback || Auth::user()->hasAnyRole(['Superadmin', 'Ketua Kornas', 'Staf Akuntan Kornas', 'Staf Kornas'])))
                    ->schema([
                        Textarea::make('feedback')
                            ->label('Tanggapan / Rekomendasi')
                            ->rows(5)
                            ->disabled(fn () => !Auth::user()->hasAnyRole(['Superadmin', 'Ketua Kornas', 'Staf Akuntan Kornas', 'Staf Kornas'])),
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
                    ->visible(fn () => Auth::user()->hasAnyRole(['Superadmin', 'Ketua Kornas', 'Staf Akuntan Kornas', 'Staf Kornas'])),
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
