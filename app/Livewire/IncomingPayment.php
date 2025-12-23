<?php

namespace App\Livewire;

use Filament\Actions\BulkActionGroup;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Invoice;
use App\Models\User;
use Exception;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

class IncomingPayment extends TableWidget
{
    public ?string $type = null;
    protected static ?string $heading = 'Pembayaran Masuk';

    public function table(Table $table): Table
    {
        return $table
            ->query(function (): Builder {
                $query = Invoice::query();

                if ($this->type) {
                    $query->where('type', $this->type);
                }

                if (Auth::user()->hasRole('Pimpinan Lembaga Pengusul') && $this->type !== 'LP_ROYALTY') {
                    $allowedSppgIds = Auth::user()
                        ->lembagaDipimpin
                        ->sppgs
                        ->pluck('id')
                        ->toArray();

                    // Incoming Rent from SPPGs
                    $query->whereIn('sppg_id', $allowedSppgIds);
                    
                    if (!$this->type) {
                        $query->where('type', 'SPPG_SEWA');
                    }
                }

                // Kornas sees everything (no restriction needed)

                // Show Paid, Rejected AND WAITING VERIFICATION
                // This ensures the user sees the payment immediately after submission.
                $query->whereIn('status', ['PAID', 'REJECTED', 'WAITING_VERIFICATION']);

                $query->orderBy('updated_at', 'desc');

                return $query;
            })
            ->columns([
                Stack::make([
                    TextColumn::make('period_range')
                        ->label('Periode')
                        ->state(fn(Invoice $record) => "Periode " . $record->start_date->format('d M Y') . " s.d " . $record->end_date->format('d M Y'))
                        ->icon('heroicon-m-calendar'),

                    TextColumn::make('invoice_number')
                        ->label('Nomor Invoice')
                        ->formatStateUsing(fn ($state) => $state)
                        ->description(fn (Invoice $record) => 'Rp ' . number_format($record->amount, 0, ',', '.'))
                        ->icon('heroicon-m-banknotes'),

                    TextColumn::make('status')
                        ->badge()
                        ->formatStateUsing(fn(string $state): string => match ($state) {
                            'WAITING_VERIFICATION' => 'Menunggu Verifikasi',
                            'PAID' => 'Pembayaran Diterima',
                            'REJECTED' => 'Pembayaran Ditolak',
                            'UNPAID' => 'Belum Dibayar',
                            default => $state,
                        })
                        ->color(fn(string $state): string => match ($state) {
                            'WAITING_VERIFICATION' => 'warning',
                            'PAID' => 'success',
                            'REJECTED' => 'danger',
                            default => 'gray',
                        })
                        ->icon(fn(string $state): string => match ($state) {
                            'WAITING_VERIFICATION' => 'heroicon-m-arrow-path',
                            'PAID' => 'heroicon-m-check',
                            'REJECTED' => 'heroicon-m-x-mark',
                            default => null,
                        }),
                ])
            ])
            ->filters([
                Filter::make('status_filter')
                    ->label('Status Pembayaran')
                    ->schema([
                        CheckboxList::make('statuses')
                            ->options([
                                'WAITING_VERIFICATION' => 'Menunggu Verifikasi',
                                'PAID' => 'Pembayaran Diterima',
                                'REJECTED' => 'Pembayaran Ditolak',
                            ])
                            ->columns(2)
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (empty($data['statuses'])) {
                            return $query;
                        }
                        return $query->whereIn('status', $data['statuses']);
                    }),
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                Action::make('view_details')
                    ->label('Detail Transaksi')
                    ->icon('heroicon-m-eye')
                    ->color('gray')
                    ->button()
                    ->modalWidth('5xl')
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false)
                    ->schema(fn(Invoice $record): array => $this->getDetailsSchema($record))
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }

    protected function getDetailsSchema(Invoice $record): array
    {
        return [
            // Section 1: Invoice Details
            Section::make('Detail Referensi Tagihan')
                ->heading('Faktur Tagihan')
                ->icon('heroicon-m-document-text')
                ->columns(3)
                ->schema([
                    TextEntry::make('invoice_number')
                        ->label('Nomor Invoice')
                        ->weight('bold')
                        ->copyable(),

                    TextEntry::make('period')
                        ->label('Periode')
                        ->state(fn(Invoice $record) => $record->start_date->format('d M Y') . " s.d " . $record->end_date->format('d M Y')),

                    TextEntry::make('amount')
                        ->label('Total Tagihan')
                        ->money('idr', true)
                        ->color('gray'),
                ]),

            // Section 2: Payer Info
            Section::make('Pelaku Pembayaran')
                ->heading('Informasi Pengirim Pembayaran')
                ->icon('heroicon-m-user-group')
                ->columns(3)
                ->schema([
                    TextEntry::make('sppg.nama_sppg')
                        ->label('SPPG / Unit')
                        ->weight('bold')
                        ->copyable(),
                    TextEntry::make('type')
                        ->label('Tipe Pembayaran')
                        ->badge(),
                ]),

            // Section 3: Payment Data
            Section::make('Data Pembayaran')
                ->heading('Detail Transfer')
                ->icon('heroicon-m-banknotes')
                ->columns(3)
                ->schema([
                    TextEntry::make('status')
                        ->label('Status Pembayaran')
                        ->badge()
                        ->formatStateUsing(fn(string $state): string => match ($state) {
                            'WAITING_VERIFICATION' => 'Menunggu Verifikasi',
                            'PAID' => 'Pembayaran Diterima',
                            'REJECTED' => 'Pembayaran Ditolak',
                            default => $state,
                        })
                        ->color(fn(string $state): string => match ($state) {
                            'WAITING_VERIFICATION' => 'warning',
                            'PAID' => 'success',
                            'REJECTED' => 'danger',
                            default => 'gray',
                        })
                        ->columnSpanFull(),

                    TextEntry::make('source_bank')
                        ->label('Bank Pengirim'),

                    TextEntry::make('destination_bank')
                        ->label('Bank Tujuan'),

                    TextEntry::make('transfer_date')
                        ->label('Tanggal Transfer')
                        ->date('d M Y'),

                    TextEntry::make('amount')
                        ->label('Jumlah Ditransfer')
                        ->money('idr', true)
                        ->size('lg')
                        ->color('success')
                        ->columnSpanFull(),

                    TextEntry::make('rejection_reason')
                        ->label('Alasan Penolakan')
                        ->color('danger')
                        ->visible(fn(Invoice $record) => $record->status === 'REJECTED')
                        ->columnSpanFull(),

                    ImageEntry::make('proof_of_payment')
                        ->label('Bukti Transfer')
                        ->disk('public')
                        ->columnSpanFull()
                        ->imageHeight(250)
                        ->imageWidth('100%'),

                    Section::make('')
                        ->schema([
                            Actions::make([
                                Action::make('view_full_image')
                                    ->label('Lihat Gambar Penuh')
                                    ->icon('heroicon-m-magnifying-glass-plus')
                                    ->color('gray')
                                    ->modalWidth('7xl')
                                    ->modalHeading('Bukti Transfer - Tampilan Penuh')
                                    ->modalSubmitAction(false)
                                    ->modalCancelAction(false)
                                    ->modalContent(function () use ($record) {
                                        $path = $record->proof_of_payment;

                                        if (!$path) {
                                             return new HtmlString('<div style="padding: 1rem; text-align: center; color: #ef4444;">File bukti transfer tidak ditemukan.</div>');
                                        }

                                        $url = Storage::url($path);

                                        return new HtmlString('
                                    <div style="display: flex; justify-content: center; align-items: center; border-radius: 0.5rem; padding: 0.5rem;">
                                        <img src="' . $url . '" alt="Bukti Transfer Full" style="max-width: 100%; max-height: 85vh; object-fit: contain; border-radius: 8px;">
                                    </div>
                                ');
                                    }),
                            ])->fullWidth(),
                        ])
                        ->columnSpanFull(),
                ]),
        ];
    }
}
