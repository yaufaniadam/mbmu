<?php

namespace App\Filament\Admin\Resources\Holidays\Pages;

use App\Filament\Admin\Resources\Holidays\HolidayResource;
use App\Filament\Imports\HolidayImporter;
use App\Models\Holiday;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListHolidays extends ListRecords
{
    protected static string $resource = HolidayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Smart multi-day action - single day if end date empty/same
            Action::make('addHolidays')
                ->label('Tambah Hari Libur')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->form([
                    TextInput::make('nama')
                        ->label('Nama Hari Libur')
                        ->required()
                        ->placeholder('Contoh: Cuti Bersama Idul Fitri'),
                    DatePicker::make('tanggal_mulai')
                        ->label('Tanggal Mulai')
                        ->required(),
                    DatePicker::make('tanggal_selesai')
                        ->label('Tanggal Selesai (opsional)')
                        ->helperText('Kosongkan jika hanya 1 hari')
                        ->afterOrEqual('tanggal_mulai'),
                ])
                ->action(function (array $data) {
                    $start = Carbon::parse($data['tanggal_mulai']);
                    // If end date empty or null, use start date (single day)
                    $end = !empty($data['tanggal_selesai']) 
                        ? Carbon::parse($data['tanggal_selesai']) 
                        : $start;
                    $period = CarbonPeriod::create($start, $end);
                    
                    $created = 0;
                    $skipped = 0;
                    
                    foreach ($period as $date) {
                        // Skip if already exists
                        if (Holiday::whereDate('tanggal', $date)->exists()) {
                            $skipped++;
                            continue;
                        }
                        
                        Holiday::create([
                            'tanggal' => $date,
                            'nama' => $data['nama'],
                        ]);
                        $created++;
                    }
                    
                    Notification::make()
                        ->title('Hari libur berhasil ditambahkan')
                        ->body("{$created} hari libur dibuat. {$skipped} hari sudah ada sebelumnya.")
                        ->success()
                        ->send();
                }),
            
            // Import CSV action
            ImportAction::make()
                ->importer(HolidayImporter::class)
                ->label('Import CSV'),
        ];
    }
}
