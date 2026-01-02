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
                        Forms\Components\FileUpload::make('proof_of_payment')
                            ->label('Bukti Transfer')
                            ->image()
                            ->directory('invoice-proofs')
                            ->visibility('public')
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
                    ->color(fn (string $state): string => match ($state) {
                        'SPPG_SEWA' => 'info',
                        'LP_ROYALTY' => 'warning',
                    }),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
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
                            ($record->type === 'SPPG_SEWA' && auth()->user()->hasRole('Pimpinan Lembaga Pengusul')) ||
                            ($record->type === 'LP_ROYALTY' && auth()->user()->hasAnyRole(['Staf Akuntan Kornas', 'Superadmin']))
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
                                                Invoice::create([
                                                    'invoice_number' => 'ROY-' . $record->invoice_number,
                                                    'sppg_id' => $record->sppg_id,
                                                    'type' => 'LP_ROYALTY',
                                                    'amount' => $royaltyAmount,
                                                    'status' => 'UNPAID',
                                                    'start_date' => $record->start_date,
                                                    'end_date' => $record->end_date,
                                                    'due_date' => now()->addDays(3),
                                                ]);
                                            }
                                        });

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
                        && auth()->user()->hasRole('Pimpinan Lembaga Pengusul'))
                    ->form([
                        Section::make('Konfirmasi Transfer Kontribusi Kornas')
                            ->schema([
                                Forms\Components\TextInput::make('source_bank')->label('Bank Sumber')->required(),
                                Forms\Components\TextInput::make('destination_bank')->label('Bank Tujuan (Kornas)')->required(),
                                Forms\Components\DatePicker::make('transfer_date')->label('Tanggal Transfer')->default(now())->required(),
                                Forms\Components\FileUpload::make('proof_of_payment')
                                    ->label('Bukti Transfer')
                                    ->image()
                                    ->directory('invoice-proofs')
                                    ->disk('public')
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
                        $url = \Illuminate\Support\Facades\Storage::url($record->proof_of_payment);
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

        if ($user->hasRole('Pimpinan Lembaga Pengusul')) {
            $lembaga = \App\Models\User::find($user->id)->lembagaDipimpin;
            if ($lembaga) {
                // Show Invoices where SPPG ID is in User's Lembaga's SPPGs
                $sppgIds = $lembaga->sppgs->pluck('id');
                $query->whereIn('sppg_id', $sppgIds);
            } else {
                // Safety fallback
                $query->whereRaw('1=0');
            }
        }

        return $query;
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
        ];
    }
}
