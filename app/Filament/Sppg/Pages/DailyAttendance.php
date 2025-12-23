<?php

namespace App\Filament\Sppg\Pages;

use App\Filament\Sppg\Widgets\DailyAttendanceStats;
use App\Models\Volunteer;
use App\Models\VolunteerDailyAttendance;
use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class DailyAttendance extends Page implements HasTable
{
    use InteractsWithTable;
    
    protected static ?string $navigationLabel = 'Input Presensi (Bulk)';
    
    protected static string|\UnitEnum|null $navigationGroup = 'Keuangan';
    
    protected static ?int $navigationSort = 2;
    
    protected ?string $heading = 'Input Presensi Harian (Bulk)';
    
    protected string $view = 'filament.sppg.pages.daily-attendance';

    public ?string $selected_date = null;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-calendar-days';
    }
    
    public static function canAccess(): bool
    {
        return Auth::user()->hasAnyRole([
            'Kepala SPPG',
            'PJ Pelaksana',
        ]);
    }
    
    public function mount(): void
    {
        $this->selected_date = now()->format('Y-m-d');
    }

    protected function getHeaderWidgets(): array
    {
        return [
            DailyAttendanceStats::class,
        ];
    }

    protected function getHeaderWidgetsColumns(): int|array
    {
        return 4;
    }

    public function getHeaderWidgetsData(): array
    {
        return [
            'selectedDate' => $this->selected_date,
        ];
    }
    
    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                $user = Auth::user();
                $sppgId = null;

                if ($user->hasRole('Kepala SPPG')) {
                    $sppgId = $user->sppgDikepalai?->id;
                } elseif ($user->hasRole('PJ Pelaksana')) {
                    $sppgId = $user->unitTugas->first()?->id;
                }

                return Volunteer::query()
                    ->where('sppg_id', $sppgId)
                    ->with(['dailyAttendances' => function ($query) {
                        $query->where('attendance_date', $this->selected_date);
                    }])
                    ->orderBy('nama_relawan');
            })
            ->columns([
                TextColumn::make('index')
                    ->label('#')
                    ->rowIndex(),
                TextColumn::make('nama_relawan')
                    ->label('Nama Relawan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('posisi')
                    ->label('Posisi')
                    ->badge()
                    ->color('info'),
                ViewColumn::make('status_attendance')
                    ->label('Status Kehadiran')
                    ->view('filament.tables.columns.attendance-status-radio')
                    ->alignCenter(),
            ])
            ->paginated(false);
    }
    
    public function updateStatus($volunteerId, $status)
    {
        $user = Auth::user();
        $sppgId = null;
        
        if ($user->hasRole('Kepala SPPG')) {
            $sppgId = $user->sppgDikepalai?->id;
        } elseif ($user->hasRole('PJ Pelaksana')) {
            $sppgId = $user->unitTugas->first()?->id;
        }
        
        $volunteer = Volunteer::find($volunteerId);

        if (!$volunteer) {
            return;
        }

        VolunteerDailyAttendance::updateOrCreate(
            [
                'volunteer_id' => $volunteerId,
                'attendance_date' => $this->selected_date,
            ],
            [
                'sppg_id' => $sppgId,
                'status' => $status,
                'recorded_by' => $user->id,
            ]
        );

        Notification::make()
            ->success()
            ->title('Status Tersimpan')
            ->body("{$volunteer->nama_relawan} ditandai {$status}")
            ->duration(1000)
            ->send();
    }
    
    public function updatedSelectedDate()
    {
        // Refresh table when date changes
    }
}
