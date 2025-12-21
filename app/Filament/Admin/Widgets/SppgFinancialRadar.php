<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Sppg;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class SppgFinancialRadar extends BaseWidget
{
    protected static ?string $heading = 'Radar Kesehatan Finansial SPPG';

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        return $user->hasAnyRole(['Superadmin', 'Staf Kornas', 'Direktur Kornas']);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Sppg::query()
            )
            ->columns([
                TextColumn::make('nama_sppg')
                    ->label('Nama Unit SPPG')
                    ->searchable(),
                TextColumn::make('balance')
                    ->label('Saldo Saat Ini')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('financial_status')
                    ->label('Status')
                    ->badge()
                    ->getStateUsing(function (Sppg $record) {
                        if ($record->balance < 5000000) return 'Kritis';
                        if ($record->balance < 15000000) return 'Siaga';
                        return 'Aman';
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'Kritis' => 'danger',
                        'Siaga' => 'warning',
                        'Aman' => 'success',
                    }),
                TextColumn::make('latest_expense')
                    ->label('Pengeluaran Terakhir')
                    ->getStateUsing(fn (Sppg $record) => $record->operatingExpenses()->latest()->first()?->amount ?? 0)
                    ->money('IDR'),
            ]);
    }
}
