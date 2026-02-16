<?php

namespace App\Filament\Admin\Resources\Instructions\Schemas;

use App\Models\LembagaPengusul;
use App\Models\Sppg;
use App\Models\User;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Role;

class InstructionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Informasi Instruksi')
                    ->schema([
                        TextInput::make('title')
                            ->label('Judul')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        
                        RichEditor::make('content')
                            ->label('Isi Instruksi')
                            ->required()
                            ->columnSpanFull(),

                        \Filament\Forms\Components\FileUpload::make('attachment_path')
                            ->label('Lampiran (Gambar/Dokumen)')
                            ->helperText('Upload gambar atau dokumen jika diperlukan (Maks. 2MB)')
                            ->disk('local')
                            ->directory('instruction-attachments')
                            ->maxSize(2048)
                            ->acceptedFileTypes(['image/*', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                            ->columnSpanFull(),
                        
                        Select::make('recipient_type')
                            ->label('Penerima')
                            ->required()
                            ->options([
                                'all' => 'Semua Pengguna',
                                'role' => 'Berdasarkan Jabatan',
                                'sppg' => 'Berdasarkan SPPG',
                                'lembaga_pengusul' => 'Berdasarkan Lembaga Pengusul',
                                'user' => 'Pengguna Tertentu',
                            ])
                            ->live()
                            ->afterStateUpdated(fn (callable $set) => $set('recipient_ids', null)),
                        
                        Select::make('recipient_ids')
                            ->label('Pilih Penerima')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->options(function (callable $get) {
                                $type = $get('recipient_type');
                                return match($type) {
                                    'role' => Role::pluck('name', 'id')->toArray(),
                                    'sppg' => Sppg::pluck('nama_sppg', 'id')->toArray(),
                                    'lembaga_pengusul' => LembagaPengusul::pluck('nama_lembaga', 'id')->toArray(),
                                    'user' => User::query()
                                        ->get()
                                        ->mapWithKeys(fn($user) => [$user->id => $user->name . ' (' . $user->email . ')'])
                                        ->toArray(),
                                    default => [],
                                };
                            })
                            ->hidden(fn (callable $get) => $get('recipient_type') === 'all')
                            ->required(fn (callable $get) => $get('recipient_type') !== 'all'),
                        
                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true)
                            ->helperText('Instruksi yang tidak aktif tidak akan ditampilkan kepada penerima'),
                    ])
                    ->columns(2),
            ]);
    }
}
