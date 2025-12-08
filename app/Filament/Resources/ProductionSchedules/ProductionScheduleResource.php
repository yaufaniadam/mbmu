<?php

namespace App\Filament\Resources\ProductionSchedules;

use App\Filament\Resources\ProductionSchedules\Pages\CreateProductionSchedule;
use App\Filament\Resources\ProductionSchedules\Pages\EditProductionSchedule;
use App\Filament\Resources\ProductionSchedules\Pages\ListProductionSchedules;
use App\Filament\Resources\ProductionSchedules\Pages\ViewProductionSchedule;
use App\Filament\Resources\ProductionSchedules\Schemas\ProductionScheduleForm;
use App\Filament\Resources\ProductionSchedules\Schemas\ProductionScheduleInfolist;
use App\Filament\Resources\ProductionSchedules\Tables\ProductionSchedulesTable;
use App\Models\ProductionSchedule;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;

class ProductionScheduleResource extends Resource
{
    protected static ?string $model = ProductionSchedule::class;

    protected static ?string $navigationLabel = 'Jadwal Produksi';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function getNavigationBadge(): ?string
    {
        $user = Auth::user();

        // Kepala SPPG → Only show schedules for their SPPG
        if ($user->hasRole('Kepala SPPG')) {
            $sppg = $user->sppgDikepalai;

            return ProductionSchedule::where([['sppg_id', $sppg->id], ['status', '==', 'Menunggu ACC Kepala SPPG']])->count();
        }

        // PJ Pelaksana → Only schedules for their unit tugas
        if ($user->hasRole('PJ Pelaksana')) {
            $unitTugas = $user->unitTugas->first();

            return ProductionSchedule::where([['sppg_id', $unitTugas->id], ['status', '==', 'Menunggu ACC Kepala SPPG']])->count();
        }

        // Default → all schedules
        return ProductionSchedule::count();
    }

    public static function form(Schema $schema): Schema
    {
        // 1. Get the Schema object configured with static components
        $formSchema = ProductionScheduleForm::configure($schema);

        // 2. Prepare an empty array for our dynamic fields
        $dynamicSchoolComponents = [];

        /** @var User $user */
        $user = Auth::user();

        $sppg = null;

        if ($user->hasRole('Kepala SPPG')) {
            $sppg = User::find($user->id)->sppgDikepalai;
        }

        if ($user->hasRole('PJ Pelaksana')) {
            $sppg = User::find($user->id)->unitTugas->first();
        }

        $schools = $sppg ? $sppg->schools : collect(); // Assuming 'schools' is the relationship name

        // 4. If the user has schools, create an inner Fieldset for each one
        if ($schools->isNotEmpty()) {

            // We use map() to transform each school into its own Fieldset component
            $schoolFieldsets = $schools->map(function ($school) {
                $latest = $school->distributions()
                    ->orderByDesc('id')
                    ->first();
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
                            ->default($latest?->jumlah_porsi_besar ?? '')
                            ->required(),
                        TextInput::make('porsi_per_sekolah.' . $school->id . '.jumlah_porsi_kecil')
                            ->label('Jumlah Porsi Kecil')
                            ->numeric()
                            ->default($latest?->jumlah_porsi_kecil ?? '')
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

        // 3. Get schools
        $schools = $infolistSchema->model->schools;
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
                    TextEntry::make('status_pengantaran_for_' . $school->id)
                        ->label('Status Pengantaran')
                        ->state(function (ProductionSchedule $record) use ($school) {
                            $distribution = $record->distributions()
                                ->where('sekolah_id', $school->id)
                                ->first();

                            return $distribution ? $distribution->status_pengantaran : null;
                        })
                        ->badge()
                        ->color(fn(?string $state): string => match ($state) {
                            'Menunggu' => 'warning',
                            'Sedang Dikirim' => 'info',
                            'Terkirim' => 'success',
                            default => 'gray',
                        })
                        ->columnSpanFull(),
                    TextEntry::make('courier' . $school->id) // 's' ditambahkan untuk key unik
                        ->label('Petugas Pengantaran')
                        ->state(function (ProductionSchedule $record) use ($school) {
                            // Akses koleksi yang sudah dimuat
                            $distribution = $record->distributions
                                ->where('sekolah_id', $school->id)
                                ->first();

                            // dd($distribution->courier);

                            // Gunakan nullsafe operator (?->) yang kita bahas sebelumnya
                            return $distribution?->courier?->name;
                        })
                        ->badge()
                        ->columnSpanFull(),
                    ImageEntry::make('photo_of_proof_for_' . $school->id)
                        ->label('Foto Bukti Pengantaran')
                        ->columnSpanFull()
                        ->imageHeight('300px')
                        ->imageWidth('100%')
                        ->visible(function (ProductionSchedule $record) use ($school) {
                            $distribution = $record->distributions()
                                ->where('sekolah_id', $school->id)
                                ->first();
                            return $distribution->status_pengantaran == 'Terkirim';
                        })
                        ->state(function (ProductionSchedule $record) use ($school) {
                            $distribution = $record->distributions()
                                ->where('sekolah_id', $school->id)
                                ->first();
                            return $distribution->photo_of_proof;
                        }),

                    Action::make('view_full_image')
                        ->label('Lihat Gambar Penuh')
                        ->icon('heroicon-m-magnifying-glass-plus')
                        ->color('gray')
                        ->modalWidth('7xl') // Ukuran modal sangat besar (7xl)
                        ->modalHeading('Bukti Pengantaran - Tampilan Penuh')
                        ->modalSubmitAction(false) // Hilangkan tombol submit
                        ->modalCancelAction(false) // Hilangkan tombol cancel
                        // Render gambar menggunakan Base64 agar aman (tanpa public URL)
                        ->modalContent(function (ProductionSchedule $record) use ($school) {
                            $distribution = $record->distributions()
                                ->where('sekolah_id', $school->id)
                                ->first();
                            $path = $distribution->photo_of_proof;

                            if (!$path || !Storage::disk('local')->exists($path)) {
                                return new HtmlString('<div style="padding: 1rem; text-align: center; color: #ef4444;">File bukti pengantaran tidak ditemukan.</div>');
                            }

                            // Baca file dan konversi ke base64
                            $fileContent = Storage::disk('local')->get($path);
                            $mimeType = Storage::disk('local')->mimeType($path);
                            $base64 = base64_encode($fileContent);
                            $src = "data:{$mimeType};base64,{$base64}";

                            return new HtmlString('
                                        <div style="display: flex; justify-content: center; align-items: center; border-radius: 0.5rem; padding: 0.5rem;">
                                            <img src="' . $src . '" alt="Bukti Pengantaran Full" style="max-width: 100%; max-height: 85vh; object-fit: contain; border-radius: 8px;">
                                        </div>
                                    ');
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

    /**
     * Conditionally prevent editing based on record status.
     */
    public static function canEdit(Model $record): bool
    {
        return $record->status === 'Direncanakan';
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

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();
        if ($user->hasRole('Kepala SPPG')) {
            $sppg = User::find($user->id)->sppgDikepalai;

            return parent::getEloquentQuery()->where('sppg_id', $sppg->id);
        }

        if ($user->hasRole('PJ Pelaksana')) {
            $unitTugas = User::find($user->id)->unitTugas->first();

            return parent::getEloquentQuery()->where('sppg_id', $unitTugas->id);
        }

        return parent::getEloquentQuery();
    }
}
