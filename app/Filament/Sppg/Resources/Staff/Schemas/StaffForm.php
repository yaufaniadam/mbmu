<?php

namespace App\Filament\Sppg\Resources\Staff\Schemas;

use App\Models\User;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class StaffForm
{
    public static function configure(Schema $schema): Schema
    {

        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama')
                    ->required(),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required(),
                Radio::make('gender')
                    ->label('Jenis Kelamin')
                    ->options([
                        'Laki-laki' => 'Laki-laki',
                        'Perempuan' => 'Perempuan',
                    ])
                    ->required(),
                TextInput::make('telepon')
                    ->label('Telepon')
                    ->required(),
                Textarea::make('alamat')
                    ->label('Alamat')
                    ->rows(3),
                TextInput::make('nik')
                    ->label('NIK')
                    ->required(),
                Select::make('role')
                    ->label('Role')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->required(),
                // TextInput::make('sppg')
                //     ->label('SPPG ID')
                //     ->default($organizationId),

            ]);
    }
}
