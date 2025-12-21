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
                                'SPPG_SEWA' => 'Sewa SPPG',
                                'LP_ROYALTY' => 'Royalty Kornas',
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
                        'SPPG_SEWA' => 'Sewa SPPG',
                        'LP_ROYALTY' => 'Royalty Kornas',
                    ]),
            ])
            ->recordActions([ // Filament V4 Syntax
                EditAction::make(),
                
                // 1. Verifikasi Pembayaran SPPG (Visible to Admin, Kornas, LP)
                Action::make('verify')
                    ->label('Verifikasi Lunas')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    // Visible for SPPG_SEWA only. 
                    // LP verifies SPPG payment. Admin/Kornas can too.
                    ->visible(fn (Invoice $record) => in_array($record->status, ['UNPAID', 'WAITING_VERIFICATION']) && $record->type === 'SPPG_SEWA')
                    ->action(function (Invoice $record) {
                        $record->update([
                            'status' => 'PAID',
                            'verified_at' => now(),
                        ]);

                        $royaltyAmount = $record->amount * 0.10; // 10%
                            
                        \App\Models\Invoice::create([
                            'invoice_number' => 'ROY-' . $record->invoice_number,
                            'sppg_id' => $record->sppg_id, 
                            'type' => 'LP_ROYALTY',
                            'amount' => $royaltyAmount,
                            'status' => 'UNPAID',
                            'start_date' => $record->start_date,
                            'end_date' => $record->end_date,
                            'due_date' => \Carbon\Carbon::now()->addDays(3),
                        ]);
                            
                        Notification::make()
                            ->title('Pembayaran Terverifikasi')
                            ->body('Tagihan Royalty (10%) otomatis diterbitkan untuk Lembaga Pengusul.')
                            ->success()
                            ->send();
                    }),

                // 2. Verifikasi Pembayaran Royalty (Visible to Admin, Kornas ONLY)
                Action::make('verify_royalty')
                    ->label('Verifikasi Royalty')
                    ->icon('heroicon-o-check-badge')
                    ->color('primary')
                    ->requiresConfirmation()
                    // Hidden from Pimpinan Lembaga Pengusul (Self-verification prevention)
                    ->visible(fn (Invoice $record) => in_array($record->status, ['UNPAID', 'WAITING_VERIFICATION']) 
                        && $record->type === 'LP_ROYALTY'
                        && !auth()->user()->hasRole('Pimpinan Lembaga Pengusul'))
                    ->action(function (Invoice $record) {
                        $record->update([
                            'status' => 'PAID',
                            'verified_at' => now(),
                        ]);
                        
                        Notification::make()
                            ->title('Royalty Diterima')
                            ->body('Pembayaran Royalty ke Kornas terverifikasi.')
                            ->success()
                            ->send();
                    }),

                 // 3. Bayar Royalty (Upload Bukti) - Visible to LP
                 Action::make('pay_royalty')
                    ->label(fn(Invoice $record) => $record->status === 'WAITING_VERIFICATION' ? 'Sedang Diverifikasi' : 'Bayar Royalty')
                    ->icon('heroicon-o-credit-card')
                    ->color('warning')
                    ->visible(fn (Invoice $record) => in_array($record->status, ['UNPAID', 'REJECTED']) 
                        && $record->type === 'LP_ROYALTY'
                        && auth()->user()->hasRole('Pimpinan Lembaga Pengusul'))
                    ->form([
                        Section::make('Konfirmasi Transfer Royalty')
                            ->schema([
                                Forms\Components\TextInput::make('source_bank')->label('Bank Sumber')->required(),
                                Forms\Components\TextInput::make('destination_bank')->label('Bank Tujuan (Kornas)')->required(),
                                Forms\Components\DatePicker::make('transfer_date')->label('Tanggal Transfer')->default(now())->required(),
                                Forms\Components\FileUpload::make('proof_of_payment')
                                    ->label('Bukti Transfer')
                                    ->image()
                                    ->directory('invoice-proofs')
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
                    ->url(fn (Invoice $record) => $record->proof_of_payment ? \Illuminate\Support\Facades\Storage::url($record->proof_of_payment) : null)
                    ->openUrlInNewTab()
                    ->visible(fn (Invoice $record) => $record->proof_of_payment),
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
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
