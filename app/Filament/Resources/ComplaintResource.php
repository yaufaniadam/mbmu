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
    protected static string|UnitEnum|null $navigationGroup = 'Bantuan & Layanan';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Pengaduan')
                    ->schema([
                         Select::make('subject')
                            ->label('Subjek Pengaduan')
                            ->options([
                                'Pencairan' => 'Pencairan',
                                'Kepala' => 'Kepala',
                                'Akuntan' => 'Akuntan',
                                'Ahli Gizi' => 'Ahli Gizi',
                                'Virtual Account' => 'Virtual Account',
                            ])
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
                \Filament\Actions\ViewAction::make(),
                Action::make('respond')
                    ->label('Tanggapi')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->color('success')
                    ->visible(fn (Complaint $record) => 
                        Auth::user()->hasAnyRole(['Superadmin', 'Direktur Kornas', 'Staf Akuntan Kornas', 'Staf Kornas']) && 
                        $record->status !== 'Closed'
                    )
                    ->form([
                        Textarea::make('feedback')
                            ->label('Isi Tanggapan')
                            ->required()
                            ->default(fn ($record) => $record->feedback),
                    ])
                    ->action(function (Complaint $record, array $data) {
                        $record->update([
                            'feedback' => $data['feedback'],
                            'status' => 'Responded',
                            'feedback_by' => Auth::id(),
                            'feedback_at' => now(),
                        ]);
                        Notification::make()->title('Tanggapan Terkirim')->success()->send();
                    }),
                Action::make('close')
                    ->label('Selesai')
                    ->icon('heroicon-o-check-circle')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->visible(fn (Complaint $record) => 
                        $record->status !== 'Closed' &&
                        (Auth::user()->hasAnyRole(['Superadmin', 'Direktur Kornas', 'Staf Akuntan Kornas', 'Staf Kornas']) || $record->user_id === Auth::id())
                    )
                    ->action(fn ($record) => $record->update(['status' => 'Closed'])),
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = Auth::user();

        if ($user->hasRole('Pimpinan Lembaga Pengusul')) {
            return $query->where('user_id', $user->id);
        }

        return $query;
    }

    public static function canCreate(): bool
    {
        return Auth::user()->hasRole('Pimpinan Lembaga Pengusul');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListComplaints::route('/'),
            'create' => Pages\CreateComplaint::route('/create'),
            'edit' => Pages\EditComplaint::route('/{record}/edit'),
        ];
    }
}
