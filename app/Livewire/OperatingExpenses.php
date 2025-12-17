<?php

namespace App\Livewire;

use App\Models\OperatingExpense;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;

class OperatingExpenses extends TableWidget
{
    protected static ?string $heading = 'Biaya Operasional';

    public function table(Table $table): Table
    {
        return $table
            ->query(function (): Builder {
                $query = OperatingExpense::query();
                $user = Auth::user();

                if ($user->hasRole('Kepala SPPG')) {
                    return $query->where('sppg_id', $user->sppgDikepalai?->id);
                }
                if ($user->hasRole('PJ Pelaksana')) {
                    return $query->where('sppg_id', $user->unitTugas->first()?->id);
                }
                if ($user->hasAnyRole(['Superadmin', 'Staf Kornas', 'Direktur Kornas'])) {
                    return $query->whereNull('sppg_id');
                }

                return $query->whereRaw('1 = 0');
            })
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Pengeluaran')
                    ->searchable(),
                TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('IDR', locale: 'id')
                    ->sortable(),
                TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('category')
                    ->label('Kategori')
                    ->badge(),
            ])
            ->recordActions([
                // 1. View Image Action (Only visible if image)
                Action::make('view_attachment')
                    ->label('Lihat Bukti')
                    ->icon('heroicon-m-eye')
                    ->color('info')
                    ->visible(fn (OperatingExpense $record) => $this->isImage($record->attachment))
                    ->modalHeading('Bukti Lampiran')
                    ->modalSubmitAction(false) // Hide buttons
                    ->modalCancelAction(false)
                    ->modalContent(function (OperatingExpense $record) {
                        // Render Base64 image because file is 'private' and cannot be accessed via URL
                        if (! Storage::disk('local')->exists($record->attachment)) {
                            return new HtmlString('<p class="text-danger-500">File tidak ditemukan.</p>');
                        }

                        $content = Storage::disk('local')->get($record->attachment);
                        $mime = Storage::disk('local')->mimeType($record->attachment);
                        $base64 = base64_encode($content);

                        return new HtmlString(
                            '<div class="flex justify-center"><img src="data:'.$mime.';base64,'.$base64.'" style="max-width: 100%; max-height: 80vh; border-radius: 8px;" /></div>'
                        );
                    }),

                // 2. Download File Action (Only visible if NOT image)
                Action::make('download_attachment')
                    ->label('Unduh Bukti')
                    ->icon('heroicon-m-arrow-down-tray')
                    ->color('gray')
                    ->visible(fn (OperatingExpense $record) => $record->attachment && ! $this->isImage($record->attachment))
                    ->action(fn (OperatingExpense $record) => Storage::disk('local')->download($record->attachment)),

                // 3. Edit Action
                EditAction::make()
                    ->modalHeading('Ubah Data Pengeluaran')
                    ->schema($this->getFormSchema())
                    // Logic: If user uploads a NEW file ($data['attachment']), delete the OLD file ($record->attachment)
                    ->before(function (OperatingExpense $record, array $data) {
                        $newFile = $data['attachment'] ?? null;
                        $oldFile = $record->attachment;

                        if ($newFile !== $oldFile && $oldFile) {
                            Storage::disk('local')->delete($oldFile);
                        }
                    }),

                // 4. Delete Action
                DeleteAction::make()
                    // Logic: Delete file from storage after record is deleted
                    ->after(fn (OperatingExpense $record) => $record->attachment ? Storage::disk('local')->delete($record->attachment) : null),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah Pengeluaran')
                    ->modalHeading('Catat Biaya Operasional Baru')
                    ->mutateFormDataUsing(function (array $data): array {
                        $user = Auth::user();
                        $sppgId = null;

                        if ($user->hasRole('Kepala SPPG')) {
                            $sppgId = $user->sppgDikepalai?->id;
                        } elseif ($user->hasRole('PJ Pelaksana')) {
                            $sppgId = $user->unitTugas->first()?->id;
                        }

                        if ($sppgId) {
                            $data['sppg_id'] = $sppgId;
                        }

                        return $data;
                    })
                    ->schema($this->getFormSchema()) // Reusing schema
                    ->successNotificationTitle('Biaya operasional berhasil dicatat'),
            ]);
    }

    /**
     * Shared form schema to avoid duplication between Create and Edit
     */
    protected function getFormSchema(): array
    {
        return [
            TextInput::make('name')
                ->label('Nama Pengeluaran')
                ->placeholder('Contoh: Pembelian ATK')
                ->required()
                ->maxLength(255),

            TextInput::make('category')
                ->label('Kategori')
                ->required()
                ->datalist([
                    'Transportasi',
                    'Konsumsi',
                    'Alat Tulis Kantor',
                    'Sewa Tempat',
                    'Lainnya',
                ]),

            TextInput::make('amount')
                ->label('Jumlah Biaya')
                ->prefix('Rp')
                ->numeric()
                ->required(),

            DatePicker::make('date')
                ->label('Tanggal Transaksi')
                ->default(now())
                ->required(),

            FileUpload::make('attachment')
                ->label('Bukti Lampiran (Nota/Struk)')
                ->image() // Validates image types for upload
                ->acceptedFileTypes(['image/*', 'application/pdf']) // Allow PDFs too if needed
                ->disk('local')
                ->directory('operating-expenses-proof')
                ->visibility('private')
                ->maxSize(5120)
                ->columnSpanFull(),
        ];
    }

    /**
     * Helper to check if a file path is an image
     */
    protected function isImage(?string $path): bool
    {
        if (! $path) {
            return false;
        }
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp']);
    }
}
