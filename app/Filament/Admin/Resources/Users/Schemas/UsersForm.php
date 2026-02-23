<?php

namespace App\Filament\Admin\Resources\Users\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UsersForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Informasi Pengguna')
                    ->schema([
                        TextInput::make('name')
                            ->label('Name')
                            ->required(),
                        TextInput::make('email')
                            ->label('email')
                            ->email(),
                        TextInput::make('telepon')
                            ->label('Telepon')
                            ->tel()
                            ->required(),
                        Textarea::make('alamat')
                            ->label('Alamat')
                            ->required(),
                        TextInput::make('nik')
                            ->label('NIK')
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
                            ->label('Foto Profil')
                            ->image()
                            ->avatar()
                            ->directory('user-photos')
                            ->maxSize(10240)
                            ->columnSpanFull(),
                        Select::make('roles')
                            ->label('Jabatan')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->required()
                            ->live(),
                        Select::make('sppg_id')
                            ->label('SPPG')
                            ->relationship('sppg', 'nama_sppg')
                            ->searchable()
                            ->preload()
                            ->helperText('Pilih SPPG yang akan dikelola oleh user ini')
                            ->visible(function (callable $get): bool {
                                $roles = $get('roles') ?? [];
                                $sppgRoles = ['PJ Pelaksana', 'Kepala SPPG', 'Ahli Gizi', 'Staf Akuntan', 'Staf Administrator SPPG'];
                                
                                // Get role names from IDs
                                $roleNames = \Spatie\Permission\Models\Role::whereIn('id', $roles)->pluck('name')->toArray();
                                
                                return count(array_intersect($roleNames, $sppgRoles)) > 0;
                            }),
                        Select::make('lembaga_pengusul_id')
                            ->label('Lembaga Pengusul')
                            ->options(\App\Models\LembagaPengusul::pluck('nama_lembaga', 'id'))
                            ->searchable()
                            ->preload()
                            ->helperText('Pilih Lembaga Pengusul yang dipimpin oleh user ini')
                            ->visible(function (callable $get): bool {
                                $roles = $get('roles') ?? [];
                                
                                // Get role names from IDs
                                $roleNames = \Spatie\Permission\Models\Role::whereIn('id', $roles)->pluck('name')->toArray();
                                
                                return in_array('Pimpinan Lembaga Pengusul', $roleNames);
                            })
                            ->dehydrated(false), // Don't save to users table, handle separately
                    ])->columns(2),

                \Filament\Schemas\Components\Section::make('Keamanan')
                    ->schema([
                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->revealable() // Adds the eye icon
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->same('passwordConfirmation'),
                        TextInput::make('passwordConfirmation')
                            ->label('Konfirmasi Password')
                            ->password()
                            ->revealable()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(false),
                    ])->columns(2),

            ]);
    }
}
