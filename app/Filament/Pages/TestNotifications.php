<?php

namespace App\Filament\Pages;

use App\Models\Complaint;
use App\Models\Instruction;
use App\Models\Invoice;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use UnitEnum;
use BackedEnum;
use Filament\Schemas\Schema;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class TestNotifications extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-beaker';
    protected static string|UnitEnum|null $navigationGroup = 'Pengaturan';
    protected static ?string $navigationLabel = 'Test Notifikasi';
    protected static ?string $title = 'Test System Notifications';
    
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
    
    protected string $view = 'filament.pages.test-notifications';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'recipient_id' => auth()->id(),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Configuration')
                    ->schema([
                        Forms\Components\Select::make('recipient_id')
                            ->label('Recipient')
                            ->options(User::limit(50)->pluck('name', 'id'))
                            ->searchable()
                            ->getSearchResultsUsing(fn (string $search) => User::where('name', 'like', "%{$search}%")->limit(50)->pluck('name', 'id'))
                            ->required()
                            ->helperText('Select who will receive the notification'),

                        Forms\Components\Select::make('notification_type')
                            ->label('Notification Type')
                            ->options([
                                'InvoiceGenerated' => 'Invoice Generated (SPPG)',
                                'PaymentPendingVerification' => 'Payment Pending Verification',
                                'ContributionBillGenerated' => 'Contribution Bill Generated',
                                'ContributionPaymentReceived' => 'Contribution Payment Received',
                                'BillDueReminder' => 'Bill Due Reminder',
                                'InstructionPublished' => 'Instruction Published',
                                'ComplaintSubmitted' => 'Complaint Submitted',
                                'ComplaintResponded' => 'Complaint Responded',
                            ])
                            ->required()
                            ->reactive(),
                    ])->columns(2),

                Section::make('Data Context')
                    ->schema([
                        Forms\Components\Select::make('invoice_id')
                            ->label('Select Invoice')
                            ->options(Invoice::latest()->limit(20)->pluck('invoice_number', 'id'))
                            ->searchable()
                            ->getSearchResultsUsing(fn (string $search) => Invoice::where('invoice_number', 'like', "%{$search}%")->latest()->limit(20)->pluck('invoice_number', 'id'))
                            ->visible(fn ($get) => in_array($get('notification_type'), [
                                'InvoiceGenerated', 
                                'PaymentPendingVerification', 
                                'ContributionBillGenerated', 
                                'ContributionPaymentReceived', 
                                'BillDueReminder'
                            ]))
                            ->required(fn ($get) => in_array($get('notification_type'), [
                                'InvoiceGenerated', 
                                'PaymentPendingVerification', 
                                'ContributionBillGenerated', 
                                'ContributionPaymentReceived', 
                                'BillDueReminder'
                            ])),

                        Forms\Components\Radio::make('reminder_type')
                            ->label('Reminder Type')
                            ->options([
                                'reminder' => 'H-3 Warning',
                                'overdue' => 'Overdue Alert',
                            ])
                            ->default('reminder')
                            ->visible(fn ($get) => $get('notification_type') === 'BillDueReminder')
                            ->required(fn ($get) => $get('notification_type') === 'BillDueReminder'),

                        Forms\Components\Select::make('instruction_id')
                            ->label('Select Instruction')
                            ->options(Instruction::latest()->limit(20)->pluck('title', 'id'))
                            ->searchable()
                            ->visible(fn ($get) => $get('notification_type') === 'InstructionPublished')
                            ->required(fn ($get) => $get('notification_type') === 'InstructionPublished'),

                        Forms\Components\Select::make('complaint_id')
                            ->label('Select Complaint')
                            ->options(Complaint::latest()->limit(20)->pluck('subject', 'id'))
                            ->searchable()
                            ->visible(fn ($get) => in_array($get('notification_type'), ['ComplaintSubmitted', 'ComplaintResponded']))
                            ->required(fn ($get) => in_array($get('notification_type'), ['ComplaintSubmitted', 'ComplaintResponded'])),

                        Forms\Components\TextInput::make('response_message')
                            ->label('Response Message (Mock)')
                            ->default('Ini adalah contoh respon admin untuk pengaduan Anda.')
                            ->visible(fn ($get) => $get('notification_type') === 'ComplaintResponded')
                            ->required(fn ($get) => $get('notification_type') === 'ComplaintResponded'),
                    ]),
            ])
            ->statePath('data');
    }

    public function send(): void
    {
        $data = $this->form->getState();
        $recipient = User::find($data['recipient_id']);
        
        if (!$recipient) {
            Notification::make()->title('Recipient not found')->danger()->send();
            return;
        }

        try {
            switch ($data['notification_type']) {
                case 'InvoiceGenerated':
                    $invoice = Invoice::findOrFail($data['invoice_id']);
                    $recipient->notify(new \App\Notifications\InvoiceGenerated($invoice));
                    break;

                case 'PaymentPendingVerification':
                    $invoice = Invoice::findOrFail($data['invoice_id']);
                    $recipient->notify(new \App\Notifications\PaymentPendingVerification($invoice));
                    break;

                case 'ContributionBillGenerated':
                    $invoice = Invoice::findOrFail($data['invoice_id']);
                    $recipient->notify(new \App\Notifications\ContributionBillGenerated($invoice));
                    break;

                case 'ContributionPaymentReceived':
                    $invoice = Invoice::findOrFail($data['invoice_id']);
                    $recipient->notify(new \App\Notifications\ContributionPaymentReceived($invoice));
                    break;

                case 'BillDueReminder':
                    $invoice = Invoice::findOrFail($data['invoice_id']);
                    $recipient->notify(new \App\Notifications\BillDueReminder($invoice, $data['reminder_type']));
                    break;

                case 'InstructionPublished':
                    $instruction = Instruction::findOrFail($data['instruction_id']);
                    $recipient->notify(new \App\Notifications\InstructionPublished($instruction));
                    break;
                
                case 'ComplaintSubmitted':
                    $complaint = Complaint::findOrFail($data['complaint_id']);
                    $recipient->notify(new \App\Notifications\ComplaintSubmitted($complaint));
                    break;

                case 'ComplaintResponded':
                    $complaint = Complaint::findOrFail($data['complaint_id']);
                    $recipient->notify(new \App\Notifications\ComplaintResponded($complaint, $data['response_message']));
                    break;
            }

            Notification::make()
                ->title('Notification Sent')
                ->body("Sent {$data['notification_type']} to {$recipient->name}")
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Failed to send')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
