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

class SppgProfile extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.sppg-profile';

    protected static string|\UnitEnum|null $navigationGroup = 'Pengaturan';

    protected static ?string $navigationLabel = 'Profil SPPG';

    protected ?string $heading = 'Profil SPPG';

    public ?array $data = [];

    public Sppg $sppg;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-cog-8-tooth';
    }

    public function getFormStatePath(): string
    {
        return 'data';
    }

    public function mount(): void
    {
        $user = Auth::user();

        if ($user->hasRole('Kepala SPPG')) {
            $this->sppg = User::find($user->id)->sppgDikepalai;
        } else {
            $this->sppg = User::find($user->id)->unitTugas->first();
        }

        // dd($this->sppg->attributesToArray());

        $this->form->fill($this->sppg->attributesToArray());
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
                        $provinces = DB::table('indonesia_provinces')->select('code', 'name')->orderBy('name')->get();

                        return $provinces->pluck('name', 'code');
                    })
                    ->string()
                    ->live()
                    ->searchable()
                    ->afterStateUpdated(function (callable $set) {
                        $set('city_code', null);
                        $set('district_code', null);
                        $set('village_code', null);
                    }),
                Select::make('city_code')
                    ->label('Kota/Kabupaten')
                    ->options(function (callable $get) {

                        $provinceCode = $get('province_code');

                        if (! $provinceCode) {
                            return [];
                        }

                        $cities = DB::table('indonesia_cities')
                            ->where('province_code', $provinceCode)
                            ->select('code', 'name')
                            ->orderBy('name')
                            ->get();

                        return $cities->pluck('name', 'code');
                    })
                    ->string()
                    ->live()
                    ->searchable()
                    ->disabled(fn (callable $get) => ! $get('province_code')),
                Select::make('district_code')
                    ->label('Kecamatan')
                    ->options(function (callable $get) {

                        $cityCode = $get('city_code');

                        if (! $cityCode) {
                            return [];
                        }

                        $districts = DB::table('indonesia_districts')
                            ->where('city_code', $cityCode)
                            ->select('code', 'name')
                            ->orderBy('name')
                            ->get();

                        return $districts->pluck('name', 'code');
                    })
                    ->string()
                    ->live()
                    ->searchable()
                    ->disabled(fn (callable $get) => ! $get('city_code')),
                Select::make('village_code')
                    ->label('Kelurahan/Desa')
                    ->options(function (callable $get) {

                        $districtCode = $get('district_code');

                        if (! $districtCode) {
                            return [];
                        }

                        $villages = DB::table('indonesia_villages')
                            ->where('district_code', $districtCode)
                            ->select('code', 'name')
                            ->orderBy('name')
                            ->get();

                        // return $villages->pluck('name', 'code');

                        return $villages->mapWithKeys(fn ($v) => [
                            (string) $v->code => $v->name,
                        ]);
                    })
                    ->string()
                    ->live()
                    ->searchable()
                    ->disabled(fn (callable $get) => ! $get('district_code')),
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
