<?php

namespace App\Filament\Resources\Invoices;

use App\Filament\Resources\Invoices\Pages;
use App\Models\Invoice;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Forms\Components\Textarea;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\SppgIncomingFund;
use BackedEnum;
use UnitEnum;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';
    protected static string|UnitEnum|null $navigationGroup = 'Keuangan';
    protected static ?string $pluralModelLabel = 'Tagihan';
    protected static ?string $modelLabel = 'Tagihan';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('invoice_number')
                            ->label('Nomor Invoice')
                            ->default(fn () => 'INV-' . strtoupper(uniqid()))
                            ->required()
                            ->maxLength(255)
                            ->disabled(fn (string $operation) => $operation === 'edit'),
                        Forms\Components\Select::make('sppg_id')
                            ->relationship('sppg', 'nama_sppg')
                            ->label('SPPG')
                            ->required()
                            ->disabled(fn (string $operation) => $operation === 'edit'),
                        Forms\Components\Select::make('type')
                            ->options([
                                'SPPG_SEWA' => 'Insentif SPPG',
                                'LP_ROYALTY' => 'Kontribusi Kornas',
                            ])
                            ->label('Tipe Tagihan')
                            ->required()
                            ->disabled(fn (string $operation) => $operation === 'edit'),
                        Forms\Components\TextInput::make('amount')
                            ->label('Jumlah')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled(fn (string $operation) => $operation === 'edit'),
                        Forms\Components\Select::make('status')
                            ->options([
                                'UNPAID' => 'Belum Bayar',
                                'WAITING_VERIFICATION' => 'Menunggu Verifikasi',
                                'PAID' => 'Lunas',
                                'REJECTED' => 'Ditolak',
                            ])
                            ->default('UNPAID')
                            ->required(),
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Periode Mulai')
                            ->required()
                            ->disabled(fn (string $operation) => $operation === 'edit'),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('Periode Selesai')
                            ->required()
                            ->disabled(fn (string $operation) => $operation === 'edit'),
                        Forms\Components\DatePicker::make('due_date')
                            ->label('Jatuh Tempo')
                            ->required(),
                        Forms\Components\FileUpload::make('proof_of_payment')->disk('public')
                            ->label('Bukti Transfer')
                            ->image()
                            ->directory('invoice-proofs')
                            ->visibility('public')
                            ->maxSize(10240)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label('No. Invoice')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sppg.nama_sppg')
                    ->label('SPPG')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'SPPG_SEWA' => 'Insentif',
                        'LP_ROYALTY' => 'Kontribusi Kornas',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'SPPG_SEWA' => 'info',
                        'LP_ROYALTY' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'UNPAID' => 'Belum Bayar',
                        'WAITING_VERIFICATION' => 'Menunggu Verifikasi',
                        'PAID' => 'Lunas',
                        'REJECTED' => 'Ditolak',
                        default => $state,
                    })
                    ->color(fn (string $state): string =>match ($state) {
                        'UNPAID' => 'gray',
                        'WAITING_VERIFICATION' => 'warning',
                        'PAID' => 'success',
                        'REJECTED' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'UNPAID' => 'Belum Bayar',
                        'WAITING_VERIFICATION' => 'Menunggu Verifikasi',
                        'PAID' => 'Lunas',
                        'REJECTED' => 'Ditolak',
                    ]),
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'SPPG_SEWA' => 'Insentif SPPG',
                        'LP_ROYALTY' => 'Kontribusi Kornas',
                    ]),
            ])
            ->recordActions([ // Filament V4 Syntax
                // EditAction::make()
                //     ->hidden(fn (Invoice $record) => 
                //         auth()->user()->hasRole('Pimpinan Lembaga Pengusul') && 
                //         $record->type === 'SPPG_SEWA'
                //     ),

                // Verifikasi Pembayaran (Specific for LP)
                Action::make('verify_payment')
                    ->label('Verifikasi Pembayaran')
                    ->icon('heroicon-o-shield-check')
                    ->color('success')
                    ->modalHeading('Verifikasi Pembayaran SPPG')
                    ->modalWidth('5xl')
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false)
                    ->visible(fn (Invoice $record) => 
                        $record->status === 'WAITING_VERIFICATION' && (
                            ($record->type === 'SPPG_SEWA' && auth()->user()->hasAnyRole(['Pimpinan Lembaga Pengusul', 'PJ Pelaksana'])) ||
                            ($record->type === 'LP_ROYALTY' && auth()->user()->hasAnyRole(['Staf Akuntan Kornas', 'Superadmin', 'Staf Kornas']))
                        )
                    )
                    ->schema(fn (Invoice $record) => [
                        Section::make('Detail Tagihan')
                            ->columns(3)
                            ->schema([
                                TextEntry::make('invoice_number')->label('No. Invoice')->weight('bold'),
                                TextEntry::make('amount')->label('Jumlah Tagihan')->money('IDR'),
                                TextEntry::make('status')->label('Status')->badge()->color('warning'),
                            ]),

                        Section::make('Data Pengirim')
                            ->columns(2)
                            ->schema([
                                TextEntry::make('source_bank')->label('Bank Sumber'),
                                TextEntry::make('transfer_date')->label('Tanggal Transfer')->date(),
                            ]),

                        Section::make('Bukti Pembayaran')
                            ->schema([
                                ImageEntry::make('proof_of_payment')
                                    ->label('')
                                    ->imageHeight(300)
                                    ->disk('public')
                                    ->columnSpanFull(),
                            ]),

                        Actions::make([
                            Action::make('approve')
                                ->label('Setujui Pembayaran')
                                ->color('success')
                                ->icon('heroicon-o-check')
                                ->requiresConfirmation()
                                ->cancelParentActions()
                                ->action(function (Invoice $record) {
                                    try {
                                        DB::transaction(function () use ($record) {
                                            $record->update([
                                                'status' => 'PAID',
                                                'verified_at' => now(),
                                            ]);

                                            // 2. Record Income (Central Scope) - ONLY for Royalty
                                            if ($record->type === 'LP_ROYALTY') {
                                                SppgIncomingFund::create([
                                                    'sppg_id' => null, // null means Central/Kornas scope
                                                    'user_id' => Auth::id(),
                                                    'amount' => $record->amount,
                                                    'category_id' => 4, // Setoran Lembaga Pengusul
                                                    'received_at' => $record->transfer_date ?? now(),
                                                    'source' => 'Penerimaan Kontribusi Kornas',
                                                    'notes' => "Otomatis dari verifikasi invoice #{$record->invoice_number} ({$record->sppg->nama_sppg})",
                                                    'attachment' => $record->proof_of_payment,
                                                ]);
                                            }

                                            // 3. Generate Royalty ONLY for SPPG_SEWA
                                            if ($record->type === 'SPPG_SEWA') {
                                                $royaltyAmount = $record->amount * 0.10;
                                                $royaltyInvoice = Invoice::create([
                                                    'invoice_number' => 'ROY-' . $record->invoice_number,
                                                    'sppg_id' => $record->sppg_id,
                                                    'type' => 'LP_ROYALTY',
                                                    'amount' => $royaltyAmount,
                                                    'status' => 'UNPAID',
                                                    'start_date' => $record->start_date,
                                                    'end_date' => $record->end_date,
                                                    'due_date' => now()->addDays(3),
                                                ]);

                                                // NOTIFICATION 1: Notify Pimpinan Lembaga about new Royalty Bill
                                                $sppg = $record->sppg;
                                                $lembaga = $sppg?->lembagaPengusul;
                                                $pimpinan = $lembaga?->pimpinan;
                                                
                                                if ($pimpinan) {
                                                    try {
                                                        $pimpinan->notify(new \App\Notifications\RoyaltyPaymentDueNotification($royaltyInvoice));
                                                    } catch (\Exception $e) {
                                                        \Illuminate\Support\Facades\Log::error("Failed to notify Pimpinan about Royalty: " . $e->getMessage());
                                                    }
                                                }
                                            }
                                        });

                                        // NOTIFICATION 3: Notify Pimpinan Lembaga if Royalty Payment is Approved
                                        if ($record->type === 'LP_ROYALTY') {
                                            $sppg = $record->sppg;
                                            $pimpinan = $sppg?->lembagaPengusul?->pimpinan;
                                            
                                            if ($pimpinan) {
                                                try {
                                                    $pimpinan->notify(new \App\Notifications\RoyaltyApprovedNotification($record));
                                                } catch (\Exception $e) {
                                                    \Illuminate\Support\Facades\Log::error("Failed to send Royalty Approval Notification: " . $e->getMessage());
                                                }
                                            }
                                        }

                                        $typeLabel = $record->type === 'SPPG_SEWA' ? 'Insentif' : 'Kontribusi';
                                        Notification::make()->title("Pembayaran {$typeLabel} Disetujui")->success()->send();
                                    } catch (\Exception $e) {
                                        Notification::make()->title('Gagal')->body($e->getMessage())->danger()->send();
                                    }
                                }),

                            Action::make('reject')
                                ->label('Tolak Pembayaran')
                                ->color('danger')
                                ->icon('heroicon-o-x-mark')
                                ->requiresConfirmation()
                                ->cancelParentActions()
                                ->form([
                                    Textarea::make('rejection_reason')
                                        ->label('Alasan Penolakan')
                                        ->required(),
                                ])
                                ->action(function (Invoice $record, array $data) {
                                    $record->update([
                                        'status' => 'REJECTED',
                                        'rejection_reason' => $data['rejection_reason'],
                                    ]);

                                     // NOTIFICATION 4: Notify Pimpinan Lembaga if Royalty Payment is Rejected
                                     if ($record->type === 'LP_ROYALTY') {
                                        $sppg = $record->sppg;
                                        $pimpinan = $sppg?->lembagaPengusul?->pimpinan;
                                        
                                        if ($pimpinan) {
                                            try {
                                                $pimpinan->notify(new \App\Notifications\RoyaltyRejectedNotification($record));
                                            } catch (\Exception $e) {
                                                \Illuminate\Support\Facades\Log::error("Failed to send Royalty Rejection Notification: " . $e->getMessage());
                                            }
                                        }
                                    }

                                    Notification::make()->title('Pembayaran Ditolak')->danger()->send();
                                }),
                        ])->fullWidth(),
                    ]),
                 Action::make('pay_royalty')
                    ->label(fn(Invoice $record) => $record->status === 'WAITING_VERIFICATION' ? 'Sedang Diverifikasi' : 'Bayar Kontribusi')
                    ->icon('heroicon-o-credit-card')
                    ->color('warning')
                    ->visible(fn (Invoice $record) => in_array($record->status, ['UNPAID', 'REJECTED']) 
                        && $record->type === 'LP_ROYALTY'
                        && auth()->user()->hasAnyRole(['Pimpinan Lembaga Pengusul', 'PJ Pelaksana']))
                    ->form([
                        Section::make('Konfirmasi Transfer Kontribusi Kornas')
                            ->schema([
                                Forms\Components\Placeholder::make('payment_details')
                                    ->label('Rincian Pembayaran')
                                    ->content(function (Invoice $record) {
                                        $rows = [];
                                        
                                        $rows[] = [
                                            'desc' => "Kontribusi Kornas (10% Insentif)",
                                            'days' => '-',
                                            'rate' => '-',
                                            'total' => 'Rp ' . number_format($record->amount, 0, ',', '.'),
                                        ];

                                        $tableRows = collect($rows)->map(fn($row) => "
                                            <tr class='bg-white border-b dark:bg-gray-800 dark:border-gray-700'>
                                                <td class='px-4 py-2 font-medium text-gray-900 dark:text-white border border-gray-200 dark:border-gray-600'>{$row['desc']}</td>
                                                <td class='px-4 py-2 text-center border border-gray-200 dark:border-gray-600'>{$row['days']}</td>
                                                <td class='px-4 py-2 text-right border border-gray-200 dark:border-gray-600'>{$row['rate']}</td>
                                                <td class='px-4 py-2 text-right border border-gray-200 dark:border-gray-600'>{$row['total']}</td>
                                            </tr>
                                        ")->join('');

                                        $grandTotal = 'Rp ' . number_format($record->amount, 0, ',', '.');

                                        return new \Illuminate\Support\HtmlString("
                                            <div class='w-full overflow-x-auto border border-gray-200 rounded-lg mb-4 mt-2 dark:border-gray-600'>
                                                <table class='w-full text-sm text-left text-gray-500 dark:text-gray-400'>
                                                    <thead class='text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400'>
                                                        <tr>
                                                            <th class='px-4 py-2 border border-gray-200 dark:border-gray-600'>Keterangan</th>
                                                            <th class='px-4 py-2 border border-gray-200 text-center dark:border-gray-600'>Jumlah Hari</th>
                                                            <th class='px-4 py-2 border border-gray-200 text-right dark:border-gray-600'>Biaya</th>
                                                            <th class='px-4 py-2 border border-gray-200 text-right dark:border-gray-600'>Total</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        {$tableRows}
                                                        <tr class='font-bold text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700'>
                                                            <td class='px-4 py-2 text-right border border-gray-200 dark:border-gray-600' colspan='3'>TOTAL</td>
                                                            <td class='px-4 py-2 text-right border border-gray-200 dark:border-gray-600'>{$grandTotal}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        ");
                                    })
                                    ->columnSpanFull(),

                                Forms\Components\TextInput::make('source_bank')->label('Bank Sumber')->required(),
                                Forms\Components\TextInput::make('destination_bank')->label('Bank Tujuan (Kornas)')->required(),
                                Forms\Components\DatePicker::make('transfer_date')->label('Tanggal Transfer')->default(now())->required(),
                                Forms\Components\FileUpload::make('proof_of_payment')->disk('public')
                                    ->label('Bukti Transfer')
                                    ->image()
                                    ->directory('invoice-proofs')
                                    ->disk('public')
                                    ->maxSize(10240)
                                    ->required(),
                            ])->columns(2)
                    ])
                    ->action(function (Invoice $record, array $data) {
                        $record->update([
                            'source_bank' => $data['source_bank'],
                            'destination_bank' => $data['destination_bank'],
                            'transfer_date' => $data['transfer_date'],
                            'proof_of_payment' => $data['proof_of_payment'],
                            'status' => 'WAITING_VERIFICATION',
                            'rejection_reason' => null,
                        ]);

                        // NOTIFICATION 2: Notify Kornas Staff about Incoming Contribution Payment
                        if ($record->type === 'LP_ROYALTY') {
                            // Defines recipients: Users with specific roles (Matching approved plan + Superadmin)
                            $recipients = \App\Models\User::role(['Staf Kornas', 'Ketua Kornas', 'Staf Akuntan Kornas', 'Superadmin'])->get();
                            
                            foreach ($recipients as $recipient) {
                                try {
                                    $recipient->notify(new \App\Notifications\RoyaltyPaymentSubmittedNotification($record));
                                } catch (\Exception $e) {
                                    \Illuminate\Support\Facades\Log::error("Failed to send Royalty Notification to {$recipient->name}: " . $e->getMessage());
                                }
                            }
                        }

                        Notification::make()->title('Bukti Pembayaran Diupload')->success()->send();
                    }),
                    
                Action::make('view_proof')
                    ->label('Lihat Bukti')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->visible(fn (Invoice $record) => $record->proof_of_payment)
                    ->modalHeading('Bukti Pembayaran')
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false)
                    ->modalContent(function (Invoice $record) {
                        $url = \Illuminate\Support\Facades\Storage::disk('public')->url($record->proof_of_payment);
                        return new \Illuminate\Support\HtmlString("
                            <div class='flex justify-center'>
                                <img src='{$url}' class='max-w-full h-auto rounded-lg shadow-lg' />
                            </div>
                        ");
                    }),
            ])
            ->toolbarActions([ // Filament V4 Syntax
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        // 1. National Level: Can see everything
        if ($user->hasAnyRole(['Superadmin', 'Ketua Kornas', 'Staf Akuntan Kornas', 'Staf Kornas'])) {
            return $query;
        }

        // 2. Local Level: Restrict to SPPG
        // Check for Kepala SPPG
        if ($user->hasRole('Kepala SPPG')) {
            $sppg = $user->sppgDikepalai;
            if ($sppg) {
                return $query->where('sppg_id', $sppg->id);
            }
        }

        // Check for Staff assigned to SPPG (including Staf Akuntan, Admin SPPG, PJ Pelaksana)
        if ($user->hasAnyRole(['Staf Akuntan', 'Admin SPPG', 'PJ Pelaksana', 'Staf Administrator SPPG'])) {
            $unitTugas = $user->unitTugas->first();
            if ($unitTugas) {
                return $query->where('sppg_id', $unitTugas->id);
            }
        }

        // Pimpinan Lembaga Pengusul
        if ($user->hasRole('Pimpinan Lembaga Pengusul')) {
            $lembaga = $user->lembagaDipimpin;
            if ($lembaga) {
                // Show Invoices where SPPG ID is in User's Lembaga's SPPGs
                $sppgIds = $lembaga->sppgs->pluck('id');
                return $query->whereIn('sppg_id', $sppgIds);
            }
        }

        // Fallback: See nothing
        return $query->whereRaw('1 = 0');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            // 'create' => Pages\CreateInvoice::route('/create'),
            // 'edit' => Pages\EditInvoice::route('/{record}/edit'),
            'generate-tool' => Pages\GenerateInvoices::route('/generate-tool'),
        ];
    }
}
