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
                    ->afterStateUpdated(function (callable $set) {
                        $set('city_code', null);
                        $set('district_code', null);
                        $set('village_code', null);
                    }),
                Select::make('city_code')
                    ->label('Kota/Kabupaten')
                    ->options(function (callable $get) {
                        $province = $get('province_code');
                        if (! $province) return [];
                        return DB::table('indonesia_cities')
                            ->where('province_code', $province)
                            ->orderBy('name')
                            ->get()
                            ->mapWithKeys(fn($c) => [(string) $c->code => $c->name]);
                    })
                    ->live()
                    ->searchable()
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
                        if (! $city) return [];
                        return DB::table('indonesia_districts')
                            ->where('city_code', $city)
                            ->orderBy('name')
                            ->get()
                            ->mapWithKeys(fn($d) => [(string) $d->code => $d->name]);
                    })
                    ->live()
                    ->searchable()
                    ->dehydrateStateUsing(fn($state) => $state === null ? null : (string) $state)
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
                        if (! $district) return [];
                        return DB::table('indonesia_villages')
                            ->where('district_code', $district)
                            ->orderBy('name')
                            ->get()
                            ->mapWithKeys(fn($v) => [(string) $v->code => $v->name]);
                    })
                    ->live()
                    ->searchable()
                    ->dehydrateStateUsing(fn($state) => $state === null ? null : (string) $state)
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
                \App\Forms\Components\LocationPicker::make('location')
                    ->label('Lokasi di Peta')
                    ->columnSpanFull()
                    ->defaultLocation(-7.797068, 110.370529)
                    ->zoom(13)
                    ->height('300px')
                    ->latitudeField('latitude')
                    ->longitudeField('longitude'),
                TextInput::make('latitude')
                    ->label('Latitude')
                    ->disabled(),
                TextInput::make('longitude')
                    ->label('Longitude')
                    ->disabled(),
                \Filament\Forms\Components\FileUpload::make('photo_path')
                    ->label('Foto Sekolah/Penerima')
                    ->image()
                    ->directory('school-photos')
                    ->columnSpanFull(),
            ]);
    }
}
