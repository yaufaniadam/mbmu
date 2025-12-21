<?php

namespace App\Filament\Sppg\Resources\Volunteers\Schemas;

use Filament\Forms\Components\Radio;
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
                        \Filament\Schemas\Components\Grid::make(2)
                            ->schema([
                                \Filament\Forms\Components\Select::make('user_id')
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
                                \Filament\Forms\Components\Select::make('gender')
                                    ->label('Jenis Kelamin')
                                    ->options([
                                        'L' => 'Laki-laki',
                                        'P' => 'Perempuan',
                                    ])
                                    ->required(),
                                \Filament\Forms\Components\Select::make('category')
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
                                    ->helperText('Untuk estimasi payroll otomatis'),
                            ]),
                        Textarea::make('address')
                            ->label('Alamat Lengkap')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
