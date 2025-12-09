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
            'Staf Gizi',
            'Staf Pengantaran',
            'Staf Akuntan',
            'PJ Pelaksana',
        ];

        $allowedRoleIds = Role::whereIn('name', $allowedRoleNames)->pluck('id')->toArray();

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
                // TextInput::make('sppg')
                //     ->label('SPPG ID')
                //     ->default($organizationId),

            ]);
    }
}
