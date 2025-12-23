<?php

namespace App\Filament\Sppg\Resources\VolunteerAttendances\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Models\Volunteer;
use Illuminate\Support\Facades\Auth;

class VolunteerAttendanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Periode Payroll')
                    ->schema([
                        DatePicker::make('period_start')
                            ->label('Tanggal Mulai')
                            ->required()
                            ->default(now()->startOfMonth()),
                        DatePicker::make('period_end')
                            ->label('Tanggal Akhir')
                            ->required()
                            ->default(now()->endOfMonth())
                            ->after('period_start'),
                    ])
                    ->columns(2),
                
                Section::make('Data Relawan')
                    ->schema([
                        \Filament\Forms\Components\Hidden::make('sppg_id')
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
                        
                        Select::make('volunteer_id')
                            ->label('Nama Relawan')
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
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                if ($state) {
                                    $volunteer = Volunteer::find($state);
                                    if ($volunteer) {
                                        $set('daily_rate', $volunteer->daily_rate ?? 0);
                                    }
                                }
                            }),
                        
                        Placeholder::make('posisi_info')
                            ->label('Jabatan')
                            ->content(function ($get) {
                                if ($get('volunteer_id')) {
                                    $volunteer = Volunteer::find($get('volunteer_id'));
                                    return $volunteer?->posisi ?? '-';
                                }
                                return '-';
                            }),
                    ])
                    ->columns(2),
                
                Section::make('Data Kehadiran')
                    ->schema([
                        TextInput::make('days_present')
                            ->label('Jumlah Hari Hadir')
                            ->numeric()
                            ->required()
                            ->default(0)
                            ->minValue(0)
                            ->live()
                            ->suffix('hari'),
                        
                        TextInput::make('late_minutes')
                            ->label('Total Menit Keterlambatan')
                            ->numeric()
                            ->required()
                            ->default(0)
                            ->minValue(0)
                            ->live()
                            ->suffix('menit')
                            ->helperText('Total akumulasi keterlambatan dalam periode'),
                    ])
                    ->columns(2),
                
                Section::make('Perhitungan Gaji')
                    ->schema([
                        TextInput::make('daily_rate')
                            ->label('Upah Harian')
                            ->numeric()
                            ->required()
                            ->prefix('Rp')
                            ->disabled()
                            ->dehydrated()
                            ->helperText('Auto diisi dari data relawan'),
                        
                        TextInput::make('late_deduction_per_hour')
                            ->label('Potongan per Jam Terlambat')
                            ->numeric()
                            ->required()
                            ->default(0)
                            ->prefix('Rp')
                            ->minValue(0)
                            ->live()
                            ->helperText('Opsional, bisa dikosongkan jika tidak ada potongan'),
                        
                        Placeholder::make('gross_calculation')
                            ->label('Gaji Kotor (Estimasi)')
                            ->content(function ($get) {
                                $days = $get('days_present') ?? 0;
                                $rate = $get('daily_rate') ?? 0;
                                $gross = $days * $rate;
                                return 'Rp ' . number_format($gross, 0, ',', '.');
                            }),
                        
                        Placeholder::make('deduction_calculation')
                            ->label('Potongan Terlambat (Estimasi)')
                            ->content(function ($get) {
                                $minutes = $get('late_minutes') ?? 0;
                                $rate = $get('late_deduction_per_hour') ?? 0;
                                $hours = $minutes / 60;
                                $deduction = $hours * $rate;
                                return 'Rp ' . number_format($deduction, 0, ',', '.');
                            }),
                        
                        Placeholder::make('net_calculation')
                            ->label('Gaji Bersih (Estimasi)')
                            ->content(function ($get) {
                                $days = $get('days_present') ?? 0;
                                $rate = $get('daily_rate') ?? 0;
                                $gross = $days * $rate;
                                
                                $minutes = $get('late_minutes') ?? 0;
                                $deductionRate = $get('late_deduction_per_hour') ?? 0;
                                $hours = $minutes / 60;
                                $deduction = $hours * $deductionRate;
                                
                                $net = $gross - $deduction;
                                return '💰 Rp ' . number_format($net, 0, ',', '.');
                            })
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                
                Textarea::make('notes')
                    ->label('Catatan')
                    ->rows(3)
                    ->columnSpanFull()
                    ->helperText('Catatan tambahan, misalnya: izin, sakit, cuti, dll'),
            ]);
    }
}
