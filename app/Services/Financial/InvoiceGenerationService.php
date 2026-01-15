<?php

namespace App\Services\Financial;

use App\Models\Invoice;
use App\Models\ProductionSchedule;
use App\Models\Sppg;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class InvoiceGenerationService
{
    /**
     * Generate invoices for a specific SPPG or all eligible SPPGs.
     * 
     * @param Sppg|null $sppg If null, processes all active SPPGs.
     * @param bool $dryRun If true, only returns what WOULD be generated without DB writes.
     * @return array Result summary ['generated_count' => int, 'details' => array]
     */
    public function generate(?Sppg $targetSppg = null, bool $dryRun = false): array
    {
        $sppgs = $targetSppg ? collect([$targetSppg]) : Sppg::where('is_active', true)->get();
        $generatedCount = 0;
        $details = [];

        foreach ($sppgs as $sppg) {
            $sppgResults = $this->processSppg($sppg, $dryRun);
            if (!empty($sppgResults)) {
                $generatedCount += count($sppgResults);
                $details = array_merge($details, $sppgResults);
            }
        }

        return [
            'generated_count' => $generatedCount,
            'details' => $details,
        ];
    }

    /**
     * Check for potential "stuck" invoices for an SPPG without generating them.
     * Useful for the UI to show "Pending" status.
     */
    public function checkPendingInvoices(Sppg $sppg): array
    {
        return $this->processSppg($sppg, true);
    }

    private function processSppg(Sppg $sppg, bool $dryRun): array
    {
        $results = [];

        // 1. Determine start date of the NEW billing period
        $lastInvoice = Invoice::where('sppg_id', $sppg->id)
            ->where('type', 'SPPG_SEWA')
            ->orderByDesc('end_date')
            ->first();

        // If no previous invoice, start from SPPG start date
        // If SPPG start date is null, default to today
        // If no previous invoice, start from the earliest ProductionSchedule date
        // If no schedules exist, fallback to date now (which will result in no invoices anyway)
        if ($lastInvoice) {
            $startDate = $lastInvoice->end_date->addDay();
        } else {
            $earliestSchedule = ProductionSchedule::where('sppg_id', $sppg->id)
                ->whereIn('status', ['Selesai', 'Didistribusikan', 'Terverifikasi', 'Direncanakan'])
                ->orderBy('tanggal', 'asc')
                ->first();

            $startDate = $earliestSchedule 
                ? $earliestSchedule->tanggal 
                : Carbon::now();
        }

        // 2. Loop to find ALL eligible 10-day periods from startDate until today
        while (true) {
            // Find the 10th production schedule after startDate
            // Note: Use get() then take() or limit() works.
            $schedules = ProductionSchedule::where('sppg_id', $sppg->id)
                ->where('tanggal', '>=', $startDate)
                ->whereIn('status', ['Selesai', 'Didistribusikan', 'Terverifikasi', 'Direncanakan'])
                ->orderBy('tanggal', 'asc')
                ->limit(10)
                ->get();

            // If we don't have enough active days for a full period (10 days), stop.
            if ($schedules->count() < 10) {
                break;
            }

            // End date is the date of the 10th active distribution
            $endDate = $schedules->last()->tanggal;

            // Prepare Invoice Data
            $invoiceData = $this->prepareInvoiceData($sppg, $startDate, $endDate);
            
                if (!$dryRun && $invoiceData['amount'] > 0) {
                     $invoice = Invoice::create([
                        'invoice_number' => 'INV-' . $sppg->kode_sppg . '-' . $endDate->format('ymd'),
                        'sppg_id' => $sppg->id,
                        'type' => 'SPPG_SEWA',
                        'amount' => $invoiceData['amount'],
                        'status' => 'UNPAID',
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'due_date' => (clone $endDate)->addDays(3),
                    ]);

                    // Send WhatsApp Notification to Kepala SPPG
                    $kepalaSppg = $sppg->kepalaSppg;
                    if ($kepalaSppg) {
                        try {
                            $kepalaSppg->notify(new \App\Notifications\InvoiceGenerated($invoice));
                        } catch (\Exception $e) {
                            \Illuminate\Support\Facades\Log::error("Failed to send invoice notification: " . $e->getMessage());
                        }
                    }
                }

                if ($invoiceData['amount'] > 0) {
                    $results[] = array_merge($invoiceData, ['sppg_name' => $sppg->nama_sppg]);
                }

            // Move start date to next day after the end of this period
            $startDate = (clone $endDate)->addDay();
        }

        return $results;
    }

    private function prepareInvoiceData(Sppg $sppg, Carbon $startDate, Carbon $endDate): array
    {
        $activeDays = ProductionSchedule::where('sppg_id', $sppg->id)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->whereIn('status', ['Selesai', 'Didistribusikan', 'Terverifikasi', 'Direncanakan'])
            ->count();

        $ratePerDay = match ($sppg->grade) {
            'A' => 6000000,
            'B' => 4500000,
            'C' => 3000000,
            default => 6000000,
        };

        $amount = $activeDays * $ratePerDay;

        return [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'active_days' => $activeDays,
            'amount' => $amount,
        ];
    }
}
