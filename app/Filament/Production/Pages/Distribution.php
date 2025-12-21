<?php

namespace App\Filament\Production\Pages;

use App\Models\FoodVerification;
use App\Models\ProductionSchedule;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class Distribution extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.production.pages.distribution';

    protected static ?string $navigationLabel = 'Pengantaran';

    protected ?string $heading = '';

    public ?array $data = [];

    public ?ProductionSchedule $record = null;

    protected bool $isEditable = true;

    protected ?FoodVerification $verificationNote = null;

    public function getLayout(): string
    {
        return 'layouts.mobile-navigation';
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-truck';
    }

    public static function shouldRegisterNavigation(): bool
    {
        // This checks the permission you just generated
        return auth()->user()->can('View:Distribution');
    }

    public function mount(): void
    {
        Gate::authorize('View:Distribution');

        $user = Auth::user();
        $organizationId = $user->unitTugas()->first()->id;

        // dd($organizationId);

        $this->record = ProductionSchedule::where('sppg_id', $organizationId)
            ->whereNotIn('status', ['Direncanakan', 'Ditolak', 'Selesai', 'Menunggu ACC Kepala SPPG'])
            ->with('sppg', 'sppg.schools')
            ->latest()
            ->first();

        if (! $this->record) {
            Notification::make()
                ->title('Data tidak ditemukan.')
                ->danger()
                ->send();

            return;
        }

        $this->isEditable = $this->record->status === 'Terverifikasi';

        // load previous verification note if exists
        $this->verificationNote = FoodVerification::where('jadwal_produksi_id', $this->record->id)->latest()->first();
    }

    // public function save(): void
    // {
    //     if (! $this->isEditable || ! $this->record) {
    //         Notification::make()
    //             ->title('Data tidak dapat diedit.')
    //             ->warning()
    //             ->send();
    //         return;
    //     }

    //     // update production schedule status
    //     $this->record->update([
    //         'status' => "Didistribusikan",
    //     ]);

    //     Notification::make()
    //         ->title('Data berhasil diperbarui!')
    //         ->success()
    //         ->send();

    //     // **Important**: refresh model and refill form so disabled states re-evaluate immediately
    //     $this->record->refresh();
    //     $this->isEditable = $this->record->status === 'Terverifikasi';
    // }
}
