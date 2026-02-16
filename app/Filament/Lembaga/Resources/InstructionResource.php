<?php

namespace App\Filament\Lembaga\Resources;

use App\Filament\Lembaga\Resources\InstructionResource\Pages;
use App\Models\Instruction;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InstructionResource extends Resource
{
    protected static ?string $model = Instruction::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $navigationLabel = 'Instruksi';
    protected static ?string $modelLabel = 'Instruksi';
    protected static ?string $pluralModelLabel = 'Instruksi';
    protected static \UnitEnum | string | null $navigationGroup = 'Operasional';
    protected static ?int $navigationSort = 2;

    public static function shouldRegisterNavigation(): bool
    {
        // Hide from Admins/Kornas who have access to the full InstructionResource (Admin)
        if (auth()->user()?->hasAnyRole(['Superadmin', 'Direktur Kornas', 'Staf Kornas', 'Staf Akuntan Kornas'])) {
            return false;
        }

        return true;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->active()
            ->forUser(auth()->user())
            ->orderBy('created_at', 'desc');
    }

    public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema->components([]); // Read only for Lembaga
    }

    public static function infolist(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Detail Instruksi')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('title')
                            ->label('Judul Instruksi')
                            ->weight('bold')
                            ->size('lg')
                            ->columnSpanFull(),
                        
                        \Filament\Schemas\Components\Grid::make(2)
                            ->schema([
                                \Filament\Infolists\Components\TextEntry::make('creator.name')
                                    ->label('Dibuat Oleh')
                                    ->icon('heroicon-m-user'),
                                \Filament\Infolists\Components\TextEntry::make('created_at')
                                    ->label('Tanggal Dibuat')
                                    ->date('d F Y, H:i')
                                    ->icon('heroicon-m-calendar'),
                            ]),

                        \Filament\Infolists\Components\TextEntry::make('content')
                            ->label('Isi Instruksi')
                            ->html()
                            ->columnSpanFull()
                            ->prose()
                            ->extraAttributes(['class' => 'p-4 rounded-lg border border-gray-200 bg-gray-50 dark:bg-gray-800 dark:border-gray-700 min-h-[200px]']),

                         \Filament\Infolists\Components\TextEntry::make('attachment_path')
                            ->label('Lampiran')
                            ->formatStateUsing(fn ($state) => 'Download Lampiran')
                            ->icon('heroicon-m-arrow-down-tray')
                            ->url(fn ($record) => route('instructions.attachment.download', $record))
                            ->openUrlInNewTab()
                            ->visible(fn ($record) => $record->attachment_path)
                            ->columnSpanFull(),
                        
                        \Filament\Infolists\Components\ViewEntry::make('acknowledgment')
                            ->view('filament.lembaga.resources.instruction-resource.components.acknowledgment-entry')
                            ->columnSpanFull(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->description(fn (Instruction $record): string => strip_tags(substr($record->content, 0, 100)) . '...')
                    ->wrap(),
                
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->getStateUsing(function (Instruction $record): string {
                        return $record->isAcknowledgedBy(auth()->id()) ? 'Sudah Dibaca' : 'Belum Dibaca';
                    })
                    ->colors([
                        'success' => 'Sudah Dibaca',
                        'warning' => 'Belum Dibaca',
                    ]),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->actions([
                \Filament\Actions\ViewAction::make(),
            ])
            ->paginated([10, 25, 50]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInstructions::route('/'),
            'view' => Pages\ViewInstruction::route('/{record}'),
        ];
    }
}
