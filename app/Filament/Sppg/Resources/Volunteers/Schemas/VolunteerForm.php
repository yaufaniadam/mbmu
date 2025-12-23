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
                                    ->options(function ($record) {
                                        // Get SPPG ID from record or current authenticated user
                                        $sppgId = $record?->sppg_id;
                                        
                                        if (!$sppgId) {
                                            // Try to get from authenticated user's unitTugas
                                            $user = auth()->user();
                                            $sppgId = $user?->unitTugas()->first()?->id;
                                        }
                                        
                                        if ($sppgId) {
                                            // Get users from sppg_user_roles pivot table
                                            return \App\Models\User::whereHas('unitTugas', function ($query) use ($sppgId) {
                                                $query->where('sppg_id', $sppgId);
                                            })->pluck('name', 'id');
                                        }
                                        
                                        return \App\Models\User::pluck('name', 'id');
                                    })
                                    ->searchable()
                                    ->nullable()
                                    ->createOptionForm(function ($component) {
                                        // Get nama_relawan from parent form state
                                        $namaRelawan = $component->getLivewire()->data['nama_relawan'] ?? '';
                                        
                                        return [
                                            TextInput::make('name')
                                                ->label('Nama Lengkap')
                                                ->required()
                                                ->default($namaRelawan),
                                            TextInput::make('email')
                                                ->label('Email')
                                                ->email()
                                                ->required()
                                                ->unique('users', 'email'),
                                            TextInput::make('password')
                                                ->label('Password')
                                                ->password()
                                                ->revealable()
                                                ->required()
                                                ->minLength(6),
                                        ];
                                    })
                                    ->createOptionUsing(function (array $data, $record) {
                                        $user = \App\Models\User::create([
                                            'name' => $data['name'],
                                            'email' => $data['email'],
                                            'password' => \Illuminate\Support\Facades\Hash::make($data['password']),
                                        ]);
                                        
                                        // Get SPPG ID
                                        $sppgId = $record?->sppg_id ?? auth()->user()?->unitTugas()->first()?->id;
                                        
                                        // Assign role via Spatie (for Spatie permissions)
                                        $role = \Spatie\Permission\Models\Role::where('name', 'Staf Pengantaran')->first();
                                        if ($role) {
                                            $user->assignRole($role);
                                        }
                                        
                                        // Also assign to SPPG via pivot table
                                        if ($sppgId && $role) {
                                            \Illuminate\Support\Facades\DB::table('sppg_user_roles')->updateOrInsert(
                                                ['user_id' => $user->id, 'sppg_id' => $sppgId],
                                                ['role_id' => $role->id]
                                            );
                                        }
                                        
                                        return $user->id;
                                    }),
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
                                \Filament\Forms\Components\Select::make('posisi')
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
