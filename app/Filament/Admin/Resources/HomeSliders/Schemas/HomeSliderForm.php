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
                FileUpload::make('image_path')
                    ->label('Gambar Slider')
                    ->image()
                    ->directory('home-sliders')
                    ->maxSize(10240)
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
