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
        $service = new \App\Services\Financial\InvoiceGenerationService();
        
        $this->info("Starting invoice generation...");
        
        $result = $service->generate();
        
        foreach ($result['details'] as $detail) {
            $this->info("Created invoice for {$detail['sppg_name']}: " . 
                $detail['start_date']->toDateString() . " - " . $detail['end_date']->toDateString() . 
                " (Rp " . number_format($detail['amount'], 0, ',', '.') . ")");
        }

        $this->info("Completed. Generated {$result['generated_count']} new invoices.");
    }
}
