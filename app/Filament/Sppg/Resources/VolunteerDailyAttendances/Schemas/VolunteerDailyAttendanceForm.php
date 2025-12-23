<?php

namespace App\Filament\Sppg\Resources\VolunteerDailyAttendances\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class VolunteerDailyAttendanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('sppg_id')
                    ->default(function () {
                        $user = Auth::user();
                        if ($user->hasRole('Kepala SPPG')) {
                            return $user->sppgDikepalai?->id;
                        } elseif ($user->hasRole('PJ Pelaksana')) {
                            return $user->unitTugas->first()?->id;
                        }
                        return null;
                    })
                    ->dehydrated(),
                
                Hidden::make('recorded_by')
                    ->default(fn () => Auth::id())
                    ->dehydrated(),
                
                Select::make('volunteer_id')
                    ->label('Relawan')
                    ->relationship('volunteer', 'nama_relawan', function ($query) {
                        $user = Auth::user();
                        $sppgId = null;
                        if ($user->hasRole('Kepala SPPG')) {
                            $sppgId = $user->sppgDikepalai?->id;
                        } elseif ($user->hasRole('PJ Pelaksana')) {
                            $sppgId = $user->unitTugas->first()?->id;
                        }
                        if ($sppgId) {
                            return $query->where('sppg_id', $sppgId);
                        }
                        return $query;
                    })
                    ->searchable()
                    ->preload()
                    ->required(),
                
                DatePicker::make('attendance_date')
                    ->label('Tanggal Presensi')
                    ->default(now())
                    ->required(),
                
                Select::make('status')
                    ->label('Status Kehadiran')
                    ->options([
                        'Hadir' => '✓ Hadir',
                        'Izin' => '📝 Izin',
                        'Sakit' => '🏥 Sakit',
                        'Alpha' => '✗ Alpha',
                    ])
                    ->default('Hadir')
                    ->required(),
                
                Textarea::make('notes')
                    ->label('Catatan')
                    ->placeholder('Catatan tambahan (opsional)')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
