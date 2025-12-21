<?php

namespace App\Filament\Admin\Resources\Volunteers\Schemas;

use App\Models\Sppg;
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
                            ->options([
                                'L' => 'Laki-laki',
                                'P' => 'Perempuan',
                            ])
                            ->required(),
                        Select::make('category')
                            ->label('Kategori Relawan')
                            ->options([
                                'Masak' => 'Juru Masak / Koki',
                                'Asisten Dapur' => 'Asisten Dapur',
                                'Pengantaran' => 'Staf Pengantaran / Kurir',
                                'Kebersihan' => 'Tenaga Kebersihan',
                                'Keamanan' => 'Tenaga Keamanan',
                                'Administrasi' => 'Staf Administrasi',
                                'Lainnya' => 'Lainnya',
                            ])
                            ->required(),
                        TextInput::make('posisi')
                            ->label('Posisi Spesifik')
                            ->placeholder('Contoh: Kepala Koki, Driver Motor, dll')
                            ->required(),
                        TextInput::make('kontak')
                            ->label('Nomor WhatsApp/HP')
                            ->tel()
                            ->required(),
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
