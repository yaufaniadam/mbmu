<?php

namespace App\Filament\Sppg\Pages;

use App\Models\Sppg;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Fieldset;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class SppgProfile extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.sppg-profile';

    protected static string|\UnitEnum|null $navigationGroup = 'Pengaturan';

    protected static ?string $navigationLabel = 'Profil SPPG';

    protected static ?int $navigationSort = 1;

    protected ?string $heading = 'Profil SPPG';

    public ?array $data = [];

    public ?Sppg $sppg = null;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-cog-8-tooth';
    }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }

    public static function shouldRegisterNavigation(): bool
    {
        // This checks the permission you just generated
        return auth()->user()->can('manage-sppg-profile');
    }

    public function getFormStatePath(): string
    {
        return 'data';
    }

    public function mount(): void
    {
        Gate::authorize('manage-sppg-profile');

        $user = Auth::user();

        if ($user->hasRole('Kepala SPPG')) {
            $this->sppg = User::find($user->id)->sppgDikepalai;
        } else {
            $this->sppg = User::find($user->id)->unitTugas->first();
        }

        if ($this->sppg) {
            $this->form->fill($this->sppg->attributesToArray());
        } else {
            // Handle empty state, maybe just empty form or notification
             Notification::make()
                ->title('Data SPPG Belum Ada')
                ->body('Akun Anda belum terhubung dengan unit SPPG manapun.')
                ->warning()
                ->send();
        }
    }

    public function getFormSchema(): array
    {
        return [
            Fieldset::make('Informasi SPPG')->schema([
                TextInput::make('nama_sppg')
                    ->label('Nama SPPG')
                    ->required(),
                TextInput::make('kode_sppg')
                    ->label('Kode SPPG')
                    ->required()
                    ->disabled() // Kode probably shouldn't be editable
                    ->unique(ignoreRecord: true), // Ignore this record when checking unique
            ]),
            \Filament\Schemas\Components\Section::make('Foto')->schema([
                \Filament\Forms\Components\FileUpload::make('photo_path')
                    ->label('Foto SPPG')
                    ->image()
                    ->disk('public')
                    ->directory('sppg-photos')
                    ->visibility('public')
                    ->required()
                    ->validationMessages([
                        'required' => 'Foto wajib diisi',
                    ])
                    ->columnSpanFull(),
                \Filament\Forms\Components\FileUpload::make('gallery_photos')
                    ->label('Galeri Foto')
                    ->multiple()
                    ->reorderable()
                    ->image()
                    ->disk('public')
                    ->directory('sppg-gallery')
                    ->visibility('public')
                    ->downloadable()
                    ->openable()
                    ->imagePreviewHeight('250')
                    ->panelLayout('grid')
                    ->columnSpanFull(),
            ]),
            Fieldset::make('Rekening Bank')->schema([
                TextInput::make('nama_bank')
                    ->label('Nama Bank')
                    ->required(),
                TextInput::make('nomor_va')
                    ->label('Nomor Virtual Account')
                    ->required(),
            ]),
            Fieldset::make('Kapasitas')->schema([
                TextInput::make('porsi_besar')
                    ->label('Kapasitas Porsi Besar')
                    ->numeric()
                    ->default(0)
                    ->required()
                    ->validationMessages([
                        'required' => 'Kapasitas porsi besar wajib diisi',
                    ]),
                TextInput::make('porsi_kecil')
                    ->label('Kapasitas Porsi Kecil')
                    ->numeric()
                    ->default(0)
                    ->required()
                    ->validationMessages([
                        'required' => 'Kapasitas porsi kecil wajib diisi',
                    ]),
            ]),
            Fieldset::make('Alamat Lengkap')->schema([
                Textarea::make('alamat')
                    ->label('Alamat')
                    ->required()
                    ->columnSpanFull()
                    ->columns(2),
                Select::make('province_code')
                    ->label('Provinsi')
                    ->options(function () {
                        return DB::table('indonesia_provinces')
                            ->orderBy('name')
                            ->get()
                            ->mapWithKeys(fn($p) => [
                                (string) $p->code => $p->name,
                            ]);
                    })
                    ->live()
                    ->searchable()
                    ->required()
                    ->validationMessages([
                        'required' => 'Provinsi wajib diisi',
                    ])
                    ->dehydrateStateUsing(fn($state) => $state === null ? null : (string) $state)
                    ->afterStateUpdated(function ($set) {
                        $set('city_code', null);
                        $set('district_code', null);
                        $set('village_code', null);
                    }),
                Select::make('city_code')
                    ->label('Kota/Kabupaten')
                    ->options(function (callable $get) {
                        $province = $get('province_code');
                        if (! $province) {
                            return [];
                        }

                        return DB::table('indonesia_cities')
                            ->where('province_code', $province)
                            ->orderBy('name')
                            ->get()
                            ->mapWithKeys(fn($c) => [
                                (string) $c->code => $c->name,
                            ]);
                    })
                    ->live()
                    ->searchable()
                    ->required()
                    ->validationMessages([
                        'required' => 'Kota/Kabupaten wajib diisi',
                    ])
                    ->dehydrateStateUsing(fn($state) => $state === null ? null : (string) $state)
                    ->disabled(fn($get) => ! $get('province_code'))
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $set('district_code', null);
                        $set('village_code', null);
                        
                        // Geocode City
                        if ($state) {
                            $city = DB::table('indonesia_cities')->where('code', $state)->value('name');
                            $province = DB::table('indonesia_provinces')->where('code', $get('province_code'))->value('name');
                            if ($city && $province) {
                                try {
                                    $response = \Illuminate\Support\Facades\Http::withUserAgent('MBM-System/1.0')
                                        ->timeout(5)
                                        ->get('https://nominatim.openstreetmap.org/search', [
                                            'q' => "$city, $province, Indonesia",
                                            'format' => 'json',
                                            'limit' => 1
                                        ]);
                                    if ($data = $response->json()[0] ?? null) {
                                        $set('location', ['lat' => $data['lat'], 'lng' => $data['lon'], 'zoom' => 12]);
                                        $set('latitude', $data['lat']);
                                        $set('longitude', $data['lon']);
                                    }
                                } catch (\Exception $e) {}
                            }
                        }
                    }),
                Select::make('district_code')
                    ->label('Kecamatan')
                    ->options(function (callable $get) {
                        $city = $get('city_code');
                        if (! $city) {
                            return [];
                        }

                        return DB::table('indonesia_districts')
                            ->where('city_code', $city)
                            ->orderBy('name')
                            ->get()
                            ->mapWithKeys(fn($d) => [
                                (string) $d->code => $d->name,
                            ]);
                    })
                    ->live()
                    ->searchable()
                    ->required()
                    ->validationMessages([
                        'required' => 'Kecamatan wajib diisi',
                    ])
                    ->dehydrateStateUsing(fn($state) => $state === null ? null : (string) $state)
                    ->disabled(fn($get) => ! $get('city_code'))
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $set('village_code', null);
                        
                        // Geocode District
                        if ($state) {
                            $district = DB::table('indonesia_districts')->where('code', $state)->value('name');
                            $city = DB::table('indonesia_cities')->where('code', $get('city_code'))->value('name');
                            $province = DB::table('indonesia_provinces')->where('code', $get('province_code'))->value('name');
                            if ($district && $city && $province) {
                                try {
                                    $response = \Illuminate\Support\Facades\Http::withUserAgent('MBM-System/1.0')
                                        ->timeout(5)
                                        ->get('https://nominatim.openstreetmap.org/search', [
                                            'q' => "$district, $city, $province, Indonesia",
                                            'format' => 'json',
                                            'limit' => 1
                                        ]);
                                    if ($data = $response->json()[0] ?? null) {
                                        $set('location', ['lat' => $data['lat'], 'lng' => $data['lon'], 'zoom' => 14]);
                                        $set('latitude', $data['lat']);
                                        $set('longitude', $data['lon']);
                                    }
                                } catch (\Exception $e) {}
                            }
                        }
                    }),
                Select::make('village_code')
                    ->label('Kelurahan/Desa')
                    ->options(function (callable $get) {
                        $district = $get('district_code');
                        if (! $district) {
                            return [];
                        }

                        return DB::table('indonesia_villages')
                            ->where('district_code', $district)
                            ->orderBy('name')
                            ->get()
                            ->mapWithKeys(fn($v) => [
                                (string) $v->code => $v->name,
                            ]);
                    })
                    ->live()
                    ->searchable()
                    ->required()
                    ->validationMessages([
                        'required' => 'Kelurahan/Desa wajib diisi',
                    ])
                    ->dehydrateStateUsing(fn($state) => $state === null ? null : (string) $state)
                    ->disabled(fn($get) => ! $get('district_code'))
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        // Geocode Village
                        if ($state) {
                            $village = DB::table('indonesia_villages')->where('code', $state)->value('name');
                            $district = DB::table('indonesia_districts')->where('code', $get('district_code'))->value('name');
                            $city = DB::table('indonesia_cities')->where('code', $get('city_code'))->value('name');
                            $province = DB::table('indonesia_provinces')->where('code', $get('province_code'))->value('name');
                            if ($village && $district && $city && $province) {
                                try {
                                    $response = \Illuminate\Support\Facades\Http::withUserAgent('MBM-System/1.0')
                                        ->timeout(5)
                                        ->get('https://nominatim.openstreetmap.org/search', [
                                            'q' => "$village, $district, $city, $province, Indonesia",
                                            'format' => 'json',
                                            'limit' => 1
                                        ]);
                                    if ($data = $response->json()[0] ?? null) {
                                        $set('location', ['lat' => $data['lat'], 'lng' => $data['lon'], 'zoom' => 16]);
                                        $set('latitude', $data['lat']);
                                        $set('longitude', $data['lon']);
                                    }
                                } catch (\Exception $e) {}
                            }
                        }
                    }),
            ]),
            Fieldset::make('Koordinat')
                ->schema([
                    \App\Forms\Components\LocationPicker::make('location')
                        ->label('Lokasi di Peta (Drag Marker)')
                        ->columnSpanFull()
                        ->defaultLocation(-7.797068, 110.370529)
                        ->zoom(13)
                        ->height('300px')
                        ->required()
                        ->validationMessages([
                            'required' => 'Lokasi di peta wajib diisi',
                        ])
                        ->latitudeField('latitude')
                        ->longitudeField('longitude'),
                    TextInput::make('latitude')
                        ->label('Latitude')
                        ->disabled()
                        ->dehydrated()
                        ->required()
                        ->validationMessages([
                            'required' => 'Latitude wajib diisi',
                        ]),
                    TextInput::make('longitude')
                        ->label('Longitude')
                        ->disabled()
                        ->dehydrated()
                        ->required()
                        ->validationMessages([
                            'required' => 'Longitude wajib diisi',
                        ]),
                ]),
            Fieldset::make('Dokumen SPPG')
                ->schema([
                    \Filament\Forms\Components\FileUpload::make('izin_operasional_path')
                        ->label('Dokumen Verval')
                        ->directory('sppg-docs')
                        ->acceptedFileTypes(['application/pdf'])
                        ->required()
                        ->validationMessages([
                            'required' => 'Dokumen verval wajib diisi',
                        ]),
                    \Filament\Forms\Components\FileUpload::make('sertifikat_akreditasi_path')
                        ->label('Sertifikat Akreditasi')
                        ->directory('sppg-docs')
                        ->acceptedFileTypes(['application/pdf'])
                        ->visibility('public'),
                    \Filament\Forms\Components\FileUpload::make('sertifikat_halal_path')
                        ->label('Sertifikat Halal')
                        ->directory('sppg-docs')
                        ->acceptedFileTypes(['application/pdf', 'image/*']),
                    \Filament\Forms\Components\FileUpload::make('slhs_path')
                        ->label('SLHS')
                        ->directory('sppg-docs')
                        ->acceptedFileTypes(['application/pdf', 'image/*']),
                    \Filament\Forms\Components\FileUpload::make('lhaccp_path')
                        ->label('HACCP')
                        ->directory('sppg-docs')
                        ->acceptedFileTypes(['application/pdf', 'image/*']),
                    \Filament\Forms\Components\FileUpload::make('iso_path')
                        ->label('ISO')
                        ->directory('sppg-docs')
                        ->acceptedFileTypes(['application/pdf', 'image/*']),
                    \Filament\Forms\Components\FileUpload::make('sertifikat_lahan_path')
                        ->label('Sertifikat Lahan')
                        ->directory('sppg-docs')
                        ->acceptedFileTypes(['application/pdf', 'image/*']),
                    \Filament\Forms\Components\FileUpload::make('dokumen_lain_path')
                        ->label('Dokumen Lain-lain')
                        ->directory('sppg-docs')
                        ->acceptedFileTypes(['application/pdf', 'image/*']),
                ])->columns(2),
        ];
    }

    public function save(): void
    {
        // Get the data from the form
        $data = $this->form->getState();

        // Update the Sppg model
        $this->sppg->update($data);

        // Send a success notification
        Notification::make()
            ->title('Profil SPPG berhasil disimpan')
            ->success()
            ->send();
    }
}
