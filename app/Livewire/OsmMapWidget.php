<?php

namespace App\Livewire;

use App\Models\Sppg;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;

class OsmMapWidget extends Widget
{
    use InteractsWithPageFilters;

    protected string $view = 'livewire.osm-map-widget';

    protected int|string|array $columnSpan = 'full';

    // Example data to display on the map
    public array $markers = [];

    public function mount(): void
    {
        $this->updateMapData();
    }

    public static function canView(): bool
    {
        // 1. Ensure user is logged in
        if (!Auth::check()) {
            return false;
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 2. Check for specific roles
        // This assumes you are using Spatie Permissions or Filament Shield
        return $user->hasAnyRole(['Superadmin', 'Staf Kornas', 'Direktur Kornas']);
    }

    #[On('refresh-map-widget')]
    public function updateMapData(): void
    {
        // 1. Get Filter Values from Dashboard
        $provinceCode = $this->filters['province_code'] ?? null;

        // 2. Base Query
        $query = Sppg::query()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude');

        // 3. Apply Filters if they exist
        if ($provinceCode) {
            $query->where('province_code', $provinceCode);
        }

        // 4. Fetch Data & Format
        $this->markers = $query->get()->map(function ($sppg) {
            $info = [
                'alamat' => $sppg->alamat,
                'kepala_sppg' => $sppg->kepalaSppg->name ?? 'N/A',
            ];
            return [
                'lat'   => (float) $sppg->latitude,
                'lng'   => (float) $sppg->longitude,
                'title' => $sppg->nama_sppg,
                'info'  => $info ?? 'N/A',
                'id'    => $sppg->id,
                // 'type' is no longer needed since everything is now an SPPG marker
            ];
        })->toArray();

        // 5. Push to Frontend
        $this->dispatch('update-map-markers', markers: $this->markers);
    }

    public function handleMapClick($lat, $lng): void
    {
        // You could save this to the DB or filter a table
        $this->js(<<<JS
            new FilamentNotification()
                .title('Location Selected')
                .body('Lat: $lat, Lng: $lng')
                .success()
                .send();
        JS);
    }
}
