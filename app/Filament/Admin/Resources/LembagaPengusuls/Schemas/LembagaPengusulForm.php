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
                Wizard::make([
                    // --- STEP 1: Lembaga & Pimpinan ---
                    Step::make('Detail Lembaga & Pimpinan')
                        ->description('Data Lembaga Pengusul dan akun Pimpinan.')

                        ->schema([
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
                                    Select::make('pimpinan_id') // Store the ID, not the name
                                        ->label('Pimpinan Lembaga')
                                        ->options(User::pluck('name', 'id')) // Simple options load
                                        ->searchable()
                                        ->preload()
                                        // ->required()

                                        // 1. Define the Modal Form
                                        ->createOptionForm([
                                            TextInput::make('name')
                                                ->label('Nama Lengkap')
                                                ->required(),
                                            TextInput::make('email')
                                                ->email()
                                                ->required()
                                                ->unique('users', 'email'),
                                        ])

                                        // 2. Define the Immediate Save Logic
                                        ->createOptionUsing(function (array $data) {
                                            // This runs IMMEDIATELY when the modal is submitted
                                            $user = User::create([
                                                'name' => $data['name'],
                                                'email' => $data['email'],
                                                'password' => Hash::make('p4$$w0rd'),
                                            ]);

                                            $user->assignRole('Pimpinan Lembaga Pengusul');

                                            // The returned ID is automatically selected in the dropdown
                                            return $user->id;
                                        }),
                                ])->columns(1),
                        ]),

                    // --- STEP 2: SPPGs & Kepala SPPG ---
                    Step::make('Daftar SPPG')
                        ->description('Tambahkan SPPG yang berada di bawah lembaga ini.')
                        ->schema([
                            Repeater::make('sppgs')
                                ->label('Daftar SPPG')
                                ->formatStateUsing(function ($record) {
                                    // If we are creating a record, return empty
                                    if (! $record) {
                                        return [];
                                    }

                                    // If we are editing, load the related SPPGs
                                    // and map them to the format the repeater expects: ['sppg_id' => 123]
                                    return $record->sppgs->map(function ($sppg) {
                                        return [
                                            'sppg_id' => $sppg->id,
                                        ];
                                    })->toArray();
                                })
                                ->schema([
                                    Section::make('SPPG')
                                        ->description('Pilih sppg atau buat baru.')
                                        ->schema([
                                            Select::make('sppg_id') // Store the ID, not the name
                                                ->label('SPPG')
                                                ->live()

                                                // 2. Filter the options
                                                ->options(function (Get $get, Livewire $livewire) {
                                                    $editingLembagaId = $livewire->record->id ?? null;
                                                    $allSelectedIds = collect($get('../../sppgs'))->pluck('sppg_id')->filter();
                                                    $currentId = $get('sppg_id');

                                                    return Sppg::query()
                                                        // Global Filter: Unassigned OR Owned by this Lembaga
                                                        ->where(function ($query) use ($editingLembagaId) {
                                                            $query->whereNull('lembaga_pengusul_id')
                                                                ->when($editingLembagaId, function ($q, $id) {
                                                                    $q->orWhere('lembaga_pengusul_id', $id);
                                                                });
                                                        })
                                                        // Local Filter: Unique in Repeater
                                                        ->where(function ($query) use ($allSelectedIds, $currentId) {
                                                            $query->whereNotIn('id', $allSelectedIds);
                                                            if ($currentId) {
                                                                $query->orWhere('id', $currentId);
                                                            }
                                                        })
                                                        // ->where('lembaga_pengusul_id', null)
                                                        ->pluck('nama_sppg', 'id');
                                                })
                                                ->searchable()
                                                ->preload()
                                                // ->required()

                                                // 1. Define the Modal Form
                                                ->createOptionForm([
                                                    TextInput::make('nama_sppg')
                                                        ->label('Nama SPPG')
                                                        ->required(),
                                                    TextInput::make('kode_sppg')
                                                        ->label('Kode SPPG')
                                                        ->required(),
                                                    DatePicker::make('tanggal_mulai_sewa')
                                                        ->label('Tanggal SPPG Mulai Beroperasi')
                                                        ->helperText('Bisa diisikan tanggal sppg akan mulai ditagih, jika sppg sudah beroperasi sebelum aplikasi ini dibuat.')
                                                        ->required(),
                                                    Section::make('Akun Kepala SPPG')
                                                        ->description('Pilih akun kepala sppg atau buat baru.')
                                                        ->schema([
                                                            Select::make('kepala_sppg_id') // Store the ID, not the name
                                                                ->label('Kepala SPPG')
                                                                ->options(User::pluck('name', 'id')) // Simple options load
                                                                ->searchable()
                                                                ->preload()
                                                                // ->required()

                                                                // 1. Define the Modal Form
                                                                ->createOptionForm([
                                                                    TextInput::make('name')
                                                                        ->label('Nama Lengkap')
                                                                        ->required(),
                                                                    TextInput::make('email')
                                                                        ->email()
                                                                        ->required()
                                                                        ->unique('users', 'email'),
                                                                ])

                                                                // 2. Define the Immediate Save Logic
                                                                ->createOptionUsing(function (array $data) {
                                                                    // This runs IMMEDIATELY when the modal is submitted
                                                                    $user = User::create([
                                                                        'name' => $data['name'],
                                                                        'email' => $data['email'],
                                                                        'password' => Hash::make('p4$$w0rd'),
                                                                    ]);

                                                                    $user->assignRole('Kepala SPPG');

                                                                    // The returned ID is automatically selected in the dropdown
                                                                    return $user->id;
                                                                }),
                                                        ])->columns(2),
                                                ])

                                                // 2. Define the Immediate Save Logic
                                                ->createOptionUsing(function (array $data) {
                                                    // This runs IMMEDIATELY when the modal is submitted
                                                    $sppg = Sppg::create([
                                                        'kepala_sppg_id' => $data['kepala_sppg_id'],
                                                        'nama_sppg' => $data['nama_sppg'],
                                                        'kode_sppg' => $data['kode_sppg'],
                                                        'lembaga_pengusul_id' => null, // Will be set after Lembaga Pengusul is created
                                                    ]);

                                                    // The returned ID is automatically selected in the dropdown
                                                    return $sppg->id;
                                                }),
                                        ]),
                                ])
                                ->addActionLabel('Tambah SPPG Lagi')
                                ->defaultItems(1)
                                ->columns(2),
                        ]),
                ])->columnSpanFull(),
            ]);
    }
}
