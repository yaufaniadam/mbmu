<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Auth\Pages\EditProfile as BaseEditProfile;

class EditProfile extends BaseEditProfile
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                TextInput::make('telepon')
                    ->label('No. Handphone / WhatsApp')
                    ->tel()
                    ->required()
                    ->maxLength(20)
                    ->unique(ignoreRecord: true)
                    ->helperText('Nomor ini digunakan untuk login. Format wajib: 628123xxx (tanpa + atau spasi).')
                    ->dehydrateStateUsing(function ($state) {
                        // Remove all non-numeric characters
                        $phone = preg_replace('/[^0-9]/', '', $state);
                        
                        // If starts with 0, replace with 62
                        if (str_starts_with($phone, '0')) {
                            $phone = '62' . substr($phone, 1);
                        }
                        
                        return $phone;
                    })
                    ->rule('regex:/^62[0-9]+$/')
                    ->validationMessages([
                        'regex' => 'Format nomor HP tidak valid. Harus berawalan 62 (contoh: 628...).',
                    ]),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
            ]);
    }
}
