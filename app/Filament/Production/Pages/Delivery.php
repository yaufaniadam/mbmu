<?php

namespace App\Filament\Production\Pages;

use App\Models\Distribution;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Panel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;

class Delivery extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.production.pages.delivery';

    protected static bool $shouldRegisterNavigation = false;

    protected ?string $heading = '';

    public Distribution $record;

    public bool $isEditable = false;

    public function getLayout(): string
    {
        return 'layouts.mobile-navigation';
    }

    public static function routes(Panel $panel): void
    {
        Route::get('/delivery/{distribution}', static::class)
            ->name('delivery');
    }

    // public static function shouldRegisterNavigation(): bool
    // {
    //     // This checks the permission you just generated
    //     return auth()->user()->can('View:Delivery');
    // }

    public function mount(Distribution $distribution): void
    {
        Gate::authorize('View:Delivery');

        $this->record = $distribution;

        $this->isEditable = $distribution->status_pengantaran === 'Menunggu';
    }

    public function save(): void
    {
        Gate::authorize('View:Delivery');

        $user = Auth::user();
        $distribution = $this->record;
        $production = $this->record->productionSchedule;

        // dd($production->getIsFullyDeliveredAttribute());

        if ($production->status === 'Ditolak') {
            Notification::make()
                ->title('Produksi ditolak, makanan batal untuk dikirm.')
                ->success()
                ->send();

            return;
        }

        if ($distribution->user_id != null && $distribution->user_id != $user->id) {
            Notification::make()
                ->title('Makanan sedang dikirim oleh petugas lain.')
                ->success()
                ->send();

            return;
        }

        if ($distribution->status_pengantaran === 'Terkirim') {
            Notification::make()
                ->title('Makanan sudah terkirim.')
                ->success()
                ->send();

            return;
        }

        if ($distribution->status_pengantaran === 'Menunggu') {
            $distribution->update([
                'status_pengantaran' => 'Sedang Dikirim',
                'user_id' => $user->id,
            ]);

            if ($production->status === 'Terverifikasi') {
                $production->update([
                    'status' => 'Didistribusikan',
                ]);
            }

            Notification::make()
                ->title('Anda telah ditugaskan untuk mengantar makanan ini.')
                ->success()
                ->send();

            return;
        }

        if ($distribution->status_pengantaran === 'Sedang Dikirim') {
            $distribution->update([
                'status_pengantaran' => 'Terkirim',
                'delivered_at' => now(),
            ]);

            if ($production->getIsFullyDeliveredAttribute()) {
                $production->update([
                    'status' => 'Selesai',
                ]);
            }

            Notification::make()
                ->title('Anda telah menyelesaikan pengiriman makanan ini.')
                ->success()
                ->send();

            return;
        }
    }
}
