<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use App\Models\Sppg;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use BackedEnum;
use UnitEnum;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-newspaper';
    protected static ?string $navigationLabel = 'Blog';
    protected static ?string $pluralModelLabel = 'Blog Posts';
    protected static ?string $modelLabel = 'Post';
    protected static string|UnitEnum|null $navigationGroup = 'Situs & Konten';
    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Konten Post')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->label('Judul')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($set, $state) => $set('slug', Str::slug($state))),
                        TextInput::make('slug')
                            ->label('Slug (URL)')
                            ->required()
                            ->unique(ignoreRecord: true),
                        Textarea::make('excerpt')
                            ->label('Ringkasan')
                            ->rows(2)
                            ->columnSpanFull(),
                        RichEditor::make('content')
                            ->label('Isi Artikel')
                            ->required()
                            ->columnSpanFull(),
                        FileUpload::make('featured_image')
                            ->label('Gambar Utama')
                            ->image()
                            ->disk('public')
                            ->directory('blog-images')
                            ->maxSize(10240),
                        Select::make('status')
                            ->label('Status')
                            ->options(function () {
                                if (Auth::user()->hasAnyRole(['Superadmin', 'Ketua Kornas', 'Staf Akuntan Kornas', 'Staf Kornas'])) {
                                    return [
                                        'draft' => 'Draft',
                                        'pending_review' => 'Pending Review',
                                        'published' => 'Published',
                                        'archived' => 'Archived',
                                    ];
                                }
                                
                                return [
                                    'draft' => 'Draft',
                                    'pending_review' => 'Ajukan Review',
                                ];
                            })
                            ->default('draft')
                            ->required()
                            ->disableOptionWhen(fn (string $value): bool => $value === 'published' && !Auth::user()->hasAnyRole(['Superadmin', 'Ketua Kornas', 'Staf Akuntan Kornas', 'Staf Kornas'])),
                        DateTimePicker::make('published_at')
                            ->label('Tanggal Publish')
                            ->native(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('featured_image')
                    ->label('Gambar')
                    ->disk('public')
                    ->circular(),
                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                TextColumn::make('author.name')
                    ->label('Penulis')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('sppg.nama_sppg')
                    ->label('SPPG')
                    ->visible(fn () => Auth::user()->hasAnyRole(['Superadmin', 'Ketua Kornas', 'Staf Akuntan Kornas', 'Staf Kornas'])),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'pending_review' => 'info',
                        'published' => 'success',
                        'archived' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('published_at')
                    ->label('Dipublish')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                //
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = Auth::user();

        // SPPG users only see their own posts
        if ($user->hasRole(['Kepala SPPG', 'Admin SPPG'])) {
            return $query->where('user_id', $user->id);
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
