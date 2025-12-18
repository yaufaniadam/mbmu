<?php

namespace App\Livewire;

use App\Models\Bill;
use App\Models\Remittance;
use App\Models\User;
use Exception;
use Filament\Actions\Action; // Import yang dibutuhkan untuk filter header
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;

class BillList extends TableWidget
{
    protected static ?string $heading = 'Daftar Tagihan';

    // Properti publik untuk melacak status yang dipilih (Digunakan untuk filter)
    public array $selectedStatuses = [];

    public array $selectedStatusesComputed = [];

    // Helper untuk mendefinisikan status dan propertinya
    protected function getStatusOptions(): array
    {
        return [
            'unpaid' => ['label' => 'Menunggu Pembayaran', 'color' => 'warning', 'icon' => 'heroicon-m-credit-card'],
            'verification' => ['label' => 'Menunggu Verifikasi', 'color' => 'info', 'icon' => 'heroicon-m-arrow-path'],
            'paid' => ['label' => 'Pembayaran Berhasil', 'color' => 'success', 'icon' => 'heroicon-m-check'],
            'rejected' => ['label' => 'Pembayaran Ditolak', 'color' => 'danger', 'icon' => 'heroicon-m-x-mark'],
        ];
    }

    // Metode untuk menambah atau menghapus status dari filter saat tombol diklik
    public function toggleStatusFilter(string $status): void
    {
        if (in_array($status, $this->selectedStatuses)) {
            // Hapus status: Gunakan array_diff untuk menghapus value dan array_values untuk reset key
            $this->selectedStatuses = array_values(array_diff($this->selectedStatuses, [$status]));
        } else {
            // Tambah status: Gabungkan array dan gunakan array_values
            $this->selectedStatuses = array_values(array_merge($this->selectedStatuses, [$status]));
        }

        // dump('this is selectedStatuses debugged inside toggleStatusFilter: ' . implode(', ', $this->selectedStatuses));

        // Memuat ulang tabel dengan event Livewire
        // $this->dispatch('$refresh');
    }

