<?php

namespace App\Filament\Sppg\Resources;

use App\Filament\Sppg\Resources\ProductionVerificationResource\Pages;
use App\Models\ProductionVerification;
use App\Models\ProductionVerificationSetting;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;
use BackedEnum;

class ProductionVerificationResource extends Resource
{
    protected static ?string $model = ProductionVerification::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationLabel = 'Evaluasi Mandiri';

    protected static bool $shouldRegisterNavigation = false;

    protected static string|UnitEnum|null $navigationGroup = 'Operasional';
    
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\DatePicker::make('date')
                    ->label('Tanggal Evaluasi')
                    ->default(now())
                    ->required(),

                // Dynamic Checklist based on Global Settings
                Repeater::make('checklist_results')
                    ->label('Checklist Evaluasi')
                    ->schema([
                        TextInput::make('item')
                            ->label('Kriteria')
                            ->disabled() // Item name is fixed
                            ->required(),
                        Select::make('status')
                            ->label('Hasil')
                            ->options([
                                'Sesuai' => 'Sesuai',
                                'Tidak Sesuai' => 'Tidak Sesuai',
                                'Perlu Perbaikan' => 'Perlu Perbaikan',
                            ])
                            ->required()
                            ->native(false),
                        TextInput::make('keterangan')
                            ->label('Keterangan (Opsional)'),
                    ])
                    ->addable(false) // Cannot add new items manually
                    ->deletable(false) // Cannot remove items
                    ->reorderable(false)
                    ->defaultItems(0) // Logic to fill this goes in Mount/Create Page
                    ->columnSpanFull()
                    ->columns(3),

                Textarea::make('notes')
                    ->label('Catatan Tambahan')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pemeriksa')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                \Filament\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(), // Maybe allow edit?
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
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
            'index' => Pages\ListProductionVerifications::route('/'),
            'create' => Pages\CreateProductionVerification::route('/create'),
            // 'edit' => Pages\EditProductionVerification::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        // Restrict to current SPPG
        $user = Auth::user();
        if ($user->hasRole('Kepala SPPG')) {
            $sppg = User::find($user->id)->sppgDikepalai;
            if (!$sppg) return parent::getEloquentQuery()->whereRaw('1=0');
            return parent::getEloquentQuery()->where('sppg_id', $sppg->id);
        }
        
        if ($user->hasRole('PJ Pelaksana')) {
            $unitTugas = User::find($user->id)->unitTugas->first();
             if (!$unitTugas) return parent::getEloquentQuery()->whereRaw('1=0');
            return parent::getEloquentQuery()->where('sppg_id', $unitTugas->id);
        }

        return parent::getEloquentQuery();
    }
}
