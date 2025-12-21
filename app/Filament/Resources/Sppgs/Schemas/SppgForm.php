<?php

namespace App\Filament\Resources\Sppgs\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;

class SppgForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Data SPPG')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        Select::make('kepala_sppg_id')
                            ->label('Kepala SPPG')
                            ->relationship('kepalaSppg', 'name', modifyQueryUsing: fn(\Illuminate\Database\Eloquent\Builder $query) => $query->role('Kepala SPPG'))
                            ->searchable()
                            ->preload(),
                        Select::make('lembaga_pengusul_id')
                            ->label('Lembaga Pengusul')
                            ->relationship('lembagaPengusul', 'nama_lembaga'),
                        TextInput::make('nama_sppg')
                            ->label('Nama SPPG')
                            ->required(),
                        TextInput::make('kode_sppg')
                            ->label('Kode SPPG')
                            ->required(),
                        TextInput::make('nama_bank')
                            ->label('Nama Bank')
                            ->required(),
                        TextInput::make('nomor_va')
                            ->label('Nomor VA')
                            ->required(),
                        DatePicker::make('tanggal_mulai_sewa')
                            ->label('Tanggal SPPG Mulai Beroperasi')
                            ->helperText('Bisa diisikan tanggal sppg akan mulai ditagih, jika sppg sudah beroperasi sebelum aplikasi ini dibuat.')
                            ->required(),
                        Textarea::make('alamat')
                            ->label('Alamat')
                            ->required(),
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
                        TextInput::make('latitude')
                            ->label('Latitude'),
                        TextInput::make('longitude')
                            ->label('Longitude')
                    ])
            ]);

        // return $schema
        //     ->components([
        //         Wizard::make([
        //             Step::make('Informasi SPPG')
        //                 ->schema([
        //                     TextInput::make('nama_sppg')
        //                         ->label('Nama SPPG')
        //                         ->required(),
        //                     TextInput::make('kode_sppg')
        //                         ->label('Kode SPPG')
        //                         ->required(),
        //                     TextInput::make('nama_bank')
        //                         ->label('Nama Bank')
        //                         ->required(),
        //                     TextInput::make('nomor_va')
        //                         ->label('Nomor VA')
        //                         ->required(),
        //                     Textarea::make('alamat')
        //                         ->label('Alamat')
        //                         ->required(),
        //                     Select::make('province_code')
        //                         ->label('Provinsi')
        //                         ->options(function () {
        //                             $provinces = DB::table('indonesia_provinces')->select('code', 'name')->orderBy('name')->get();

        //                             return $provinces->pluck('name', 'code');
        //                         })
        //                         ->live()
        //                         ->searchable()
        //                         ->afterStateUpdated(function (callable $set) {
        //                             $set('city_code', null);
        //                             $set('district_code', null);
        //                             $set('village_code', null);
        //                         }),
        //                     Select::make('city_code')
        //                         ->label('Kota/Kabupaten')
        //                         ->options(function (callable $get) {

        //                             $provinceCode = $get('province_code');

        //                             if (! $provinceCode) {
        //                                 return [];
        //                             }

        //                             $cities = DB::table('indonesia_cities')
        //                                 ->where('province_code', $provinceCode)
        //                                 ->select('code', 'name')
        //                                 ->orderBy('name')
        //                                 ->get();

        //                             return $cities->pluck('name', 'code');
        //                         })
        //                         ->live()
        //                         ->searchable()
        //                         ->disabled(fn (callable $get) => ! $get('province_code')),
        //                     Select::make('district_code')
        //                         ->label('Kecamatan')
        //                         ->options(function (callable $get) {

        //                             $cityCode = $get('city_code');

        //                             if (! $cityCode) {
        //                                 return [];
        //                             }

        //                             $districts = DB::table('indonesia_districts')
        //                                 ->where('city_code', $cityCode)
        //                                 ->select('code', 'name')
        //                                 ->orderBy('name')
        //                                 ->get();

        //                             return $districts->pluck('name', 'code');
        //                         })
        //                         ->live()
        //                         ->searchable()
        //                         ->disabled(fn (callable $get) => ! $get('city_code')),
        //                     Select::make('village_code')
        //                         ->label('Kelurahan/Desa')
        //                         ->options(function (callable $get) {

        //                             $districtCode = $get('district_code');

        //                             if (! $districtCode) {
        //                                 return [];
        //                             }

        //                             $villages = DB::table('indonesia_villages')
        //                                 ->where('district_code', $districtCode)
        //                                 ->select('code', 'name')
        //                                 ->orderBy('name')
        //                                 ->get();

        //                             return $villages->pluck('name', 'code');
        //                         })
        //                         ->live()
        //                         ->searchable()
        //                         ->disabled(fn (callable $get) => ! $get('district_code')),
        //                 ]),
        //             Step::make('Pengusul')
        //                 ->schema([
        //                     Select::make('kepala_sppg_id')
        //                         ->label('Kepala SPPG')
        //                         ->relationship('kepalaSppg', 'name'),
        //                     Select::make('lembaga_pengusul_id')
        //                         ->label('Lembaga Pengusul')
        //                         ->relationship('lembagaPengusul', 'nama_lembaga'),
        //                 ]),
        //         ]),
        //     ]);
    }
}
