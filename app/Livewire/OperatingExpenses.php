<?php

namespace App\Livewire;

use App\Models\OperatingExpense;
use App\Models\OperatingExpenseCategory;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;

class OperatingExpenses extends TableWidget
{
    protected static ?string $heading = 'Biaya Operasional';

    public static function canView(): bool
    {
        return Auth::user()->hasAnyRole([
            'Kepala SPPG',
            'PJ Pelaksana',
            'Superadmin',
            'Staf Kornas',
            'Staf Akuntan Kornas',
            'Direktur Kornas',
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(function (): Builder {
                $query = OperatingExpense::query();
                $user = Auth::user();
                $panelId = \Filament\Facades\Filament::getCurrentPanel()->getId();

                if ($panelId === 'admin') {
                    // National roles in Admin Panel: See 'Central' expenses (sppg_id = null)
                    return $query->whereNull('sppg_id');
                }

                // Any role in SPPG panel: Scope to their assigned SPPG
                $sppgId = $user->hasRole('Kepala SPPG')
                    ? $user->sppgDikepalai?->id
                    : $user->unitTugas->first()?->id;

                if ($sppgId) {
                    return $query->where('sppg_id', $sppgId);
                }

                return $query->whereRaw('1 = 0');
            })
            ->columns([
                TextColumn::make('sppg.nama_sppg')
                    ->label('Unit SPPG')
                    ->badge()
                    ->color('gray')
                    ->searchable()
                    ->sortable()
                    ->visible(fn () => \Filament\Facades\Filament::getCurrentPanel()->getId() === 'admin'),
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
                TextColumn::make('categoryData.name')
                    ->label('Kategori')
                    ->badge(),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('sppg_id')
                    ->label('Filter per SPPG')
                    ->relationship('sppg', 'nama_sppg')
                    ->searchable()
                    ->preload()
                    ->visible(fn () => \Filament\Facades\Filament::getCurrentPanel()->getId() === 'admin'),
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

                // 3. AUDIT EDIT ACTION (Replaces Standard Edit)
                EditAction::make()
                    ->label('Revisi')
                    ->icon('heroicon-m-pencil-square')
                    ->color('warning')
                    ->visible(fn () => ! Auth::user()->hasAnyRole(['Superadmin', 'Direktur Kornas']))
                    ->modalHeading('Revisi Biaya Operasional')
                    ->modalDescription('PERHATIAN: Mengubah data ini akan membuat catatan baru dan mengarsipkan catatan lama sebagai histori (Audit Trail).')
                    ->schema($this->getFormSchema())
                    ->using(function (OperatingExpense $record, array $data): OperatingExpense {
                        return DB::transaction(function () use ($record, $data) {
                            // A. Prepare new data
                            $newData = $data;

                            // Inherit IDs from the original record
                            $newData['sppg_id'] = $record->sppg_id;

                            // Link to the old record
                            $newData['previous_version_id'] = $record->id;

                            // B. Create the NEW Record
                            // Note: We do NOT delete the old file attachment.
                            // Both records might point to the same file (if file wasn't changed),
                            // or the new record has a new file. The old file stays for audit.
                            $newRecord = OperatingExpense::create($newData);

                            // C. Soft Delete the OLD Record
                            $record->delete();

                            return $newRecord;
                        });
                    }),

                // 4. SOFT DELETE ACTION
                DeleteAction::make()
                    ->label('Arsipkan')
                    ->visible(fn () => ! Auth::user()->hasAnyRole(['Superadmin', 'Direktur Kornas']))
                    ->modalHeading('Arsipkan Pengeluaran Ini?')
                    ->modalDescription('Data akan dihapus dari daftar aktif, namun tetap tersimpan di database untuk keperluan audit.'),
                // IMPORTANT: Removed the 'after' hook that deleted the file.
                // We must keep the file evidence for archived records.
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah Pengeluaran')
                    ->visible(fn () => ! Auth::user()->hasAnyRole(['Superadmin', 'Direktur Kornas']))
                    ->modalHeading('Catat Biaya Operasional Baru')
                    ->mutateDataUsing(function (array $data): array {
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

            Select::make('category_id')
                ->label('Kategori')
                ->relationship('categoryData', 'name')
                ->required()
                ->searchable()
                ->preload()
                ->native(false)
                ->createOptionForm([
                    TextInput::make('name')
                        ->label('Nama Kategori Baru')
                        ->required()
                        ->maxLength(255),
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
