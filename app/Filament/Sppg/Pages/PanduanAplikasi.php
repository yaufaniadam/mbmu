<?php

namespace App\Filament\Sppg\Pages;

use Filament\Pages\Page;

class PanduanAplikasi extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-book-open';

    protected string $view = 'filament.sppg.pages.panduan-aplikasi';

    protected static string|\UnitEnum|null $navigationGroup = 'Bantuan & Dukungan';

    protected static ?int $navigationSort = 99;

    protected static ?string $title = 'Panduan Aplikasi SPPG';
}
