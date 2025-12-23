<?php

namespace App\Livewire;

use App\Models\Invoice;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BillList extends TableWidget
{
    public ?string $type = null;

    public function getHeading(): ?string
    {
        return $this->type === 'LP_ROYALTY' ? 'Daftar Tagihan Royalti' : 'Daftar Tagihan Sewa';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(function (): Builder {
                $user = Auth::user();
                $query = Invoice::query();
                
                // 1. Explicit type provided (preferred)
                if ($this->type) {
                    $query->where('type', $this->type);
                    
                    if ($this->type === 'LP_ROYALTY' && $user->hasRole('Pimpinan Lembaga Pengusul')) {
                        $allowedSppgIds = $user->lembagaDipimpin?->sppgs->pluck('id')->toArray() ?? [];
                        return $query->whereIn('sppg_id', $allowedSppgIds);
                    }
                    
                    // Fallback for types in SPPG role
                    $sppg = $user->hasRole('Kepala SPPG') ? $user->sppgDikepalai : $user->unitTugas->first();
                    if ($sppg) {
                        return $query->where('sppg_id', $sppg->id);
                    }
                    
                    return $query->whereRaw('1=0');
                }

                // 2. Role-based fallback (Legacy/Safety)
                if ($user->hasRole('Pimpinan Lembaga Pengusul')) {
                    // Lembaga Pengusul pays Royalty to Kornas
                    $query->where('type', 'LP_ROYALTY');
                    
                    // Show only invoices belonging to their SPPGs
                    $allowedSppgIds = $user->lembagaDipimpin?->sppgs->pluck('id')->toArray() ?? [];
                    return $query->whereIn('sppg_id', $allowedSppgIds);
                }

                // Default: SPPG Rent Invoices
                $query->where('type', 'SPPG_SEWA');

                $sppg = null;
                if ($user->hasRole('Kepala SPPG')) {
                    $sppg = $user->sppgDikepalai;
                } elseif ($user->hasAnyRole(['PJ Pelaksana', 'Staf Akuntan', 'Staf Administrator SPPG'])) {
                     $sppg = $user->unitTugas->first();
                }

                if ($sppg) {
                     return $query->where('sppg_id', $sppg->id);
                }
                
                return $query->whereRaw('1=0');
            })
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('No. Invoice')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('period')
                    ->label('Periode')
                    ->state(fn (Invoice $record) => $record->start_date->format('d M') . ' - ' . $record->end_date->format('d M')),
                TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'UNPAID' => 'gray',
                        'WAITING_VERIFICATION' => 'warning',
                        'PAID' => 'success',
                        'REJECTED' => 'danger',
                    }),
                TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'UNPAID' => 'Belum Bayar',
                        'WAITING_VERIFICATION' => 'Menunggu Verifikasi',
                        'PAID' => 'Lunas',
                        'REJECTED' => 'Ditolak',
                    ]),
            ])
            ->recordActions([
                 Action::make('pay')
                    ->label(fn(Invoice $record) => $record->status === 'PAID' ? 'Lihat Bukti' : ($record->status === 'WAITING_VERIFICATION' ? 'Sedang Diverifikasi' : 'Bayar'))
                    ->icon(fn(Invoice $record) => $record->status === 'PAID' ? 'heroicon-m-eye' : 'heroicon-m-credit-card')
                    ->color(fn(Invoice $record) => $record->status === 'PAID' ? 'secondary' : 'primary')
                    ->modalHeading(fn($record) => $record->status === 'PAID' ? 'Detail Pembayaran' : 'Konfirmasi Pembayaran')
                    ->modalDescription(fn($record) => $record->status === 'PAID' ? 'Berikut adalah data pembayaran yang telah diverifikasi.' : 'Silakan isi formulir di bawah ini.')
                    ->visible(fn(Invoice $record) => true)
                    ->disabled(fn(Invoice $record) => $record->status === 'WAITING_VERIFICATION')
                    ->form([
                        Section::make('Informasi Transfer')
                            ->schema([
                                TextInput::make('source_bank')
                                    ->label('Bank Sumber')
                                    ->placeholder('Contoh: BSI, Mandiri')
                                    ->required()
                                    ->disabled(fn ($record) => $record->status !== 'UNPAID' && $record->status !== 'REJECTED'),
                                    
                                TextInput::make('destination_bank')
                                    ->label('Bank Tujuan')
                                    ->placeholder('Nama Bank Penerima')
                                    ->required()
                                    ->disabled(fn ($record) => $record->status !== 'UNPAID' && $record->status !== 'REJECTED'),
                                    
                                DatePicker::make('transfer_date')
                                    ->label('Tanggal Transfer')
                                    ->default(now())
                                    ->required()
                                    ->disabled(fn ($record) => $record->status !== 'UNPAID' && $record->status !== 'REJECTED')
                                    ->columnSpanFull(),
                            ])->columns(2),

                        Textarea::make('rejection_reason_view')
                            ->label('Alasan Penolakan')
                            ->helperText('Perbaiki data pembayaran sesuai catatan di sini.')
                            ->visible(fn ($record) => $record->status === 'REJECTED')
                            ->disabled()
                            ->columnSpanFull()
                            ->default(fn ($record) => $record->rejection_reason),

                        FileUpload::make('proof_of_payment')
                            ->label('Bukti Transfer')
                            ->image()
                            ->directory('invoice-proofs')
                            ->visibility('public')
                            ->required()
                            ->disabled(fn ($record) => $record->status === 'PAID')
                            ->columnSpanFull(),
                    ])
                    ->action(function (Invoice $record, array $data) {
                        if ($record->status === 'PAID' || $record->status === 'WAITING_VERIFICATION') {
                            return; 
                        }
                        
                        $record->update([
                            'proof_of_payment' => $data['proof_of_payment'],
                            'source_bank' => $data['source_bank'],
                            'destination_bank' => $data['destination_bank'],
                            'transfer_date' => $data['transfer_date'],
                            'status' => 'WAITING_VERIFICATION',
                            'rejection_reason' => null, 
                        ]);

                        Notification::make()
                            ->title('Pembayaran Dikirim')
                            ->body('Detail pembayaran bank Anda telah disimpan.')
                            ->success()
                            ->send();
                    })
                    ->modalSubmitActionLabel('Kirim Konfirmasi')
            ])
            ->toolbarActions([
                //
            ]);
    }
}
