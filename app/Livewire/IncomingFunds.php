<?php

namespace App\Livewire;

use App\Models\SppgIncomingFund;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;

class IncomingFunds extends TableWidget
{
    public static function canView(): bool
    {
        return Auth::user()->hasAnyRole([
            'Kepala SPPG',
            'PJ Pelaksana',

        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(function (): Builder {
                $query = SppgIncomingFund::query();
                $user = Auth::user();

                if ($user->hasRole('Kepala SPPG')) {
                    return $query->where('sppg_id', $user->sppgDikepalai?->id);
                }
                if ($user->hasRole('PJ Pelaksana')) {
                    return $query->where('sppg_id', $user->unitTugas->first()?->id);
                }
                if ($user->hasAnyRole(['Superadmin', 'Staf Kornas', 'Direktur Kornas'])) {
                    // Assuming Admins see 'Central' funds (sppg_id = null)
                    return $query->whereNull('sppg_id');
                }

                return $query->whereRaw('1 = 0');
            })
            ->columns([
                TextColumn::make('source')
                    ->label('Sumber Dana')
                    ->searchable(),
                TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('IDR', locale: 'id')
                    ->sortable(),
                TextColumn::make('received_at')
                    ->label('Tanggal Diterima')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Pencatat')
                    ->sortable(),
            ])
            ->recordActions([
                // 1. View Image Action
                Action::make('view_attachment')
                    ->label('Lihat Bukti')
                    ->icon('heroicon-m-eye')
                    ->color('info')
                    ->visible(fn (SppgIncomingFund $record) => $this->isImage($record->attachment))
                    ->modalHeading('Bukti Lampiran')
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false)
                    ->modalContent(function (SppgIncomingFund $record) {
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

                // 2. Download File Action
                Action::make('download_attachment')
                    ->label('Unduh Bukti')
                    ->icon('heroicon-m-arrow-down-tray')
                    ->color('gray')
                    ->visible(fn (SppgIncomingFund $record) => $record->attachment && ! $this->isImage($record->attachment))
                    ->action(fn (SppgIncomingFund $record) => Storage::disk('local')->download($record->attachment)),

                // 3. AUDIT EDIT ACTION (Replaces Standard Edit)
                EditAction::make()
                    ->label('Revisi')
                    ->icon('heroicon-m-pencil-square')
                    ->color('warning')
                    ->modalHeading('Revisi Data Dana Masuk')
                    ->modalDescription('PERHATIAN: Mengubah data ini akan membuat catatan baru dan mengarsipkan catatan lama sebagai histori (Audit Trail).')
                    ->schema($this->getFormSchema())
                    // IMPORTANT: We override the standard save behavior here
                    ->using(function (SppgIncomingFund $record, array $data): SppgIncomingFund {
                        return DB::transaction(function () use ($record, $data) {
                            // 1. Prepare new data
                            $newData = $data;
                            $newData['sppg_id'] = $record->sppg_id;
                            $newData['user_id'] = Auth::id(); // The person doing the revision

                            // 2. CONNECT THE RECORDS
                            // Point the new record to the old record's ID
                            $newData['previous_version_id'] = $record->id;

                            // 3. Create NEW Record
                            $newRecord = SppgIncomingFund::create($newData);

                            // 4. Soft Delete OLD Record
                            $record->delete();

                            return $newRecord;
                        });
                    }),

                // 4. SOFT DELETE ACTION
                DeleteAction::make()
                    ->label('Arsipkan')
                    ->modalHeading('Arsipkan Data Ini?')
                    ->modalDescription('Data akan dihapus dari daftar aktif, namun tetap tersimpan di database untuk keperluan audit.'),
                // We REMOVED the 'after' hook that deleted the file.
                // File must remain on disk for soft-deleted records.
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Catat Dana Masuk')
                    ->modalHeading('Catat Penerimaan Dana Baru')
                    ->schema($this->getFormSchema())
                    ->using(function (array $data, string $model): SppgIncomingFund {
                        $user = Auth::user();
                        $sppgId = null;

                        $data['user_id'] = $user->id;

                        if ($user->hasRole('Kepala SPPG')) {
                            $sppgId = $user->sppgDikepalai?->id;
                        } elseif ($user->hasRole('PJ Pelaksana')) {
                            $sppgId = $user->unitTugas->first()?->id;
                        }

                        if ($sppgId) {
                            $data['sppg_id'] = $sppgId;
                        }

                        return $model::create($data);
                    })
                    ->successNotificationTitle('Dana masuk berhasil dicatat'),
            ]);
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('source')
                ->label('Sumber Dana')
                ->placeholder('Contoh: Donasi, Kas Pusat, dll')
                ->required()
                ->maxLength(255),

            TextInput::make('amount')
                ->label('Jumlah')
                ->prefix('Rp')
                ->numeric()
                ->required(),

            DatePicker::make('received_at')
                ->label('Tanggal Diterima')
                ->default(now())
                ->required(),

            Textarea::make('notes')
                ->label('Catatan Tambahan')
                ->rows(3)
                ->columnSpanFull(),

            FileUpload::make('attachment')
                ->label('Bukti Transfer / Dokumen')
                ->image() // Validates image types
                ->acceptedFileTypes(['image/*', 'application/pdf']) // Allow PDFs
                ->disk('local')
                ->directory('incoming-funds-proof')
                ->visibility('private')
                ->maxSize(5120)
                ->columnSpanFull(),
        ];
    }

    protected function isImage(?string $path): bool
    {
        if (! $path) {
            return false;
        }
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp']);
    }
}
