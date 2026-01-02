<?php

namespace App\Filament\Admin\Resources\Volunteers\Schemas;

use App\Models\Sppg;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class VolunteerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Informasi Relawan')
                    ->schema([
                        Select::make('sppg_id')
                            ->label('Unit SPPG')
                            ->options(Sppg::pluck('nama_sppg', 'id'))
                            ->searchable()
                            ->required(),
                        Select::make('user_id')
                            ->label('Akun Pengguna Sistem')
                            ->helperText('Hubungkan jika relawan butuh akses aplikasi (misal: Kurir)')
                            ->options(\App\Models\User::pluck('name', 'id'))
                            ->searchable()
                            ->nullable(),
                        TextInput::make('nama_relawan')
                            ->label('Nama Lengkap')
                            ->required(),
                        TextInput::make('nik')
                            ->label('NIK / Identitas')
                            ->required(),
                        Select::make('gender')
                            ->label('Jenis Kelamin')
                            ->options([
                                'L' => 'Laki-laki',
                                'P' => 'Perempuan',
                            ]),
                        DatePicker::make('birth_date')
                            ->label('Tanggal Lahir'),
                        FileUpload::make('photo_path')
                            ->label('Foto Relawan')
                            ->image()
                            ->avatar()
                            ->directory('volunteer-photos')
                            ->columnSpanFull(),
                        Select::make('posisi')
                            ->label('Jabatan')
                            ->options([
                                'Asisten Lapangan' => 'Asisten Lapangan',
                                'Koordinator Bahan' => 'Koordinator Bahan',
                                'Koordinator Masak' => 'Koordinator Masak',
                                'Koordinator Pemorsian' => 'Koordinator Pemorsian',
                                'Koordinator Pencucian' => 'Koordinator Pencucian',
                                'Persiapan' => 'Persiapan',
                                'Masak' => 'Masak',
                                'Pemorsian' => 'Pemorsian',
                                'Distribusi' => 'Distribusi',
                                'Pencucian' => 'Pencucian',
                                'Cleaning Service' => 'Cleaning Service',
                            ])
                            ->searchable()
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn ($state, callable $set) => $set('category', $state)),
                        \Filament\Forms\Components\Hidden::make('category'),
                        TextInput::make('kontak')
                            ->label('Nomor WhatsApp/HP')
                            ->tel(),
                        TextInput::make('daily_rate')
                            ->label('Upah per Hari (Rate)')
                            ->numeric()
                            ->prefix('Rp')
                            ->helperText('Digunakan untuk perhitungan payroll otomatis ke depan'),
                        Textarea::make('address')
                            ->label('Alamat Lengkap')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
