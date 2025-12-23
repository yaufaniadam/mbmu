<?php

namespace App\Filament\Sppg\Resources\Schools\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;

class SchoolForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_sekolah')
                    ->label('Nama Penerima')
                    ->required(),
                \Filament\Schemas\Components\Grid::make(2)
                    ->schema([
                        TextInput::make('default_porsi_besar')
                            ->label('Default Porsi Besar')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                        TextInput::make('default_porsi_kecil')
                            ->label('Default Porsi Kecil')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                    ]),
                Textarea::make('alamat')
                    ->label('Alamat')
                    ->rows(3)
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
            ]);
    }
}
