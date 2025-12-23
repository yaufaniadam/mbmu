<?php

namespace App\Filament\Admin\Resources\Holidays\Pages;

use App\Filament\Admin\Resources\Holidays\HolidayResource;
use Filament\Resources\Pages\Page;

class HolidayCalendar extends Page
{
    protected static string $resource = HolidayResource::class;

    protected string $view = 'filament.admin.resources.holidays.pages.holiday-calendar';

    protected static ?string $title = 'Kalender Hari Libur';


    public function getHolidays(): array
    {
        return \App\Models\Holiday::all()->map(function ($holiday) {
            return [
                'id' => $holiday->id,
                'title' => $holiday->nama,
                'start' => $holiday->tanggal->format('Y-m-d'),
                'allDay' => true,
                'backgroundColor' => '#dc2626',
                'borderColor' => '#dc2626',
            ];
        })->toArray();
    }

    public function addHoliday(string $date, string $nama): void
    {
        \App\Models\Holiday::create([
            'tanggal' => $date,
            'nama' => $nama,
        ]);

        $this->dispatch('holiday-added');
    }

    public function deleteHoliday(int $id): void
    {
        \App\Models\Holiday::find($id)?->delete();
        $this->dispatch('holiday-deleted');
    }
}
