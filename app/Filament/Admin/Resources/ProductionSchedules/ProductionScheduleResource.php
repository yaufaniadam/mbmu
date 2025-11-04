<?php

namespace App\Filament\Admin\Resources\ProductionSchedules;

use App\Filament\Admin\Resources\ProductionSchedules\Pages\CreateProductionSchedule;
use App\Filament\Admin\Resources\ProductionSchedules\Pages\EditProductionSchedule;
use App\Filament\Admin\Resources\ProductionSchedules\Pages\ListProductionSchedules;
use App\Filament\Admin\Resources\ProductionSchedules\Pages\ViewProductionSchedule;
use App\Filament\Admin\Resources\ProductionSchedules\Schemas\ProductionScheduleForm;
use App\Filament\Admin\Resources\ProductionSchedules\Schemas\ProductionScheduleInfolist;
use App\Filament\Admin\Resources\ProductionSchedules\Tables\ProductionSchedulesTable;
use App\Models\ProductionSchedule;
use App\Models\User;
use BackedEnum;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ProductionScheduleResource extends Resource
{
    protected static ?string $model = ProductionSchedule::class;

    protected static ?string $navigationLabel = 'Jadwal Produksi';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        // 1. Get the Schema object configured with static components
        $formSchema = ProductionScheduleForm::configure($schema);

        // 2. Prepare an empty array for our dynamic fields
        $dynamicSchoolComponents = [];

        /** @var User $user */
        $user = Auth::user();

        // 3. Get the user's schools (or an empty collection)
        $unitTugas = $user->unitTugas()->first();
        $schools = $unitTugas ? $unitTugas->schools : collect(); // Assuming 'schools' is the relationship name

        // 4. If the user has schools, create an inner Fieldset for each one
        if ($schools->isNotEmpty()) {

            // We use map() to transform each school into its own Fieldset component
            $schoolFieldsets = $schools->map(function ($school) {
                // This is the inner fieldset for each school
                return Fieldset::make($school->nama_sekolah) // Use school name as the label
                    ->schema([
                        // *** ADDED THIS HIDDEN FIELD ***
                        // This field holds the school ID as part of the data
                        Hidden::make('porsi_per_sekolah.' . $school->id . '.sekolah_id')
                            ->default($school->id),
                        // The path now uses dot notation for the nested JSON structure
                        TextInput::make('porsi_per_sekolah.' . $school->id . '.jumlah_porsi_besar')
                            ->label('Jumlah Porsi Besar')
                            ->numeric()
                            ->required(),
                        TextInput::make('porsi_per_sekolah.' . $school->id . '.jumlah_porsi_kecil')
                            ->label('Jumlah Porsi Kecil')
                            ->numeric()
                            ->required(),
                    ])
                    ->columns(2); // Two columns inside this inner fieldset
            })->all(); // Convert the collection to a plain array

            // 5. Wrap all the individual school fieldsets in one main Fieldset
            $dynamicSchoolComponents = [
                Fieldset::make('Jumlah Porsi Per Sekolah')
                    ->schema($schoolFieldsets) // Add the array of fieldsets
                    ->columns(1), // Stack each school's fieldset vertically
            ];
        }

        // 6. Get static components from the schema and merge with dynamic ones
        $staticComponents = $formSchema->getComponents();

        // 7. Return the final schema by setting its components
        return $schema->schema(
            array_merge(
                $staticComponents,
                $dynamicSchoolComponents
            )
        );
    }


    public static function infolist(Schema $schema): Schema
    {
        // 1. Call your static configure method
        // (Assuming you have created this file: app/Filament/Sppg/Resources/ProductionSchedules/Schemas/ProductionScheduleInfolist.php)
        $infolistSchema = ProductionScheduleInfolist::configure($schema);

        // 2. Get the static components from it
        $staticComponents = $infolistSchema->getComponents();

        // 3. Get user and schools
        $user = Auth::user();
        if (!$user) {
            return $infolistSchema->schema($staticComponents);
        }

        $unitTugas = $user->unitTugas()->first();
        if (!$unitTugas) {
            return $infolistSchema->schema($staticComponents);
        }

        $schools = $unitTugas->schools()->get();
        if ($schools->isEmpty()) {
            return $infolistSchema->schema($staticComponents);
        }

        // 4. Create dynamic infolist components
        $dynamicComponents = [];
        foreach ($schools as $school) {
            $dynamicComponents[] = FieldSet::make($school->nama_sekolah)
                ->schema([
                    TextEntry::make('jumlah_porsi_besar_for_' . $school->id)
                        ->label('Jumlah Porsi Besar')
                        // Use a custom state getter to find the right distribution
                        ->state(function (ProductionSchedule $record) use ($school) {
                            $distribution = $record->distributions()
                                ->where('sekolah_id', $school->id)
                                ->first();

                            return $distribution ? $distribution->jumlah_porsi_besar : '0';
                        }),
                    TextEntry::make('jumlah_porsi_kecil_for_' . $school->id)
                        ->label('Jumlah Porsi Kecil')
                        ->state(function (ProductionSchedule $record) use ($school) {
                            $distribution = $record->distributions()
                                ->where('sekolah_id', $school->id)
                                ->first();

                            return $distribution ? $distribution->jumlah_porsi_kecil : '0';
                        }),
                ])
                ->columns(2);
        }

        // 5. Merge static and dynamic components
        $allComponents = array_merge($staticComponents, $dynamicComponents);

        // 6. Return the final schema
        return $infolistSchema->schema($allComponents);
    }

    public static function table(Table $table): Table
    {
        return ProductionSchedulesTable::configure($table);
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
            'index' => ListProductionSchedules::route('/'),
            'create' => CreateProductionSchedule::route('/create'),
            'view' => ViewProductionSchedule::route('/{record}'),
            'edit' => EditProductionSchedule::route('/{record}/edit'),
        ];
    }
}
