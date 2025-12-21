<?php

namespace App\Livewire;

use Filament\Actions\BulkActionGroup;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Remittance;
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

                $query = Remittance::query();

                if (Auth::user()->hasRole('Pimpinan Lembaga Pengusul')) {

                    $allowedSppgIds = User::find(Auth::id())
                        ->lembagaDipimpin
                        ->sppgs
                        ->pluck('id')
                        ->toArray();

                    $query
                        ->whereHas('bill', function ($q) use ($allowedSppgIds) {
                            $q->whereIn('sppg_id', $allowedSppgIds);
                            $q->where('billed_to_type', 'sppg');
                        });
                }

                // SAFE ACCESS: Memastikan $user tidak null sebelum diakses('Staf Kornas'))
                if (Auth::user()->hasAnyRole(['Staf Kornas', 'Direktur Kornas'])) {
                    $query->whereHas('bill', function ($q) {
                        $q->where('billed_to_type', 'pengusul');
                    });
                }

                // dd($query->toSql());

                $query->where('status', 'pending');
                $query->orderBy('created_at', 'desc');

                return $query;
            })
            ->columns([
                Stack::make([
                    TextColumn::make('bill.sppg.nama_sppg')
                        ->label('SPPG')
                        ->weight('bold')
                        ->icon('heroicon-m-building-office')
                        ->visible(fn () => Auth::user()->hasAnyRole(['Staf Kornas', 'Staf Akuntan Kornas', 'Direktur Kornas'])),

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
            ->filters([])
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

    protected function getDetailsSchema(Remittance $record): array
    {
        $isPending = $record->status === 'pending' || $record->status === 'verification';
        $hasPermission = $this->canVerifyOrReject();

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

            // Bagian 2: PELAKU PEMBAYARAN
            Section::make('Pelaku Pembayaran')
                ->heading('Informasi Pengirim Pembayaran')
                ->icon('heroicon-m-user-group')
                ->columns(3)
                ->schema([
                    TextEntry::make('user.name')
                        ->label('Nama Pengirim')
                        ->weight('bold')
                        ->copyable(),
                    TextEntry::make('bill.sppg.nama_sppg')
                        ->label('SPPG')
                        ->weight('bold')
                        ->copyable(),
                    TextEntry::make('jabatan')
                        ->label('Jabatan')
                        ->weight('bold')
                        ->copyable()
                        ->state(function ($record) {
                            return $record->user?->getRoleNames()->join(', ');
                        }),
                ]),

            // BAGIAN 3: DETAIL TRANSAKSI (Data Remittance itu sendiri)
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

                            // TOMBOL VERIFIKASI & TOLAK
                            Actions::make([
                                Action::make('verify')
                                    ->label('Verifikasi Pembayaran')
                                    ->icon('heroicon-m-check-circle')
                                    ->color('success')
                                    // MODIFIKASI: Hanya terlihat jika status pending DAN pengguna memiliki izin
                                    ->visible($isPending && $hasPermission)
                                    ->requiresConfirmation()
                                    ->modalHeading('Konfirmasi Verifikasi')
                                    ->modalDescription('Apakah Anda yakin ingin menyetujui dan memverifikasi pembayaran ini? Konfirmasi pembayaran tidak dapat dibatalkan.')
                                    ->action(function (?Remittance $record) {
                                        try {
                                            DB::beginTransaction();

                                            $record->update(['status' => 'verified']);
                                            $record->bill->update(['status' => 'paid']);

                                            DB::commit();
                                            // $action->cancel();
                                            Notification::make()->title('Pembayaran Diverifikasi')->success()->send();
                                        } catch (Exception $e) {
                                            DB::rollBack();
                                            Notification::make()->title('Gagal Verifikasi')->body('Error: ' . $e->getMessage())->danger()->send();
                                        }
                                    }),

                                // Tombol Tolak
                                Action::make('reject')
                                    ->label('Tolak Pembayaran')
                                    ->icon('heroicon-m-x-circle')
                                    ->color('danger')
                                    // MODIFIKASI: Hanya terlihat jika status pending DAN pengguna memiliki izin
                                    ->visible($isPending && $hasPermission)
                                    ->schema([
                                        Textarea::make('rejection_reason')
                                            ->label('Alasan Penolakan')
                                            ->required()
                                            ->maxLength(500),
                                    ])
                                    ->modalHeading('Tolak Pembayaran')
                                    ->modalDescription('Masukkan alasan penolakan. Status tagihan terkait juga akan diubah menjadi "rejected".')
                                    ->action(function (?Remittance $record, array $data) {
                                        try {
                                            DB::beginTransaction();

                                            $record->update([
                                                'status' => 'rejected',
                                                'rejection_reason' => $data['rejection_reason'],
                                            ]);
                                            $record->bill->update(['status' => 'unpaid']);

                                            DB::commit();
                                            // $this->dispatch('close-modal');
                                            // $this->dispatch('close-modal');
                                            Notification::make()->title('Pembayaran Ditolak')->success()->send();
                                        } catch (Exception $e) {
                                            DB::rollBack();
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
        return $user && ($user->hasAnyRole(['Pimpinan Lembaga Pengusul', 'Staf Kornas', 'Staf Akuntan Kornas']));
    }
}
