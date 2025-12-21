<?php

namespace App\Filament\Admin\Resources\LembagaPengusuls\Schemas;

use App\Models\Sppg;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Livewire\Component as Livewire;

class LembagaPengusulForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Lembaga')
                    ->schema([
                        TextInput::make('nama_lembaga')
                            ->required(),
                        Textarea::make('alamat_lembaga')
                            ->required()
                            ->columnSpanFull(),
                    ]),
                Section::make('Akun Pimpinan Lembaga')
                    ->description('Pilih akun pimpinan atau buat baru.')
                    ->schema([
                        Select::make('pimpinan_id')
                            ->label('Pimpinan Lembaga')
                            ->options(User::pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('Nama Lengkap')
                                    ->required(),
                                TextInput::make('email')
                                    ->email()
                                    ->required()
                                    ->unique('users', 'email'),
                            ])
                            ->createOptionUsing(function (array $data) {
                                $user = User::create([
                                    'name' => $data['name'],
                                    'email' => $data['email'],
                                    'password' => Hash::make('p4$$w0rd'),
                                ]);

                                $user->assignRole('Pimpinan Lembaga Pengusul');

                                return $user->id;
                            }),
                    ])->columns(1),
            ]);
    }
}
