<?php

namespace App\Filament\Sppg\Resources\Menus;

use App\Filament\Sppg\Resources\Menus\MenuResource\Pages;
use App\Filament\Sppg\Resources\Menus\MenuResource\RelationManagers;
use App\Filament\Sppg\Resources\Menus\Pages\CreateMenu;
use App\Filament\Sppg\Resources\Menus\Pages\EditMenu;
use App\Filament\Sppg\Resources\Menus\Pages\ListMenus;
use App\Filament\Sppg\Resources\Menus\Schemas\MenuForm;
use App\Filament\Sppg\Resources\Menus\Tables\MenusTable;
use App\Models\Menu;
use App\Models\User;
use BackedEnum;
use UnitEnum;
use Filament\Forms;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;

    protected static ?string $navigationLabel = 'Menu Makanan';
    
    protected static ?string $pluralModelLabel = 'Menu Makanan';

    public static function getNavigationGroup(): ?string
    {
        if (Auth::user()?->hasAnyRole(['Kepala SPPG', 'PJ Pelaksana'])) {
            return 'Operasional';
        }

        return 'Situs & Konten';
    }

    protected static ?int $navigationSort = 3;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

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
                Auth::user()->hasRole('Super Admin') 
                    ? Forms\Components\Select::make('sppg_id')
                        ->label('SPPG')
                        ->relationship('sppg', 'nama_sppg')
                        ->searchable()
                        ->preload()
                        ->required()
                    : Forms\Components\Hidden::make('sppg_id')
                        ->default(fn () => Auth::user()->getManagedSppg()?->id)
                        ->required(),
                Forms\Components\FileUpload::make('image')->disk('public')
                    ->label('Foto Menu')
                    ->image()
                    ->disk('public')
                    ->directory('menu-photos')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->maxSize(200) // 200 KB
                    ->validationMessages([
                        'max' => 'Ukuran foto terlalu besar. Maksimal 200 KB. Kompres foto terlebih dahulu.',
                        'mimes' => 'Format foto tidak didukung. Gunakan JPG, PNG, atau WebP.',
                    ])
                    ->helperText('Maks. 200 KB · Format: JPG, PNG, WebP · Kompres di tinypng.com jika perlu')
                    ->columnSpanFull()
                    ->required()
                    ->imageEditor(),
                Forms\Components\TextInput::make('name')
                    ->label('Nama Menu (Opsional)')
                    ->placeholder('Misal: Nasi Goreng Spesial')
                    ->columnSpanFull(),
                Forms\Components\DatePicker::make('date')
                    ->label('Tanggal Menu')
                    ->default(now())
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('description')
                    ->label('Deskripsi (Opsional)')
                    ->placeholder('Penjelasan singkat tentang menu ini')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Foto')
                    ->square(),
                TextColumn::make('name')
                    ->label('Nama Menu')
                    ->placeholder('Tanpa Nama')
                    ->searchable(),
                TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(50)
                    ->placeholder('-'),
                TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                \Filament\Tables\Filters\Filter::make('created_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('created_from')
                            ->label('Dari Tanggal'),
                        \Filament\Forms\Components\DatePicker::make('created_until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
                \Filament\Tables\Filters\SelectFilter::make('sppg_id')
                    ->label('SPPG')
                    ->relationship('sppg', 'nama_sppg')
                    ->searchable()
                    ->preload(),
                \Filament\Tables\Filters\SelectFilter::make('province_code')
                    ->label('Provinsi')
                    ->options(fn() => \Laravolt\Indonesia\Models\Province::pluck('name', 'code'))
                    ->query(fn (Builder $query, array $data) => 
                        $query->when(
                            $data['value'],
                            fn ($q) => $q->whereHas('sppg', fn ($sq) => $sq->where('province_code', $data['value']))
                        )
                    )
                    ->searchable(),
                \Filament\Tables\Filters\SelectFilter::make('city_code')
                    ->label('Kabupaten/Kota')
                    ->options(fn() => \Laravolt\Indonesia\Models\City::pluck('name', 'code'))
                    ->query(fn (Builder $query, array $data) => 
                        $query->when(
                            $data['value'],
                            fn ($q) => $q->whereHas('sppg', fn ($sq) => $sq->where('city_code', $data['value']))
                        )
                    )
                    ->searchable(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();
        $query = parent::getEloquentQuery();

        if ($user->hasRole('Super Admin')) {
            return $query;
        }

        $sppg = $user->getManagedSppg();

        if (!$sppg) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where('sppg_id', $sppg->id);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMenus::route('/'),
            'create' => CreateMenu::route('/create'),
            'edit' => EditMenu::route('/{record}/edit'),
        ];
    }
}
