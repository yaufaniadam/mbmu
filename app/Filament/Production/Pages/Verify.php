<?php

namespace App\Filament\Production\Pages;

use App\Models\ProductionSchedule;
use App\Models\FoodVerification;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
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

        $this->record = ProductionSchedule::where('sppg_id', $organizationId)->latest()->first();

        if (! $this->record) {
            Notification::make()
                ->title('Data tidak ditemukan.')
                ->danger()
                ->send();
            return;
        }

        // determine editability
        $this->isEditable = $this->record->status === 'Direncanakan';

        // load previous verification note if exists
        $this->verificationNote = FoodVerification::where('jadwal_produksi_id', $this->record->id)->latest()->first();

        $this->form->fill([
            'status' => $this->record->status,
            'catatan' => $this->verificationNote?->catatan,
        ]);
    }

    public function getFormSchema(): array
    {
        return [
            Radio::make('status')
                ->label('Status Verifikasi')
                ->options([
                    'Ditolak' => 'Ditolak',
                    'Terverifikasi' => 'Terverifikasi',
                ])
                ->inline()
                ->live()
                ->required()
                // disabled depends on the record's current status
                ->disabled(fn() => $this->record && $this->record->status !== 'Direncanakan')
                ->afterStateUpdated(function ($state, Set $set) {
                    if ($state !== 'Ditolak') {
                        $set('catatan', null);
                    }
                }),

            Textarea::make('catatan')
                ->label('Catatan (wajib jika ditolak)')
                ->columnSpanFull()
                ->visible(fn(Get $get) => $get('status') === 'Ditolak')
                ->required(fn(Get $get) => $get('status') === 'Ditolak')
                ->disabled(fn() => $this->record && $this->record->status !== 'Direncanakan'),
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

        // update production schedule status
        $this->record->update([
            'status' => $data['status'],
        ]);

        // create/update verification note
        if ($data['status'] === 'Ditolak' && ! empty($data['catatan'])) {
            FoodVerification::updateOrCreate(
                ['jadwal_produksi_id' => $this->record->id],
                [
                    'user_id' => $user->id,
                    'catatan' => $data['catatan'],
                ]
            );
        }

        Notification::make()
            ->title('Data berhasil diperbarui!')
            ->success()
            ->send();

        // **Important**: refresh model and refill form so disabled states re-evaluate immediately
        $this->record->refresh();
        $this->isEditable = $this->record->status === 'Direncanakan';

        $this->form->fill([
            'status' => $this->record->status,
            'catatan' => $this->record->status === 'Ditolak'
                ? FoodVerification::where('jadwal_produksi_id', $this->record->id)->latest()->value('catatan')
                : null,
        ]);
    }
}
