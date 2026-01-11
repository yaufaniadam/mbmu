<?php

namespace App\Filament\Resources\Invoices\Pages;

use App\Filament\Resources\Invoices\InvoiceResource;
use App\Models\Sppg;
use App\Services\Financial\InvoiceGenerationService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;

class GenerateInvoices extends Page
{
    protected static string $resource = InvoiceResource::class;

    protected string $view = 'filament.resources.invoices.pages.generate-invoices';
    
    protected static ?string $title = 'Manual Generate Tool';

    public $pendingInvoices = [];

    public function mount()
    {
        $this->refreshPendingList();
    }

    public function refreshPendingList()
    {
        $service = new InvoiceGenerationService();
        $this->pendingInvoices = [];

        $sppgs = Sppg::where('is_active', true)->get();

        foreach ($sppgs as $sppg) {
            // Check only (true = dryRun)
            $pending = $service->checkPendingInvoices($sppg);
            
            if (!empty($pending)) {
                foreach ($pending as $item) {
                     // Add sppg ID for reference
                     $item['sppg_id'] = $sppg->id;
                     $item['sppg_name'] = $sppg->nama_sppg;
                     $this->pendingInvoices[] = $item;
                }
            }
        }
    }

    public function generateAll()
    {
        $service = new InvoiceGenerationService();
        $result = $service->generate(); // Run for real

        Notification::make()
            ->title('Generate Success')
            ->body("Successfully generated {$result['generated_count']} invoices.")
            ->success()
            ->send();

        $this->refreshPendingList();
    }

    public function generateSingle($sppgId)
    {
        $sppg = Sppg::find($sppgId);
        if (!$sppg) return;

        $service = new InvoiceGenerationService();
        $result = $service->generate($sppg);

        Notification::make()
            ->title('Invoice Generated')
            ->body("Generated {$result['generated_count']} invoices for {$sppg->nama_sppg}.")
            ->success()
            ->send();

        $this->refreshPendingList();
    }
}
