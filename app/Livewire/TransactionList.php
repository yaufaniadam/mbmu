<?php

namespace App\Livewire;

use Filament\Actions\BulkActionGroup;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Remittance;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;

class TransactionList extends TableWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(function (): Builder {
                $query = Remittance::query();
                if (Auth::user()->hasRole('Kepala SPPG')) {
                    $query->whereHas('bill', function ($q) {
                        $q->where('sppg_id', User::find(Auth::user()->id)->sppgDikepalai->id);
                        $q->where('billed_to_type', 'sppg');
                    });
                }
                if (Auth::user()->hasRole('PJ Pelaksana')) {
                    $query->whereHas('bill', function ($q) {
                        $q->where('sppg_id', User::find(Auth::user()->id)->unitTugas->first()->id);
                        $q->where('billed_to_type', 'sppg');
                    });
                }
                if (Auth::user()->hasRole('Pimpinan Lembaga Pengusul')) {
                    $query->whereHas('bill', function ($q) {
                        $q->whereIn('sppg_id', User::find(Auth::user()->id)->lembagaDipimpin->sppgs->pluck('id')->toArray());
                        $q->where('billed_to_type', 'pengusul');
                    });
                }
                return $query;
            })
            ->columns([
                Stack::make([
                    TextColumn::make('period_range')
                        ->label('Periode')
                        // SAFE ACCESS: Memastikan $record tidak null sebelum diakses
                        ->state(fn(?Remittance $record): ?string => $record ? "Periode {$record->bill->period_start} s.d {$record->bill->period_end}" : null)
                        ->icon('heroicon-m-calendar'),

                    TextColumn::make('bill.invoice_number')
                        ->label('Nomor Invoice')
                        ->money('idr', true)
                        ->icon('heroicon-m-banknotes'),

                    TextColumn::make('status')
                        ->badge()
                        ->formatStateUsing(fn(string $state): string => match ($state) {
                            'pending' => 'Menunggu Verifikasi',
                            'verified' => 'Pembayaran Diterima',
                            'rejected' => 'Pembayaran Ditolak',
                            default => $state,
                        })
                        ->color(fn(string $state): string => match ($state) {
                            'pending' => 'warning',
                            'verified' => 'success',
                            'rejected' => 'danger',
                            default => 'gray',
                        })
                        ->icon(fn(string $state): string => match ($state) {
                            'pending' => 'heroicon-m-arrow-path',
                            'verified' => 'heroicon-m-check',
                            'rejected' => 'heroicon-m-x-mark',
                            default => null,
                        }),
                ])
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                // ACTION TOMBOL DETAIL
                Action::make('view_details')
                    ->label('Detail Transaksi')
                    ->icon('heroicon-m-eye')
                    ->color('gray')
                    ->button()
                    ->modalWidth('5xl')
                    ->modalSubmitAction(false) // Read only
                    ->modalCancelAction(false)
                    ->schema(fn(Remittance $record): array => $this->getDetailsSchema($record))
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }

    protected function getDetailsSchema(Remittance $record): array
    {
        return [
            // BAGIAN 1: DETAIL TAGIHAN (Dari Relasi Bill)
            Section::make('Detail Referensi Tagihan')
                ->heading('Faktur Tagihan')
                ->icon('heroicon-m-document-text')
                ->columns(3)
                ->schema([
                    TextEntry::make('bill.invoice_number')
                        ->label('Nomor Invoice')
                        ->weight('bold')
                        ->copyable(),

                    TextEntry::make('bill.period_range')
                        ->label('Periode')
                        ->state(fn(Remittance $record) => "{$record->bill->period_start} s.d {$record->bill->period_end}"),

                    TextEntry::make('bill.amount')
                        ->label('Total Tagihan')
                        ->money('idr', true)
                        ->color('gray'),
                ]),

            // BAGIAN 2: DETAIL TRANSAKSI (Data Remittance itu sendiri)
            Section::make('Data Pembayaran')
                ->heading('Detail Transfer')
                ->icon('heroicon-m-banknotes')
                ->columns(3)
                ->schema([
                    TextEntry::make('status')
                        ->label('Status Pembayaran')
                        ->badge()
                        ->formatStateUsing(fn(string $state): string => match ($state) {
                            'pending' => 'Menunggu Verifikasi',
                            'verification' => 'Sedang Diverifikasi', // Sesuaikan jika ada status verification
                            'verified' => 'Pembayaran Diterima',
                            'rejected' => 'Pembayaran Ditolak',
                            default => $state,
                        })
                        ->color(fn(string $state): string => match ($state) {
                            'pending', 'verification' => 'warning',
                            'verified' => 'success',
                            'rejected' => 'danger',
                            default => 'gray',
                        })
                        ->columnSpanFull(),

                    TextEntry::make('source_bank_name')
                        ->label('Bank Pengirim'),

                    TextEntry::make('destination_bank_name')
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

                    // Rejection Reason (Hanya jika ditolak)
                    TextEntry::make('rejection_reason')
                        ->label('Alasan Penolakan')
                        ->color('danger')
                        ->visible(fn(Remittance $record) => $record->status === 'rejected')
                        ->columnSpanFull(),

                    // Bukti Transfer
                    ImageEntry::make('proof_file_path')
                        ->label('Bukti Transfer')
                        ->disk('local') // Pastikan sesuai dengan config filesystem
                        ->columnSpanFull()
                        ->imageHeight(250)
                        ->imageWidth('100%'),

                    // === TOMBOL MODAL ZOOM (Sama seperti BillList) ===
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
                                $path = $record->proof_file_path;

                                if (!$path || !Storage::disk('local')->exists($path)) {
                                    return new HtmlString('<div style="padding: 1rem; text-align: center; color: #ef4444;">File bukti transfer tidak ditemukan.</div>');
                                }

                                $fileContent = Storage::disk('local')->get($path);
                                $mimeType = Storage::disk('local')->mimeType($path);
                                $base64 = base64_encode($fileContent);
                                $src = "data:{$mimeType};base64,{$base64}";

                                return new HtmlString('
                                    <div style="display: flex; justify-content: center; align-items: center; border-radius: 0.5rem; padding: 0.5rem;">
                                        <img src="' . $src . '" alt="Bukti Transfer Full" style="max-width: 100%; max-height: 85vh; object-fit: contain; border-radius: 8px;">
                                    </div>
                                ');
                            }),
                    ])->fullWidth(),
                ]),
        ];
    }
}
