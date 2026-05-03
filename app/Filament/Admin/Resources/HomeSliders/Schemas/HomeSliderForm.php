<?php

namespace App\Filament\Admin\Resources\HomeSliders\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class HomeSliderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                FileUpload::make('image_path')->disk('public')
                    ->label('Gambar Slider')
                    ->image()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->disk('public')
                    ->directory('home-sliders')
                    ->maxSize(1024) // 1 MB
                    ->validationAttribute('Gambar Slider')
                    ->validationMessages([
                        'max'      => 'Ukuran gambar terlalu besar. Maksimal 1 MB.',
                        'mimes'    => 'Format tidak didukung. Gunakan JPG, PNG, atau WebP.',
                        'uploaded' => 'Gambar Slider gagal diupload. Pastikan ukuran file di bawah 1 MB.',
                    ])
                    ->helperText('Maks. 1 MB · Format: JPG, PNG, WebP')
                    ->imagePreviewHeight('200')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('link_url')
                    ->url(),
                TextInput::make('order')
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
