<?php

namespace App\Filament\Lembaga\Resources\Documents;

use App\Filament\Admin\Resources\Documents\Pages\CreateDocument;
use App\Filament\Admin\Resources\Documents\Pages\EditDocument;
use App\Filament\Admin\Resources\Documents\Pages\ListDocuments;
use App\Models\Document;
use App\Enums\DocumentCategory;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use BackedEnum;
use UnitEnum;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static ?string $modelLabel = 'Dokumen';
    
    protected static ?string $pluralModelLabel = 'Dokumen';

    protected static ?string $navigationLabel = 'Dokumen';

    protected static string|UnitEnum|null $navigationGroup = 'Kelembagaan';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();
        if (!$user) return false;

        if ($user->hasRole('Superadmin')) return true;

        if ($user->hasRole(['Ketua Kornas', 'Staf Kornas', 'Pimpinan Lembaga Pengusul'])) {
            return true;
        }

        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('lembaga_pengusul_id')
                    ->label('Lembaga Pengusul')
                    ->relationship('lembagaPengusul', 'nama_lembaga')
                    ->searchable()
                    ->preload()
                    ->required(fn () => !Auth::user()->hasRole('Pimpinan Lembaga Pengusul'))
                    ->visible(fn () => !Auth::user()->hasRole('Pimpinan Lembaga Pengusul')),

                \Filament\Forms\Components\Hidden::make('lembaga_pengusul_id')
                    ->default(fn () => \App\Models\User::find(Auth::id())->lembagaDipimpin?->id)
                    ->visible(fn () => Auth::user()->hasRole('Pimpinan Lembaga Pengusul')),

                Select::make('kategori')
                    ->options(DocumentCategory::class)
                    ->required()
                    ->label('Kategori')
                    ->native(false),
                
                DatePicker::make('tanggal')
                    ->required()
                    ->label('Tanggal')
                    ->native(false),

                Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->columnSpanFull(),

                FileUpload::make('file_path')
                    ->label('File')
                    ->acceptedFileTypes(['application/pdf'])
                    ->directory('documents')
                    ->downloadable()
                    ->openable()
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('lembagaPengusul.nama_lembaga')
                    ->label('Lembaga')
                    ->sortable()
                    ->searchable()
                    ->placeholder('-')
                    ->toggleable(),

                TextColumn::make('kategori')
                    ->badge()
                    ->sortable()
                    ->searchable(),

                TextColumn::make('tanggal')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('keterangan')
                    ->limit(50),
                
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
                ViewAction::make(),
            ])
            ->toolbarActions([
                //
            ])
            ->modifyQueryUsing(function (Builder $query) {
                // Determine what users can see
                $user = Auth::user();
                if ($user->hasRole('Superadmin')) {
                    return $query;
                }
                
                if ($user->hasRole(['Ketua Kornas', 'Staf Kornas'])) {
                    // Kornas presumably sees all documents?
                    return $query;
                }

                if ($user->hasRole('Pimpinan Lembaga Pengusul')) {
                    $lembagaId = $user->lembagaDipimpin?->id;
                    
                    return $query->where(function (Builder $q) use ($user, $lembagaId) {
                        $q->where('user_id', $user->id);
                        
                        if ($lembagaId) {
                            $q->orWhere('lembaga_pengusul_id', $lembagaId);
                        }
                    });
                }

                return $query->where('user_id', $user->id); // Fallback
            });
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDocuments::route('/'),
            'create' => CreateDocument::route('/create'),
            'edit' => EditDocument::route('/{record}/edit'),
        ];
    }
}