    public function table(Table $table): Table
    {
        return $table
            // PENTING: Menggunakan closure query yang membaca properti $selectedStatuses
            ->query(function (): Builder {
                $query = Bill::query();

                // 1. Membersihkan array dari nilai non-string atau kosong dan mengatur ulang indeks.
                $statuses = array_values(array_filter($this->selectedStatuses, 'is_string'));

                // 2. Menerapkan filter hanya jika ada status yang valid.
                if (! empty($statuses)) {
                    // Jika ada status yang valid, kembalikan query yang sudah difilter.
                    return $query->whereIn('status', $statuses);
                }

                if (Auth::user()->hasRole('Kepala SPPG')) {
                    $query->where('sppg_id', User::find(Auth::user()->id)->sppgDikepalai->id);
                    $query->where('billed_to_type', 'sppg');
                }

                if (Auth::user()->hasRole('PJ Pelaksana')) {
                    $query->where('sppg_id', User::find(Auth::user()->id)->unitTugas->first()->id);
                    $query->where('billed_to_type', 'sppg');
                }

                // dd(User::find(Auth::user()->id)->lembagaDipimpin);
                if (Auth::user()->hasRole('Pimpinan Lembaga Pengusul')) {
                    $query->whereIn('sppg_id', User::find(Auth::user()->id)->lembagaDipimpin->sppgs->pluck('id')->toArray());
                    $query->where('billed_to_type', 'pengusul');
                }

                // dump($query->toSql());

                // 3. Jika tidak ada status yang valid, kembalikan query dasar (tidak terfilter).
                return $query;
            })
            ->columns([
                Stack::make([
                    // Menggunakan struktur column dari code terbaru user
                    TextColumn::make('period_range')
                        ->label('Periode')
                        // SAFE ACCESS: Memastikan $record tidak null sebelum diakses
                        ->state(fn (?Bill $record): ?string => $record ? "Periode {$record->period_start} s.d {$record->period_end}" : null)
                        ->icon('heroicon-m-calendar'),

                    TextColumn::make('amount')
                        ->label('Nominal')
                        ->money('idr', true)
                        ->icon('heroicon-m-banknotes'),

                    TextColumn::make('status')
                        ->badge()
                        ->formatStateUsing(fn (string $state): string => match ($state) {
                            'unpaid' => 'Menunggu Pembayaran',
                            'verification' => 'Menunggu Verifikasi',
                            'paid' => 'Pembayaran Berhasil',
                            'rejected' => 'Pembayaran Ditolak',
                            default => $state,
                        })
                        ->color(fn (string $state): string => match ($state) {
                            'unpaid' => 'warning',
                            'verification' => 'info',
                            'paid' => 'success',
                            'rejected' => 'danger',
                            default => 'gray',
                        })
                        ->icon(fn (string $state): string => match ($state) {
                            'unpaid' => 'heroicon-m-credit-card',
                            'verification' => 'heroicon-m-arrow-path',
                            'paid' => 'heroicon-m-check',
                            'rejected' => 'heroicon-m-x-mark',
                            default => null,
                        }),

                    // Rejection reason column, visible only when status is 'rejected'
                    TextColumn::make('rejection_reason')
                        ->label('Alasan Penolakan')
                        ->visible(fn (?Bill $record): bool => $record && $record->status === 'rejected')
                        ->color('danger')
                        ->prefix('Alasan: ')
                        ->wrap()
                        ->size('sm')
                        ->state(fn (?Bill $record): ?string => $record?->remittance?->rejection_reason),
                ]),
            ])
            ->filters([
                // Menggunakan Filter generik dengan CheckboxList untuk filter status
                Filter::make('status_filter') // Gunakan nama filter yang unik
                    ->label('Status Pembayaran')
                    ->schema([
                        CheckboxList::make('statuses')
                            ->options([
                                'unpaid' => 'Menunggu Pembayaran',
                                'verification' => 'Menunggu Verifikasi',
                                'paid' => 'Pembayaran Berhasil',
                                'rejected' => 'Pembayaran Ditolak',
                            ])
                            ->columns(2), // Opsional: Tampilkan dalam 2 kolom
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (empty($data['statuses'])) {
                            return $query;
                        }

                        // Memfilter berdasarkan status yang dipilih
                        return $query->whereIn('status', $data['statuses']);
                    }),
            ])

            ->recordActions([
                Action::make('create_remittance')
                    ->label(fn (Bill $record): string => $record->status === 'verification' ? 'Detail Transaksi' : 'Buat Pembayaran')
                    ->button()
                    ->color('success')
                    ->icon(fn (Bill $record): string => $record->status === 'verification' ? 'heroicon-m-eye' : 'heroicon-m-banknotes')
                    // Visible jika unpaid ATAU verification
                    ->visible(fn (Bill $record): bool => in_array($record->status, ['unpaid', 'verification', 'rejected']))

                    // Menggabungkan Infolist dan Form Schema menjadi satu array.
                    ->schema(fn (Bill $record): array => array_merge(
                        $this->getInfolistSchema($record), // Infolist (Detail Invoice/Remittance)
                        $this->getRemittanceFormSchema($record) // Form Fields
                    ))
                    // ->column(3)
                    // === END: INFOLIST (Invoice View) ===
                    ->action(function (Bill $record, array $data) {
                        try {
                            DB::beginTransaction();

                            $user = Auth::user();

                            $sppg = null;

                            if ($user->hasRole('Kepala SPPG')) {
                                $sppg = User::find($user->id)->sppgDikepalai;
                                if ($sppg->balance < $record->amount) {
                                    Notification::make()
                                        ->title('Gagal Mencatat Pembayaran')
                                        ->body('Saldo SPPG Anda tidak mencukupi untuk melakukan pembayaran tagihan ini.')
                                        ->danger()
                                        ->send();

                                    return;
                                }
                            }
                            if ($user->hasRole('PJ Pelaksana')) {
                                $sppg = User::find($user->id)->unitTugas->first();
                                if ($sppg->balance < $record->amount) {
                                    Notification::make()
                                        ->title('Gagal Mencatat Pembayaran')
                                        ->body('Saldo SPPG Anda tidak mencukupi untuk melakukan pembayaran tagihan ini.')
                                        ->danger()
                                        ->send();

                                    return;
                                }
                            }

                            // 1. Buat Record Remittance: Menyimpan semua data dari form dan mengaitkannya dengan Bill dan User.
                            Remittance::create([
                                'bill_id' => $record->id,
                                'user_id' => Auth::id(), // Mendapatkan ID pengguna yang saat ini login
                                // 'remittance_date' => now(), // Tanggal pembuatan record remittance
                                'amount_sent' => $record->amount,
                                'status' => 'pending', // Status default saat pertama kali dibuat
                                'proof_file_path' => $data['proof_file_path'], // Path file yang tersimpan (sudah diunggah ke local)
                                'source_bank_name' => $data['source_bank_name'],
                                'destination_bank_name' => $data['destination_bank_name'],
                                'transfer_date' => $data['transfer_date'],
                                // 'rejection_reason' dibiarkan null
                            ]);

                            // 2. Update Status Bill: Mengubah status tagihan menjadi 'verification'
                            $record->update(['status' => 'verification']);

                            DB::commit();

                            Notification::make()
                                ->title('Pembayaran Berhasil Dicatat')
                                ->body('Bukti transfer Anda telah berhasil diunggah. Tagihan **'.$record->invoice_number.'** sedang menunggu verifikasi.')
                                ->success()
                                ->send();
                        } catch (Exception $e) {
                            DB::rollBack();
                            Notification::make()
                                ->title('Gagal Mencatat Pembayaran')
                                ->body('Terjadi kesalahan saat menyimpan data: '.$e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }

    protected function getInfolistSchema(Bill $record): array
    {
        // Cek apakah tagihan sudah diverifikasi/menunggu verifikasi
        $isVerification = $record->status === 'verification';

        // Cari remittance yang statusnya "verification" (diasumsikan ini status pending)
        // Catatan: Pastikan relasi 'remittances' ada di model Bill
        $remittance = $isVerification ? $record->remittances()->where('status', 'pending')->latest()->first() : null;

        // Jika status verification dan remittance ditemukan, tampilkan detail Remittance
        if ($isVerification && $remittance) {
            return [
                Section::make('Detail Transaksi Pembayaran')
                    ->heading('Bukti Pembayaran (Menunggu Verifikasi)')
                    ->description('Data pembayaran yang telah Anda kirimkan untuk tagihan ini. Menunggu verifikasi dari administrator.')
                    ->icon('heroicon-m-clock')
                    ->schema([
                        TextEntry::make('source_bank_name')
                            ->label('Sumber Dana')
                            ->state($remittance->source_bank_name),
                        TextEntry::make('destination_bank_name')
                            ->label('Bank Tujuan')
                            ->state($remittance->destination_bank_name),
                        TextEntry::make('transfer_date')
                            ->label('Tanggal Transfer')
                            ->date('d M Y')
                            ->state($remittance->transfer_date),
                        TextEntry::make('amount')
                            ->label('Jumlah Dibayar')
                            ->money('idr', true)
                            ->size('lg')
                            ->color('success')
                            ->columnSpanFull()
                            ->state($remittance->amount_sent),

                        // Perhatian: 'proof_file_path' harus diakses dari objek Remittance
                        ImageEntry::make('proof_file_path')
                            ->label('Bukti Transfer')
                            ->disk('local') // Pastikan ini sama dengan disk FileUpload
                            ->columnSpanFull()
                            ->imageHeight('300px')
                            ->imageWidth('100%')
                            ->state($remittance->proof_file_path),

                        Action::make('view_full_image')
                            ->label('Lihat Gambar Penuh')
                            ->icon('heroicon-m-magnifying-glass-plus')
                            ->color('gray')
                            ->modalWidth('7xl') // Ukuran modal sangat besar (7xl)
                            ->modalHeading('Bukti Transfer - Tampilan Penuh')
                            ->modalSubmitAction(false) // Hilangkan tombol submit
                            ->modalCancelAction(false) // Hilangkan tombol cancel
                            // Render gambar menggunakan Base64 agar aman (tanpa public URL)
                            ->modalContent(function () use ($remittance) {
                                $path = $remittance->proof_file_path;

                                if (! $path || ! Storage::disk('local')->exists($path)) {
                                    return new HtmlString('<div style="padding: 1rem; text-align: center; color: #ef4444;">File bukti transfer tidak ditemukan pada server. Path: '.$path.'</div>');
                                }

                                // Baca file dan konversi ke base64
                                $fileContent = Storage::disk('local')->get($path);
                                $mimeType = Storage::disk('local')->mimeType($path);
                                $base64 = base64_encode($fileContent);
                                $src = "data:{$mimeType};base64,{$base64}";

                                return new HtmlString('
                                        <div style="display: flex; justify-content: center; align-items: center; border-radius: 0.5rem; padding: 0.5rem;">
                                            <img src="'.$src.'" alt="Bukti Transfer Full" style="max-width: 100%; max-height: 85vh; object-fit: contain; border-radius: 8px;">
                                        </div>
                                    ');
                            }),

                    ])
                    ->columns(3),
                // Mengarahkan state Infolist ke objek Remittance
                // ->state($remittance)
            ];
        }

        // Default: Jika unpaid, tampilkan Detail Tagihan (Invoice View)
        return [
            Section::make('Detail Tagihan')
                ->heading('Faktur Pembayaran')
                ->description('Harap verifikasi detail tagihan di bawah sebelum melanjutkan ke pembayaran.')
                ->icon('heroicon-m-document-text')
                ->columns(3)
                ->schema([
                    TextEntry::make('invoice_number')
                        ->label('Nomor Invoice')
                        ->weight('bold')
                        ->copyable(),
                    TextEntry::make('period_range')
                        ->label('Periode Tagihan')
                        ->state(fn (Bill $record): string => "{$record->period_start} s.d {$record->period_end}"),
                    TextEntry::make('status')
                        ->label('Status Saat Ini')
                        ->badge()
                        ->color('warning')
                        ->formatStateUsing(fn (string $state): string => match ($state) {
                            'unpaid' => 'Menunggu Pembayaran',
                            default => $state,
                        }),
                    TextEntry::make('amount')
                        ->label('Total Pembayaran')
                        ->money('idr', true)
                        ->size('lg')
                        ->color('primary')
                        ->columnSpanFull(),
                ]),
        ];
    }

    protected function getRemittanceFormSchema(Bill $record): array
    {
        // Form hanya diperlukan saat status unpaid
        $isAvailable = $record->status === 'unpaid';

        return [
            Section::make('bank_transfer_section')
                ->heading('Informasi Transfer Bank')
                ->description('Silakan isi detail transfer bank Anda di bawah ini untuk memproses pembayaran tagihan.')
                ->schema([
                    TextInput::make('source_bank_name')
                        ->label('Sumber Dana')
                        ->required($isAvailable)
                        ->visible($isAvailable) // Sembunyikan jika tidak unpaid
                        ->columnSpan(1),

                    TextInput::make('destination_bank_name')
                        ->label('Nama Bank Tujuan')
                        ->required($isAvailable)
                        ->visible($isAvailable)
                        ->columnSpan(1),
                ])
                ->visible($isAvailable)
                ->columns(2),

            DatePicker::make('transfer_date')
                ->label('Tanggal Transfer')
                ->default(now())
                ->required($isAvailable)
                ->visible($isAvailable)
                ->maxDate(now())
                ->columnSpan(1),

            FileUpload::make('proof_file_path') // Menggunakan nama kolom model
                ->label('Bukti Transfer')
                ->required($isAvailable)
                ->visible($isAvailable) // Sembunyikan jika tidak unpaid
                ->image()
                ->disk('local')
                ->directory('remittances/proofs')
                ->visibility('private')
                ->maxSize(5120),
        ];
    }
}
