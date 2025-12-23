<?php

namespace App\Filament\Sppg\Pages;

use App\Models\Volunteer;
use App\Models\VolunteerDailyAttendance;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use UnitEnum;
use BackedEnum;

class DailyAttendance extends Page implements HasForms
{
    use InteractsWithForms;
    
    protected string $view = 'filament.sppg.pages.daily-attendance';
    
    protected static ?string $navigationLabel = 'Input Presensi (Bulk)';
    
    protected static string|\UnitEnum|null $navigationGroup = 'Keuangan';
    
    protected static ?int $navigationSort = 2;
    
    protected ?string $heading = 'Input Presensi Harian (Bulk)';
    
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
    
    public $selected_date;
    public $search = '';
    public $attendances = [];
    
    public function mount(): void
    {
        $this->selected_date = now()->format('Y-m-d');
        $this->loadAttendances();
    }
    
    public function updatedSearch()
    {
        $this->loadAttendances();
    }
    
    public function loadAttendances()
    {
        $user = Auth::user();
        $sppgId = null;
        
        if ($user->hasRole('Kepala SPPG')) {
            $sppgId = $user->sppgDikepalai?->id;
        } elseif ($user->hasRole('PJ Pelaksana')) {
            $sppgId = $user->unitTugas->first()?->id;
        }
        
        if (!$sppgId) {
            $this->attendances = [];
            return;
        }
        
        $query = Volunteer::where('sppg_id', $sppgId)
            ->orderBy('nama_relawan');
            
        if (!empty($this->search)) {
            $query->where('nama_relawan', 'like', '%' . $this->search . '%');
        }
        
        $volunteers = $query->get();
        
        $this->attendances = $volunteers->map(function ($volunteer) use ($sppgId) {
            $existing = VolunteerDailyAttendance::where('volunteer_id', $volunteer->id)
                ->where('attendance_date', $this->selected_date)
                ->first();
            
            return [
                'volunteer_id' => $volunteer->id,
                'nama_relawan' => $volunteer->nama_relawan,
                'posisi' => $volunteer->posisi,
                'status' => $existing?->status ?? null, // Default to null instead of Alpha
                'notes' => $existing?->notes ?? '',
                'id' => $existing?->id,
                'is_recorded' => (bool) $existing,
            ];
        })->toArray();
    }
    
    public function updateStatus($index, $status)
    {
        // Update local state immediately for UI responsiveness
        $this->attendances[$index]['status'] = $status;
        
        $attendance = $this->attendances[$index];
        $user = Auth::user();
        $sppgId = null;
        
        if ($user->hasRole('Kepala SPPG')) {
            $sppgId = $user->sppgDikepalai?->id;
        } elseif ($user->hasRole('PJ Pelaksana')) {
            $sppgId = $user->unitTugas->first()?->id;
        }
        
        VolunteerDailyAttendance::updateOrCreate(
            [
                'volunteer_id' => $attendance['volunteer_id'],
                'attendance_date' => $this->selected_date,
            ],
            [
                'sppg_id' => $sppgId,
                'status' => $status,
                'notes' => $attendance['notes'] ?? null,
                'recorded_by' => $user->id,
            ]
        );
                // Set flag for UI feedback
            $this->attendances[$index]['is_saved'] = true;
            $this->attendances[$index]['is_recorded'] = true;
            
            // Send success notification
        Notification::make()
            ->success()
            ->title('Status Tersimpan')
            ->body("{$attendance['nama_relawan']} ditandai {$status}")
            ->duration(2000)
            ->send();
    }
    
    // Legacy method - kept for wire:model support if needed, but primary logic is now in updateStatus
    public function updatedAttendances($value, $key)
    {
       // simplified or removed to avoid double saving if wire:click is used
    }
}
