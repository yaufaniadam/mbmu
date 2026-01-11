<?php

namespace App\Filament\Sppg\Widgets;

use App\Services\Sppg\SppgOnboardingService;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class SppgOnboardingStatsWidget extends Widget
{
    protected string $view = 'filament.sppg.widgets.sppg-onboarding-stats-widget';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = -10; // Ensure it's at the top

    public function getViewData(): array
    {
        $user = Auth::user();
        
        /** @var \App\Models\Sppg|null $sppg */
        $sppg = $user->sppg; // Ensure this relationship exists on User model or fetch manually

        if (!$sppg) {
            // Fallback if no SPPG is assigned yet, though usually it should be.
            return [
                'hasSppg' => false,
                'progress' => [],
            ];
        }

        $service = new SppgOnboardingService();
        $progress = $service->getProgress($sppg);

        return [
            'hasSppg' => true,
            'progress' => $progress,
        ];
    }

    public static function canView(): bool
    {
        // Only show if the onboarding is NOT complete? 
        // Or always show it? User requested "To Do List", implies it should be visible until done.
        // Let's keep it visible always for now, but maybe collapsible in future.
        
        $user = Auth::user();
        if (!$user || !$user->sppg) {
            return false;
        }
        
        // Optional: Hide if 100% complete
        // $service = new SppgOnboardingService();
        // $progress = $service->getProgress($user->sppg);
        // return !$progress['is_complete'];

        return true;
    }
}
