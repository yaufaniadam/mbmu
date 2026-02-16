<?php

namespace App\Filament\Lembaga\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Facades\Auth;
use BackedEnum;

class Dashboard extends BaseDashboard
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-home';

    public function getWidgets(): array
    {
        return [
            \App\Filament\Lembaga\Widgets\LembagaStatsOverview::class,
        ];
    }

    public function getColumns(): int | array
    {
        return 2;
    }

    public function getData(): array
    {
        $user = Auth::user();
        $lembaga = $user->lembagaDipimpin;

        if (!$lembaga) {
            return [
                'total_sppg' => 0,
                'pending_verifications' => 0,
                'unpaid_invoices' => 0,
                'recent_complaints' => collect([]),
            ];
        }

        // Count SPPG dibawahi
        $totalSppg = $lembaga->sppgs()->count();

        // Count pending verifications (Insentif SPPG yang menunggu verifikasi)
        $pendingVerifications = \App\Models\Invoice::query()
            ->whereIn('sppg_id', $lembaga->sppgs->pluck('id'))
            ->where('type', 'SPPG_SEWA')
            ->where('status', 'WAITING_VERIFICATION')
            ->count();

        // Count unpaid royalty invoices
        $unpaidInvoices = \App\Models\Invoice::query()
            ->whereIn('sppg_id', $lembaga->sppgs->pluck('id'))
            ->where('type', 'LP_ROYALTY')
            ->where('status', 'UNPAID')
            ->count();

        // Recent complaints
        $recentComplaints = \App\Models\Complaint::query()
            ->where('user_id', $user->id)
            ->where('source_type', 'lembaga_pengusul')
            ->latest()
            ->take(5)
            ->get();

        return [
            'total_sppg' => $totalSppg,
            'pending_verifications' => $pendingVerifications,
            'unpaid_invoices' => $unpaidInvoices,
            'recent_complaints' => $recentComplaints,
            'lembaga' => $lembaga,
        ];
    }
}
