<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoice:generate';

    protected $description = 'Generate operational invoices for SPPGs every 10 days';

    public function handle()
    {
        $sppgs = \App\Models\Sppg::where('is_active', true)->get();
        $count = 0;

        foreach ($sppgs as $sppg) {
            $this->processSppg($sppg, $count);
        }

        $this->info("Generated {$count} new invoices.");
    }

    private function processSppg($sppg, &$count)
    {
        // 1. Determine start date of the NEW billing period
        $lastInvoice = \App\Models\Invoice::where('sppg_id', $sppg->id)
            ->where('type', 'SPPG_SEWA')
            ->orderByDesc('end_date')
            ->first();

        // If no previous invoice, start from SPPG start date
        // If SPPG start date is null, default to today (edge case)
        $startDate = $lastInvoice
            ? $lastInvoice->end_date->addDay()
            : ($sppg->tanggal_mulai_sewa ? \Carbon\Carbon::parse($sppg->tanggal_mulai_sewa) : \Carbon\Carbon::today());

        // 2. Check if enough days have passed to form a 10-day period
        $today = \Carbon\Carbon::today();
        
        // Loop to generate multiple invoices if missed (e.g., cron didn't run for a month, or creating catch-up invoices)
        while (true) {
            // Find the 10th production schedule after startDate
            $schedules = \App\Models\ProductionSchedule::where('sppg_id', $sppg->id)
                ->where('tanggal', '>=', $startDate)
                ->whereIn('status', ['Selesai', 'Didistribusikan', 'Terverifikasi', 'Direncanakan'])
                ->orderBy('tanggal', 'asc')
                ->limit(10)
                ->get();

            // If we don't have enough active days for a full period (10 days), stop and wait.
            if ($schedules->count() < 1) {
                break;
            }

            // End date is the date of the 10th active distribution
            $endDate = $schedules->last()->tanggal;

            // Generate Invoice
            $this->createInvoice($sppg, $startDate, $endDate);
            $count++;

            // Move start date to next day after the end of this period
            $startDate = (clone $endDate)->addDay();
        }
    }

    private function createInvoice($sppg, $startDate, $endDate)
    {
        // Count active distribution days in this period
        // Active days = production_schedules with status 'Selesai' or 'Didistribusikan'
        $activeDays = \App\Models\ProductionSchedule::where('sppg_id', $sppg->id)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->whereIn('status', ['Selesai', 'Didistribusikan', 'Terverifikasi', 'Direncanakan'])
            ->count();

        // Calculate amount based on SPPG Grade
        $ratePerDay = match ($sppg->grade) {
            'A' => 6000000,
            'B' => 4500000,
            'C' => 3000000,
            default => 3000000, // Default for 'C' or null
        };
        $amount = $activeDays * $ratePerDay;

        // Skip if no active days (no charge)
        if ($activeDays === 0) {
            $this->warn("Skipped invoice for {$sppg->nama_sppg}: No active distribution days in period.");
            return;
        }

        \App\Models\Invoice::create([
            'invoice_number' => 'INV-' . $sppg->kode_sppg . '-' . $endDate->format('ymd'),
            'sppg_id' => $sppg->id,
            'type' => 'SPPG_SEWA',
            'amount' => $amount,
            'status' => 'UNPAID',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'due_date' => (clone $endDate)->addDays(3), // Due in 3 days
        ]);
        
        $this->info("Created invoice for {$sppg->nama_sppg}: {$startDate->toDateString()} - {$endDate->toDateString()} ({$activeDays} active days = Rp " . number_format($amount, 0, ',', '.') . ")");
    }
}
