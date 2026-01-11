<?php

declare(strict_types=1);

namespace App\Services\Sppg;

use App\Models\Sppg;

class SppgOnboardingService
{
    /**
     * Calculate onboarding progress for a given SPPG.
     *
     * @param Sppg $sppg
     * @return array
     */
    public function getProgress(Sppg $sppg): array
    {
        $steps = [
            'profile' => [
                'title' => 'Lengkapi Profil SPPG',
                'description' => 'Isi data dasar, lokasi, dan informasi rekening.',
                'completed' => $this->checkProfileCompleteness($sppg),
                'route_name' => 'filament.sppg.pages.sppg-profile',
                'icon' => 'heroicon-o-building-office',
            ],
            'staff' => [
                'title' => 'Tambah Staf',
                'description' => 'Daftarkan minimal 1 staf dapur.',
                'completed' => $sppg->staff()->count() > 0,
                'route_name' => 'filament.sppg.resources.staff.index',
                'icon' => 'heroicon-o-users',
            ],
            'volunteers' => [
                'title' => 'Tambah Data Relawan',
                'description' => 'Daftarkan relawan yang membantu operasional.',
                'completed' => $sppg->volunteers()->count() > 0,
                'route_name' => 'filament.sppg.resources.volunteers.index',
                'icon' => 'heroicon-o-heart',
            ],
            'schools' => [
                'title' => 'Tambah Penerima Manfaat',
                'description' => 'Daftarkan sekolah atau panti asuhan penerima manfaat.',
                'completed' => $sppg->schools()->count() > 0,
                'route_name' => 'filament.sppg.resources.schools.index',
                'icon' => 'heroicon-o-academic-cap',
            ],
        ];

        $totalSteps = count($steps);
        $completedSteps = collect($steps)->where('completed', true)->count();
        $percentage = $totalSteps > 0 ? round(($completedSteps / $totalSteps) * 100) : 0;

        return [
            'steps' => $steps,
            'percentage' => $percentage,
            'is_complete' => $percentage === 100,
        ];
    }

    private function checkProfileCompleteness(Sppg $sppg): bool
    {
        // Basic fields check (customize as needed)
        return !empty($sppg->nama_sppg) &&
               !empty($sppg->alamat) &&
               !empty($sppg->province_code) &&
               !empty($sppg->nama_bank) && // Assuming these are critical
               !empty($sppg->nomor_va);
    }
}
