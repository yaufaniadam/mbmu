<?php

namespace App\Filament\Resources\ProductionSchedules\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ProductionScheduleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components(array_merge(
                [
                    DatePicker::make('tanggal')
                        ->label('Tanggal')
                        ->required(),
                    Textarea::make('menu_hari_ini')
                        ->label('Menu Hari Ini')
                        ->required(),
                ],
                static::getSchoolFields()
            ));
    }

    protected static function getSchoolFields(): array
    {
        /** @var \App\Models\User $user */
        $user = \Illuminate\Support\Facades\Auth::user();
        $sppg = null;

        if ($user->hasRole('Kepala SPPG')) {
            $sppg = $user->sppgDikepalai;
        } elseif ($user->hasRole('PJ Pelaksana')) {
            $sppg = $user->unitTugas->first();
        }

        $schools = $sppg ? $sppg->schools : collect();

        if ($schools->isEmpty()) {
            return [];
        }

        $schoolFieldsets = $schools->map(function ($school) {
            $latest = $school->distributions()
                ->orderByDesc('id')
                ->first();

            return \Filament\Schemas\Components\Fieldset::make($school->nama_sekolah)
                ->schema([
                    \Filament\Forms\Components\Hidden::make('porsi_per_sekolah.' . $school->id . '.sekolah_id')
                        ->default($school->id),
                    \Filament\Forms\Components\TextInput::make('porsi_per_sekolah.' . $school->id . '.jumlah_porsi_besar')
                        ->label('Jumlah Porsi Besar')
                        ->numeric()
                        ->default($latest?->jumlah_porsi_besar ?? '')
                        ->required(),
                    \Filament\Forms\Components\TextInput::make('porsi_per_sekolah.' . $school->id . '.jumlah_porsi_kecil')
                        ->label('Jumlah Porsi Kecil')
                        ->numeric()
                        ->default($latest?->jumlah_porsi_kecil ?? '')
                        ->required(),
                ])
                ->columns(2);
        })->all();

        return [
            \Filament\Schemas\Components\Fieldset::make('Jumlah Porsi Per Penerima MBM')
                ->schema($schoolFieldsets)
                ->columns(1),
        ];
    }
}
