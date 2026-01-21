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

    protected static string|UnitEnum|null $navigationGroup = null;

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
                        ->default(function () {
                            $user = Auth::user();
                            if ($user->hasRole('Kepala SPPG')) {
                                return User::find($user->id)->sppgDikepalai?->id;
                            }
                            return User::find($user->id)->unitTugas->first()?->id;
                        })
                        ->required(),
                Forms\Components\FileUpload::make('image')
                    ->label('Foto Menu')
                    ->image()
                    ->directory('menu-photos')
                    ->columnSpanFull()
                    ->required()
                    ->imageEditor(),
                Forms\Components\TextInput::make('name')
                    ->label('Nama Menu (Opsional)')
                    ->placeholder('Misal: Nasi Goreng Spesial')
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

        if ($user->hasRole('Super Admin')) {
            return parent::getEloquentQuery();
        }

        if ($user->hasRole('Kepala SPPG')) {
            $sppg = User::find($user->id)->sppgDikepalai;

            if (!$sppg) {
                return parent::getEloquentQuery()->whereRaw('1 = 0');
            }

            return parent::getEloquentQuery()->where('sppg_id', $sppg->id);
        }

        if ($user->hasAnyRole(['PJ Pelaksana', 'Ahli Gizi', 'Staf Administrator SPPG', 'Staf Akuntan', 'Staf Gizi', 'Staf Pengantaran'])) {
            $unitTugas = User::find($user->id)->unitTugas->first();

            if (!$unitTugas) {
                return parent::getEloquentQuery()->whereRaw('1 = 0');
            }

            return parent::getEloquentQuery()->where('sppg_id', $unitTugas->id);
        }

        return parent::getEloquentQuery();
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
