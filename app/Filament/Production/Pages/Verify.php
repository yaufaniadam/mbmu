<?php

namespace App\Filament\Production\Pages;

use App\Models\FoodVerification;
use App\Models\ProductionSchedule;
use App\Models\ProductionVerificationSetting;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Facades\Auth;

class Verify extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.production.pages.verify';

    protected static ?string $navigationLabel = 'Verifikasi Gizi';

    protected ?string $heading = '';

    public ?array $data = [];

    public ?ProductionSchedule $record = null;

    protected bool $isEditable = true;

    protected ?FoodVerification $verificationNote = null;

    public function getLayout(): string
    {
        return 'layouts.mobile-navigation';
    }

    public function getFormStatePath(): string
    {
        return 'data';
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-check-badge';
    }

    public function mount(): void
    {
        $user = Auth::user();
        $organizationId = $user->unitTugas()->first()->id;

        $this->record = ProductionSchedule::where('sppg_id', $organizationId)
            ->latest()
            ->first();

        if (! $this->record) {
            Notification::make()
                ->title('Data tidak ditemukan.')
                ->danger()
                ->send();

            return;
        }

        $this->isEditable = $this->record->status === 'Direncanakan';
        $this->verificationNote = FoodVerification::where('jadwal_produksi_id', $this->record->id)->latest()->first();

        // --- LOGIKA DAFTAR PERIKSA BARU DIMULAI ---

        // 1. Dapatkan templat daftar periksa dari Pengaturan
        $setting = ProductionVerificationSetting::firstWhere('sppg_id', $this->record->sppg_id);
        $templateItems = $setting?->checklist_data ?? [];

        // 2. Dapatkan data daftar periksa yang tersimpan dari verifikasi ini
        $savedItems = $this->verificationNote?->checklist_data ?? [];

        // 3. Gabungkan keduanya (LOGIKA DIPERBARUI)
        $finalChecklistData = [];
        foreach ($templateItems as $templateItem) {
            $itemName = $templateItem['item_name'];
            $savedItem = collect($savedItems)->firstWhere('item_name', $itemName);

            // --- PERBAIKAN DI SINI ---
            // Kita ubah menjadi BOOELAN untuk Toggle, bukan string.
            $isChecked = false; // Default baru adalah boolean false
            if ($savedItem) {
                if (isset($savedItem['checked'])) {
                    // Konversi string "true" yang disimpan menjadi boolean true
                    $isChecked = $savedItem['checked'] === 'true';
                } elseif (isset($savedItem['is_verified'])) {
                    // Konversi data boolean lama
                    $isChecked = (bool) $savedItem['is_verified'];
                }
            }

            $finalChecklistData[] = [
                'item_name' => $itemName,
                'checked' => $isChecked, // <-- Key 'checked' sekarang berisi boolean
                'catatan_item' => $savedItem['catatan_item'] ?? null,
            ];
        }
        // --- LOGIKA DAFTAR PERIKSA BARU BERAKHIR ---

        $this->form->fill([
            'status' => $this->record->status,
            'catatan' => $this->verificationNote?->catatan,
            'checklist_data' => $finalChecklistData,
        ]);
    }

    public function getFormSchema(): array
    {
        return [
            // Radio::make('status')
            //     ->label('Status Verifikasi')
            //     ->options([
            //         'Ditolak' => 'Ditolak',
            //         'Terverifikasi' => 'Terverifikasi',
            //     ])
            //     ->inline()
            //     ->live()
            //     ->required()
            //     // disabled depends on the record's current status
            //     ->disabled(fn () => $this->record && $this->record->status !== 'Direncanakan')
            //     ->afterStateUpdated(function ($state, Set $set) {
            //         if ($state !== 'Ditolak') {
            //             $set('catatan', null);
            //         }
            //     }),

            Textarea::make('catatan')
                ->label('Catatan (wajib jika ditolak)')
                ->columnSpanFull()
                ->visible(fn (Get $get) => $get('status') === 'Ditolak')
                ->required(fn (Get $get) => $get('status') === 'Ditolak')
                ->disabled(fn () => $this->record && $this->record->status !== 'Direncanakan'),

            // --- 8. REPEATER DAFTAR PERIKSA BARU ---
            Section::make('Daftar Periksa Verifikasi')
                ->schema([
                    Repeater::make('checklist_data')
                        ->label('Item Verifikasi')
                        ->schema([
                            // ... (schema internal Repeater tidak berubah)
                            Hidden::make('item_name'),

                            Checkbox::make('checked')
                                ->label(fn (Get $get): string => $get('item_name') ?? 'Item')
                                ->disabled(! $this->isEditable)
                                ->dehydrateStateUsing(fn ($state): string => $state ? 'true' : 'false'),

                            Textarea::make('catatan_item')
                                ->label('Catatan Item')
                                ->rows(2)
                                ->columnSpanFull()
                                ->disabled(! $this->isEditable),
                        ])
                        // ... (addable, deletable, dll. tidak berubah)
                        ->addable(false)
                        ->deletable(false)
                        ->reorderable(false)
                        ->columnSpanFull()
                        ->grid(1)
                        ->compact(),
                    // --- AKHIR TAMBAHAN ---

                ])
                ->hidden(fn () => ! $this->record),
        ];
    }

    public function save(): void
    {
        if (! $this->isEditable || ! $this->record) {
            Notification::make()
                ->title('Data tidak dapat diedit.')
                ->warning()
                ->send();

            return;
        }

        $data = $this->form->getState();
        $user = Auth::user();

        // --- TAMBAHAN BARU DI SINI ---
        // Kita akan menyusun ulang array secara manual di sini
        $checklistData = $data['checklist_data'] ?? [];
        $reorderedChecklistData = collect($checklistData)->map(fn ($item) => [
            'item_name' => $item['item_name'] ?? null,
            'checked' => $item['checked'] ?? 'false',
            'catatan_item' => $item['catatan_item'] ?? null,
        ])->all();
        // --- AKHIR TAMBAHAN ---

        $this->record->update([
            'status' => 'Menunggu ACC Kepala SPPG',
        ]);

        FoodVerification::updateOrCreate(
            ['jadwal_produksi_id' => $this->record->id],
            [
                'user_id' => $user->id,
                'catatan' => $data['catatan'] ?? null,
                'checklist_data' => $reorderedChecklistData, // <-- Menggunakan variabel yang baru
            ]
        );

        Notification::make()
            ->title('Data berhasil diperbarui!')
            ->success()
            ->send();

        // ... (sisa metode save tidak berubah)
        $this->record->refresh();
        $this->verificationNote = FoodVerification::where('jadwal_produksi_id', $this->record->id)->latest()->first();
        $thisId = $this->isEditable = $this->record->status === 'Direncanakan';

        // Panggil mount() lagi untuk memuat ulang data
        $this->mount();
    }
}
