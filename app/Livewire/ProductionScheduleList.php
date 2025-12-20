<?php

namespace App\Livewire;

use App\Models\ProductionSchedule;
use App\Models\User;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ProductionScheduleList extends TableWidget
{
    use InteractsWithPageFilters; // <-- 2. ADD THIS TRAIT

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Jadwal Produksi'; // Optional: Add a heading

    protected static bool $isLazy = false;

    public function table(Table $table): Table
    {
        return $table
            // <-- 3. MODIFY THE QUERY TO USE FILTERS
            ->query(function (): Builder {
                /** @var \App\Models\User $user */
                $user = Auth::user();

                $sppgId = $this->pageFilters['sppg_id'] ?? null;

                if ($user->hasRole('Kepala SPPG')) {
                    $sppgId = User::find($user->id)->sppgDikepalai?->id;
                } elseif ($user->hasAnyRole(['PJ Pelaksana', 'Ahli Gizi', 'Staf Administrator SPPG', 'Staf Akuntan', 'Staf Gizi', 'Staf Pengantaran'])) {
                    $sppgId = User::find($user->id)->unitTugas->first()?->id;
                }

                return ProductionSchedule::query()
                    ->when(
                        $sppgId,
                        fn ($query) => $query->where('sppg_id', $sppgId)
                    )
                    ->latest('tanggal'); // Show newest first
            })
            ->columns([
                TextColumn::make('tanggal')
                    ->label('Menu Tanggal')
                    // <-- 4. ADD DATE FORMATTING
                    ->date('l, d F Y'), // e.g., "Jumat, 22 Mei 2025"
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'primary',
                        'success' => 'Selesai',
                        'warning' => 'Proses',
                    ]),
            ])
            // ... (rest of your table code)
            ->filters([
                //
            ]);
    }
}
