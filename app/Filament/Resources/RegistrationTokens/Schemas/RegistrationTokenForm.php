<?php

namespace App\Filament\Resources\RegistrationTokens\Schemas;

use App\Models\RegistrationToken;
use App\Models\Sppg;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class RegistrationTokenForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Token')
                    ->schema([
                        Select::make('sppg_id')
                            ->label('SPPG')
                            ->options(
                                Sppg::query()
                                    ->whereIn('status', ['Proses Persiapan', 'Operasional / Siap Berjalan'])
                                    ->pluck('nama_sppg', 'id')
                            )
                            ->searchable()
                            ->required()
                            ->helperText('Hanya SPPG dengan status Proses Persiapan dan Operasional yang dapat menerima token'),
                        
                        Select::make('role')
                            ->label('Role/Jabatan')
                            ->options(RegistrationToken::ROLE_LABELS)
                            ->required(),
                        
                        TextInput::make('token')
                            ->label('Kode Token')
                            ->default(fn () => RegistrationToken::generateToken())
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(32)
                            ->helperText('Kode unik untuk registrasi'),
                    ])
                    ->columns(2),

                Section::make('Pengaturan')
                    ->schema([
                        TextInput::make('max_uses')
                            ->label('Maksimal Penggunaan')
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->required(),
                        
                        DateTimePicker::make('expires_at')
                            ->label('Berlaku Sampai')
                            ->nullable()
                            ->helperText('Kosongkan jika tidak ada batas waktu'),
                        
                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ])
                    ->columns(3),
                Section::make('Target Penerima (Opsional - untuk WA)')
                    ->description('Isi jika ingin mengirim token langsung via WhatsApp')
                    ->schema([
                        TextInput::make('recipient_name')
                            ->label('Nama Penerima')
                            ->placeholder('Contoh: Budi Santoso')
                            ->maxLength(255),
                        TextInput::make('recipient_phone')
                            ->label('Nomor WhatsApp')
                            ->tel()
                            ->placeholder('Contoh: 0812xxxx (Format Indonesia)')
                            ->helperText('Diperlukan untuk fitur Kirim WA'),
                    ])
                    ->columns(2),
            ]);
    }
}
