<?php

namespace App\Livewire;

use Filament\Actions\BulkActionGroup;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Invoice; // Changed from Remittance
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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;

class VerifyPaymentList extends TableWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(function (): Builder {

                $query = Invoice::query();

                if (Auth::user()->hasRole('Pimpinan Lembaga Pengusul')) {
                    $allowedSppgIds = Auth::user()
                        ->lembagaDipimpin
                        ->sppgs
                        ->pluck('id')
                        ->toArray();

                    $query->whereIn('sppg_id', $allowedSppgIds)
                          ->where('type', 'SPPG_SEWA');
                }

                if (Auth::user()->hasAnyRole(['Staf Kornas', 'Direktur Kornas'])) {
                    $query->where('type', 'LP_ROYALTY');
                }

                $query->where('status', 'WAITING_VERIFICATION');
                $query->orderBy('updated_at', 'desc');

                return $query;
            })
            ->columns([
                Stack::make([
                    TextColumn::make('sppg.nama_sppg')
                        ->label('SPPG')
                        ->weight('bold')
                        ->icon('heroicon-m-building-office')
                        ->visible(fn () => Auth::user()->hasAnyRole(['Staf Kornas', 'Staf Akuntan Kornas', 'Direktur Kornas'])),

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
            ->filters([])
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
                    ->modalSubmitAction(false) // Read only
                    ->modalCancelAction(false)
                    ->schema(fn($record) => $record
                        ? $this->getDetailsSchema($record)
                        : [
                            TextEntry::make('missing')
                                ->label('Record tidak ditemukan')
                                ->disabled()
                        ])
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }

    protected function getDetailsSchema(Invoice $record): array
    {
        $isPending = $record->status === 'WAITING_VERIFICATION';
        $hasPermission = $this->canVerifyOrReject();

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

                    // Rejection Reason
                    TextEntry::make('rejection_reason')
                        ->label('Alasan Penolakan')
                        ->color('danger')
                        ->visible(fn(Invoice $record) => $record->status === 'REJECTED')
                        ->columnSpanFull(),

                    // Bukti Transfer
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

                            // Verify and Reject Actions
                            Actions::make([
                                Action::make('verify')
                                    ->label('Verifikasi Pembayaran')
                                    ->icon('heroicon-m-check-circle')
                                    ->color('success')
                                    ->visible($isPending && $hasPermission)
                                    ->requiresConfirmation()
                                    ->modalHeading('Konfirmasi Verifikasi')
                                    ->modalDescription('Apakah Anda yakin ingin menyetujui dan memverifikasi pembayaran ini? Konfirmasi pembayaran tidak dapat dibatalkan.')
                                    ->action(function (Invoice $record) {
                                        try {
                                            $record->update([
                                                'status' => 'PAID',
                                                'verified_at' => now(),
                                            ]);
                                            Notification::make()->title('Pembayaran Diverifikasi')->success()->send();
                                        } catch (Exception $e) {
                                            Notification::make()->title('Gagal Verifikasi')->body('Error: ' . $e->getMessage())->danger()->send();
                                        }
                                    }),

                                Action::make('reject')
                                    ->label('Tolak Pembayaran')
                                    ->icon('heroicon-m-x-circle')
                                    ->color('danger')
                                    ->visible($isPending && $hasPermission)
                                    ->schema([
                                        Textarea::make('rejection_reason')
                                            ->label('Alasan Penolakan')
                                            ->required()
                                            ->maxLength(500),
                                    ])
                                    ->modalHeading('Tolak Pembayaran')
                                    ->modalDescription('Masukkan alasan penolakan.')
                                    ->action(function (Invoice $record, array $data) {
                                        try {
                                            $record->update([
                                                'status' => 'REJECTED',
                                                'rejection_reason' => $data['rejection_reason'],
                                            ]);
                                            Notification::make()->title('Pembayaran Ditolak')->success()->send();
                                        } catch (Exception $e) {
                                            Notification::make()->title('Gagal Menolak')->body('Error: ' . $e->getMessage())->danger()->send();
                                        }
                                    })
                                    ->requiresConfirmation(),
                            ])
                        ])
                        ->columnSpanFull(),
                ]),
        ];
    }

    private function canVerifyOrReject(): bool
    {
        $user = Auth::user();
        return $user && ($user->hasAnyRole(['Pimpinan Lembaga Pengusul', 'Staf Kornas', 'Staf Akuntan Kornas', 'Direktur Kornas']));
    }
}
