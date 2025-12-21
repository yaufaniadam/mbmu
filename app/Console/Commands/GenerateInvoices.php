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
            // End date is 9 days after start date (inclusive = 10 days)
            $endDate = (clone $startDate)->addDays(9);

            // If the 10-day period is not yet complete (endDate is in the future), stop.
            // We only bill COMPLETED periods or periods ending TODAY.
            if ($endDate->isFuture()) {
                break;
            }

            // Generate Invoice
            $this->createInvoice($sppg, $startDate, $endDate);
            $count++;

            // Move start date to next day for next iteration (catch-up logic)
            $startDate = $endDate->addDay();
        }
    }

    private function createInvoice($sppg, $startDate, $endDate)
    {
        $amount = 60000000; // Rp 60.000.000 fixed per 10 days

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
        
        $this->info("Created invoice for {$sppg->nama_sppg}: {$startDate->toDateString()} - {$endDate->toDateString()}");
    }
}
