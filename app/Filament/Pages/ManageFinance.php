<?php

namespace App\Filament\Pages;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;

class ManageFinance extends Page implements HasForms
{
    use InteractsWithForms;
    protected string $view = 'filament.pages.manage-finance';
    protected ?string $heading = '';
    protected static ?string $navigationLabel = 'Keuangan';

    public $activeTab = 'pay';

    protected $queryString = [
        'activeTab',
    ];

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-banknotes';
    }

    // public function getFormSchema(): array
    // {
    //     return [
    //         Tabs::make('Pembayaran')
    //             ->schema([])
    //     ];
    // }
}
