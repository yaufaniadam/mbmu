<?php

namespace App\Livewire;

use App\Models\Distribution;
use App\Models\User;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ProductionDistributionList extends TableWidget
{
    use InteractsWithPageFilters; // <-- 2. ADD THIS TRAIT

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Distribusi'; // Optional: Add a heading

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

                return Distribution::query()
                    // Eager load relationships to prevent N+1 query problems
                    ->with(['productionSchedule', 'school', 'courier'])
                    // Show only distributions for schedules that are verified or further
                    ->whereHas('productionSchedule', function ($query) use ($sppgId) {
                        $query->whereIn('status', ['Terverifikasi', 'Didistribusikan', 'Selesai']);
                        if ($sppgId) {
                            $query->where('sppg_id', $sppgId);
                        }
                    })
                    ->latest('created_at'); // Show newest first
            })
            ->columns([
                TextColumn::make('productionSchedule.tanggal')
                    ->label('Tanggal Distribusi')
                    // <-- 4. ADD DATE FORMATTING
                    ->date('l, d F Y'), // e.g., "Jumat, 22 Mei 2025"
                TextColumn::make('school.nama_sekolah')
                    ->label('Penerima MBM'),
                TextColumn::make('status_pengantaran')
                    ->label('Status Distribusi')
                    ->badge()
                    ->colors([
                        'success' => 'Terkirim',
                        'warning' => 'Proses',
                        'danger' => 'Gagal',
                    ]),
                TextColumn::make('courier.name')
                    ->label('Petugas Pengantar'),
            ])
            ->actions([
                \Filament\Actions\Action::make('deliver')
                    ->label('Antarkan')
                    ->icon('heroicon-m-truck')
                    ->color('primary')
                    ->url(fn ($record) => "/production/delivery/{$record->id}")
                    ->openUrlInNewTab(false)
                    ->visible(fn () => Auth::user()->hasAnyRole(['Staf Pengantaran', 'Superadmin'])),
            ])
            ->filters([
                //
            ]);
    }
}
