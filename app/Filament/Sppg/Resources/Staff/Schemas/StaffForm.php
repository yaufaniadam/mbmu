<?php

namespace App\Filament\Sppg\Resources\Staff\Schemas;

use App\Models\User;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class StaffForm
{
    public static function configure(Schema $schema): Schema
    {
        $allowedRoleNames = [
            'Ahli Gizi',
            'Staf Gizi',
            'Staf Pengantaran',
            'Staf Akuntan',
            'PJ Pelaksana',
        ];

        $allowedRoleIds = Role::whereIn('name', $allowedRoleNames)->pluck('id')->toArray();

        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Informasi Staff')
                    ->schema([
                        \Filament\Schemas\Components\Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nama')
                                    ->required(),
                                TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->live(debounce: 500)
                                    ->rule('regex:/^.+@.+\..+$/') // Enforce dot in domain
                                    ->validationMessages([
                                        'required' => 'Email wajib diisi.',
                                        'email' => 'Format email tidak valid (harus mengandung @ dan domain).',
                                        'regex' => 'Format email tidak valid (kurang tanda titik pada domain, misal: .com atau .id).',
                                    ])
                                    ->required(),
                                \Filament\Forms\Components\FileUpload::make('photo_path')
                                    ->label('Foto')
                                    ->avatar()
                                    ->image()
                                    ->directory('staff-photos')
                                    ->columnSpanFull(),
                                Radio::make('gender')
                                    ->label('Jenis Kelamin')
                                    ->options([
                                        'Laki-laki' => 'Laki-laki',
                                        'Perempuan' => 'Perempuan',
                                    ])
                                    ->required(),
                                TextInput::make('telepon')
                                    ->label('Telepon')
                                    ->tel()
                                    ->live()
                                    ->rule('digits_between:11,14')
                                    ->validationMessages([
                                        'required' => 'Nomor telepon wajib diisi.',
                                        'digits_between' => 'Nomor telepon harus terdiri dari 11 sampai 14 digit angka.',
                                    ])
                                    ->required(),
                                TextInput::make('nik')
                                    ->label('NIK')
                                    ->live()
                                    ->mask('9999999999999999')
                                    ->rule('digits:16') 
                                    ->validationMessages([
                                        'required' => 'NIK wajib diisi.',
                                        'digits' => 'NIK harus terdiri tepat 16 digit angka.',
                                    ])
                                    ->required(),
                                Select::make('roles')
                                    ->label('Role')
                                    ->relationship(
                                        name: 'roles',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn(Builder $query) => $query->whereIn('name', $allowedRoleNames)
                                    )
                                    ->multiple()
                                    ->preload()
                                    ->required()
                                    // 🎯 CRITICAL: Add the backend validation rule 🎯
                                    ->rules([
                                        'array',
                                        'min:1',
                                        Rule::in($allowedRoleIds), // Ensure ALL submitted IDs are in the allowed list
                                    ]),
                                TextInput::make('password')
                                    ->label('Password')
                                    ->password()
                                    ->revealable()
                                    ->required(fn(string $context): bool => $context === 'create')
                                    ->dehydrated(fn($state) => filled($state))
                                    ->minLength(8)
                                    ->validationMessages([
                                        'required' => 'Password wajib diisi untuk staff baru.',
                                        'min' => 'Password minimal harus 8 karakter.',
                                    ]),
                            ]),
                        Textarea::make('alamat')
                            ->label('Alamat')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
                // TextInput::make('sppg')
                //     ->label('SPPG ID')
                //     ->default($organizationId),

            ]);
    }
}
