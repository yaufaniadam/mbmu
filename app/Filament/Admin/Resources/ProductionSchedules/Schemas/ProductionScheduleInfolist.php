<?php

namespace App\Filament\Admin\Resources\ProductionSchedules\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ProductionScheduleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tanggal')
                    ->label('Tanggal')
                    ->date(),
                TextEntry::make('menu_hari_ini')
                    ->label('Menu Hari Ini'),
                // TextEntry::make('jumlah')
                //     ->label('Jumlah')
                //     ->numeric(),
            ])
            ->columns(1);
    }
}
