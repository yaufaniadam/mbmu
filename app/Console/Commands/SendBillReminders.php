<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Models\User;
use App\Notifications\BillDueReminder;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendBillReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-bill-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminders for invoices approaching due date or overdue';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting bill reminder check...');

        $today = Carbon::today();
        
        // 1. Reminder: 3 days before due date (H-3)
        $upcomingInvoices = Invoice::where('status', 'UNPAID')
            ->whereDate('due_date', $today->copy()->addDays(3))
            ->with(['sppg', 'sppg.kepalaSppg', 'sppg.lembagaPengusul.pimpinan'])
            ->get();

        $this->info("Found {$upcomingInvoices->count()} upcoming invoices (H-3).");

        foreach ($upcomingInvoices as $invoice) {
            $this->sendNotification($invoice, 'reminder');
        }

        // 2. Overdue: 1 day after due date
        // Note: You can add more checks like 3 days after, 7 days, etc.
        $overdueInvoices = Invoice::where('status', 'UNPAID')
            ->whereDate('due_date', $today->copy()->subDay(1))
            ->with(['sppg', 'sppg.kepalaSppg', 'sppg.lembagaPengusul.pimpinan'])
            ->get();
            
        $this->info("Found {$overdueInvoices->count()} newly overdue invoices.");

        foreach ($overdueInvoices as $invoice) {
            $this->sendNotification($invoice, 'overdue');
        }
        
        $this->info('Bill reminder check completed.');
    }

    private function sendNotification(Invoice $invoice, string $type)
    {
        $recipient = null;

        // Determine Recipient based on Invoice Type
        if ($invoice->type === 'SPPG_SEWA') {
            // Recipient: Kepala SPPG
            $recipient = $invoice->sppg->kepalaSppg;
        } elseif ($invoice->type === 'LP_ROYALTY') {
            // Recipient: Pimpinan Lembaga Pengusul
            $recipient = $invoice->sppg->lembagaPengusul?->pimpinan;
        }

        if ($recipient) {
            try {
                $recipient->notify(new BillDueReminder($invoice, $type));
                $this->info("Sent {$type} to {$recipient->name} for Invoice {$invoice->invoice_number}");
            } catch (\Exception $e) {
                Log::error("Failed to send bill reminder: " . $e->getMessage());
                $this->error("Failed to send to {$recipient->name}");
            }
        } else {
            $this->warn("No recipient found for Invoice {$invoice->invoice_number}");
        }
    }
}
