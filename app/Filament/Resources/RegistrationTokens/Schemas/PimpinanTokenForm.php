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

class PimpinanTokenForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Token')
                    ->schema([
                        Select::make('sppg_id')
                            ->label('Lembaga Pengusul')
                            ->options(
                                Sppg::query()
                                    ->with('lembagaPengusul')
                                    ->whereIn('status', ['Proses Persiapan', 'Operasional / Siap Berjalan'])
                                    ->get()
                                    ->mapWithKeys(function ($sppg) {
                                        $label = $sppg->lembagaPengusul->nama_lembaga ?? $sppg->nama_sppg;
                                        return [$sppg->id => $label];
                                    })
                                    ->toArray()
                            )
                            ->getOptionLabelUsing(fn ($value): ?string => Sppg::find($value)?->lembagaPengusul?->nama_lembaga ?? Sppg::find($value)?->nama_sppg)
                            ->searchable()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if (!$state) return;
                                
                                $sppg = Sppg::find($state);
                                if (!$sppg) return;

                                // Always fetch Pimpinan data for this form
                                if ($sppg->lembagaPengusul && $sppg->lembagaPengusul->pimpinan) {
                                    $user = $sppg->lembagaPengusul->pimpinan;
                                    $set('recipient_name', $user->name);
                                    $set('recipient_phone', $user->telepon);
                                } else {
                                     // Clear if not found, to avoid confusion
                                     $set('recipient_name', null);
                                     $set('recipient_phone', null);
                                }
                            })
                            ->helperText('Pilih Lembaga Pengusul yang akan menerima token'),
                        
                        Select::make('role')
                            ->label('Role/Jabatan')
                            ->options([
                                'kepala_lembaga' => 'Kepala Lembaga Pengusul',
                            ])
                            ->default('kepala_lembaga')
                            ->disabled() // Lock this form to only this role
                            ->dehydrated() // Ensure it is saved
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
                            ->default(1)
                            ->hidden(),
                        
                        DateTimePicker::make('expires_at')
                            ->label('Berlaku Sampai')
                            ->nullable()
                            ->helperText('Kosongkan jika tidak ada batas waktu')
                            ->hidden(),
                        
                        Toggle::make('is_active')
                            ->default(true)
                            ->hidden(),
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
                            ->placeholder('Format: 0812xxxx')
                            ->helperText('Diperlukan untuk fitur Kirim WA'),
                    ])
                    ->columns(2),
            ]);
    }
}
