<?php

namespace App\Filament\Admin\Resources\ProductionSchedules\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProductionScheduleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('tanggal')
                    ->label('Tanggal')
                    ->required(),
                Textarea::make('menu_hari_ini')
                    ->label('Menu Hari Ini')
                    ->required(),
            ]);
    }
}
