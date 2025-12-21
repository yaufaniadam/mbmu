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
use UnitEnum;
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

    protected static ?string $navigationLabel = 'Rencana Distribusi';

    protected static string|UnitEnum|null $navigationGroup = 'Operasional';

    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    public static function getModelLabel(): string
    {
        return 'Rencana Distribusi';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Rencana Distribusi';
    }

    public static function getNavigationBadge(): ?string
    {
        $user = Auth::user();

        // Kepala SPPG → Only show schedules for their SPPG
        if ($user->hasRole('Kepala SPPG')) {
            $sppg = $user->sppgDikepalai;
            return $sppg ? ProductionSchedule::where([['sppg_id', '=', $sppg->id], ['status', '=', 'Menunggu ACC Kepala SPPG']])->count() : 0;
        }

        // PJ Pelaksana → Only schedules for their unit tugas
        if ($user->hasRole('PJ Pelaksana')) {
            $unitTugas = $user->unitTugas->first();

            return $unitTugas ? ProductionSchedule::where([['sppg_id', '=', $unitTugas->id], ['status', '=', 'Menunggu ACC Kepala SPPG']])->count() : 0;
        }

        // Default → all schedules
        return ProductionSchedule::count();
    }

    public static function form(Schema $schema): Schema
    {
        return ProductionScheduleForm::configure($schema);
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

                    Action::make('view_full_image_for_' . $school->id)
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

    public static function canCreate(): bool
    {
        $panelId = \Filament\Facades\Filament::getCurrentPanel()?->getId();
        
        // SPPG panel: Auto-generated only, no manual creation
        if ($panelId === 'sppg') {
            return false;
        }
        
        // Admin panel: Also read-only
        return $panelId !== 'admin';
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        if (\Filament\Facades\Filament::getCurrentPanel()?->getId() === 'admin') {
            return false;
        }

        return $record->status === 'Direncanakan';
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return \Filament\Facades\Filament::getCurrentPanel()?->getId() !== 'admin';
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
        $query = parent::getEloquentQuery()
            ->orderByDesc('tanggal');

        if ($user->hasRole('Kepala SPPG')) {
            $sppg = User::find($user->id)->sppgDikepalai;
            
            if (!$sppg) {
                return $query->whereRaw('1 = 0'); // Empty result if no SPPG
            }

            return $query->where('sppg_id', $sppg->id);
        }

        if ($user->hasAnyRole(['PJ Pelaksana', 'Ahli Gizi', 'Staf Administrator SPPG', 'Staf Akuntan', 'Staf Gizi', 'Staf Pengantaran'])) {
            $unitTugas = User::find($user->id)->unitTugas->first();

            if (!$unitTugas) {
                return $query->whereRaw('1 = 0'); // Empty result if no Unit Tugas
            }

            return $query->where('sppg_id', $unitTugas->id);
        }

        if ($user->hasRole('Pimpinan Lembaga Pengusul')) {
            $unitTugas = User::find($user->id)->lembagaDipimpin;

            if (!$unitTugas) {
                return $query->whereRaw('1 = 0'); // Empty result
            }

            return $query->whereHas('sppg', function (Builder $query) use ($unitTugas) {
                $query->where('lembaga_pengusul_id', $unitTugas->id);
            });
        }

        return $query;
    }
}
