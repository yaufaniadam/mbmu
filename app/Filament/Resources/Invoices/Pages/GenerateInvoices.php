<?php

namespace App\Filament\Resources\Invoices\Pages;

use App\Filament\Resources\Invoices\InvoiceResource;
use App\Models\Sppg;
use App\Services\Financial\InvoiceGenerationService;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Support\Enums\Size;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class GenerateInvoices extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static string $resource = InvoiceResource::class;

    protected string $view = 'filament.resources.invoices.pages.generate-invoices';
    
    protected static ?string $title = 'Manual Generate Invoice';

    public $pendingInvoices = [];

    public function mount()
    {
        $this->refreshPendingList();
    }

    public function refreshPendingList()
    {
        $service = new InvoiceGenerationService();
        $this->pendingInvoices = []; // Reset

        $sppgs = Sppg::where('is_active', true)->get();

        foreach ($sppgs as $sppg) {
            // Check only (true = dryRun)
            $pending = $service->checkPendingInvoices($sppg);
            
            if (!empty($pending)) {
                foreach ($pending as $item) {
                     // Add sppg ID for reference
                     $item['sppg_id'] = $sppg->id;
                     $item['sppg_name'] = $sppg->nama_sppg;
                     // Ensure we have a unique key for the table rows if possible, or just use index
                     $item['id'] = $sppg->id; // Use SPPG ID as the row key for now since one invoice per SPPG usually
                     $this->pendingInvoices[] = $item;
                }
            }
        }
    }

    public function getCachedPendingInvoices()
    {
        return $this->pendingInvoices;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                // We need a query to start with, but we are showing custom rows.
                // Filament Tables usually work with Eloquent Queries.
                // However, we can use `invoked()` or pass a collection if we use `rows()`. 
                // Wait, standard Filament tables are Eloquent based. 
                // If I want to show an array data, I should arguably use a different approach or trick it.
                // 
                // Trick: Query SPPGs but filter them in PHP? No, pagination breaks.
                // 
                // Better approach for Array Data:
                // Use `Sppg::query()->whereIn('id', collect($this->pendingInvoices)->pluck('sppg_id'))`
                // And then use `formatStateUsing` or `getStateUsing` to pull the calculated invoice data from `$this->pendingInvoices` array using the record ID.
                Sppg::query()->whereIn('id', collect($this->pendingInvoices)->pluck('sppg_id'))
            )
            ->columns([
                Tables\Columns\TextColumn::make('nama_sppg')
                    ->label('SPPG Name')
                    ->description(fn (Sppg $record) => 'ID: ' . $record->id)
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('billing_period')
                    ->label('Billing Period')
                    ->state(function (Sppg $record) {
                        $data = $this->getInvoiceDataFor($record->id);
                        if (!$data) return '-';
                        return Carbon::parse($data['start_date'])->isoFormat('D MMM Y') . ' to ' . Carbon::parse($data['end_date'])->isoFormat('D MMM Y');
                    }),

                Tables\Columns\TextColumn::make('active_days')
                    ->label('Active Days')
                    ->state(function (Sppg $record) {
                        $data = $this->getInvoiceDataFor($record->id);
                        return $data ? $data['active_days'] . ' Days' : '-';
                    })
                    ->badge()
                    ->color('info')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->state(function (Sppg $record) {
                        $data = $this->getInvoiceDataFor($record->id);
                        return $data ? $data['amount'] : 0;
                    })
                    ->money('IDR')
                    ->alignRight(),
            ])
            ->actions([
                Action::make('generate')
                    ->label('Generate')
                    ->color('gray')
                    ->size(Size::ExtraSmall)
                    ->button()
                    ->action(fn (Sppg $record) => $this->generateSingle($record->id)),
            ])
            ->bulkActions([
                // Not strictly needed as we have "Generate All" header action
            ]);
    }

    protected function getInvoiceDataFor($sppgId)
    {
        return collect($this->pendingInvoices)->firstWhere('sppg_id', $sppgId);
    }

    public function getHeaderActions(): array
    {
        return [
            Action::make('generateAll')
                ->label('Generate ALL Invoices')
                ->icon('heroicon-o-bolt')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('Generate All Pending Invoices')
                ->modalDescription('Are you sure you want to generate ALL pending invoices? This will create database records for all items in the list.')
                ->action('generateAll')
                ->hidden(fn () => empty($this->pendingInvoices)),
        ];
    }

    public function getStats(): array
    {
        $count = count($this->pendingInvoices);
        $amount = collect($this->pendingInvoices)->sum('amount');

        return [
            Stat::make('Total Pending Invoices', $count)
                ->icon('heroicon-o-document-text')
                ->color('primary'),
            Stat::make('Total Potential Revenue', 'Rp ' . number_format($amount, 0, ',', '.'))
                ->icon('heroicon-o-currency-dollar')
                ->color('success'),
        ];
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
