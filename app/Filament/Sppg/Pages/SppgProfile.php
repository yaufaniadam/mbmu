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
            Fieldset::make('Rekening Bank')->schema([
                TextInput::make('nama_bank')
                    ->label('Nama Bank')
                    ->required(),
                TextInput::make('nomor_va')
                    ->label('Nomor Virtual Account')
                    ->required(),
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
                    ->dehydrateStateUsing(fn($state) => $state === null ? null : (string) $state)
                    ->disabled(fn($get) => ! $get('province_code')),
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
                    ->dehydrateStateUsing(fn($state) => $state === null ? null : (string) $state)
                    ->disabled(fn($get) => ! $get('city_code')),
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
                    ->dehydrateStateUsing(fn($state) => $state === null ? null : (string) $state)
                    ->disabled(fn($get) => ! $get('district_code')),
            ]),
            Fieldset::make('Koordinat')
                ->schema([
                    TextInput::make('latitude')
                        ->label('Latitude')
                        ->required(),
                    TextInput::make('longitude')
                        ->label('Longitude')
                        ->required(),
                ]),
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
