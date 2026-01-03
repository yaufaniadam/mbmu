<?php

namespace App\Filament\Production\Pages;

use App\Models\ProductionVerification;
use App\Models\ProductionSchedule;
use App\Models\ProductionVerificationSetting;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class Verify extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.production.pages.verify';

    protected static ?string $navigationLabel = 'Verifikasi Gizi';

    protected ?string $heading = '';

    public ?array $data = [];

    public ?ProductionSchedule $record = null;

    protected bool $isEditable = true;

    protected ?ProductionVerification $verificationNote = null;

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

    public static function shouldRegisterNavigation(): bool
    {
        // This checks the permission you just generated
        return auth()->user()->can('View:Verify');
    }

    public function mount(): void
    {
        Gate::authorize('View:Verify');

        $user = Auth::user();
        $organizationId = $user->unitTugas()->first()?->id;

        if (!$organizationId) {
             abort(403, 'Anda belum terdaftar di unit tugas manapun.');
        }

        $this->record = ProductionSchedule::where('sppg_id', $organizationId)
            ->latest()
            ->first();

        if (! $this->record) {
            Notification::make()
                ->title('Aktifitas tidak ditemukan.')
                ->danger()
                ->send();

            return;
        }

        $this->isEditable = $this->record->status === 'Direncanakan';
        $this->verificationNote = $this->record->verification;

        // 1. Get template
        $setting = ProductionVerificationSetting::first();
        $templateItems = $setting?->checklist_data ?? [];

        // 2. Get saved results
        $savedResults = $this->verificationNote?->checklist_results ?? [];

        // 3. Merge
        $finalChecklistData = [];
        foreach ($templateItems as $templateItem) {
            $itemName = $templateItem['item_name'];
            $savedItem = collect($savedResults)->firstWhere('item', $itemName);

            // In our new simplified mobile UI for field staff, we use discrete status
            // mapping from boolean toggle to 'Sesuai' / 'Tidak Sesuai' if needed
            // But let's keep it simple: Sesuai (checked) or Tidak Sesuai (unchecked)
            $status = $savedItem['status'] ?? 'Tidak Sesuai';
            
            $finalChecklistData[] = [
                'item_name' => $itemName,
                'checked' => $status === 'Sesuai', 
                'catatan_item' => $savedItem['keterangan'] ?? null,
            ];
        }

        $this->form->fill([
            'notes' => $this->verificationNote?->notes,
            'checklist_data' => $finalChecklistData,
        ]);
    }

    public function getFormSchema(): array
    {
        return [
            Section::make('Daftar Periksa Verifikasi')
                ->schema([
                    Repeater::make('checklist_data')
                        ->label('Item Verifikasi')
                        ->schema([
                            Hidden::make('item_name'),

                            Checkbox::make('checked')
                                ->label(fn(Get $get): string => $get('item_name') ?? 'Item')
                                ->disabled(! $this->isEditable),

                            Textarea::make('catatan_item')
                                ->label('Catatan Item')
                                ->rows(2)
                                ->columnSpanFull()
                                ->disabled(! $this->isEditable),
                        ])
                        ->addable(false)
                        ->deletable(false)
                        ->reorderable(false)
                        ->columnSpanFull()
                        ->grid(1)
                        ->compact(),
                ])
                ->hidden(fn() => ! $this->record),

            Textarea::make('notes')
                ->label('Catatan Evaluasi Keseluruhan')
                ->placeholder('Misal: Ok semua, atau ada catatan tertentu...')
                ->columnSpanFull()
                ->disabled(! $this->isEditable),
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

        // Map to ProductionVerification format
        $checklistData = $data['checklist_data'] ?? [];
        $formattedResults = [];
        foreach ($checklistData as $item) {
            $formattedResults[] = [
                'item' => $item['item_name'],
                'status' => $item['checked'] ? 'Sesuai' : 'Tidak Sesuai',
                'keterangan' => $item['catatan_item'] ?? null,
            ];
        }

        ProductionVerification::updateOrCreate(
            ['production_schedule_id' => $this->record->id],
            [
                'sppg_id' => $this->record->sppg_id,
                'user_id' => $user->id,
                'date' => now(),
                'checklist_results' => $formattedResults,
                'notes' => $data['notes'] ?? null,
            ]
        );

        $this->record->update([
            'status' => 'Menunggu ACC Kepala SPPG',
        ]);

        Notification::make()
            ->title('Evaluasi Mandiri Berhasil Disimpan!')
            ->success()
            ->send();

        $this->record->refresh();
        $this->mount();
    }
}
