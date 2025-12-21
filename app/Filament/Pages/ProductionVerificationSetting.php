<?php

namespace App\Filament\Pages;

use App\Models\ProductionVerificationSetting as ModelsProductionVerificationSetting;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use UnitEnum;

class ProductionVerificationSetting extends Page implements HasActions, HasForms
{
    use InteractsWithActions, InteractsWithForms;

    protected string $view = 'filament.pages.production-verification-setting';

    protected static string|UnitEnum|null $navigationGroup = 'Sistem';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Ketentuan Verifikasi';

    protected ?string $heading = 'Ketentuan Verifikasi Produksi';

    public ?array $data = [];

    public ModelsProductionVerificationSetting $productionVerificationSetting;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-clipboard-document-list';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return \Filament\Facades\Filament::getCurrentPanel()?->getId() === 'admin' && 
               auth()->user()->can('View:ProductionVerificationSetting');
    }

    public function getFormStatePath(): string
    {
        return 'data';
    }

    public function mount(): void
    {
        // STRICT PANEL CHECK: Only allow access from the 'admin' panel.
        if (\Filament\Facades\Filament::getCurrentPanel()?->getId() !== 'admin') {
            abort(403, 'Akses ditolak. Pengaturan ini hanya dapat diakses melalui panel Admin Pusat.');
        }

        Gate::authorize('View:ProductionVerificationSetting');

        $user = Auth::user();

        // STRICT ROLE CHECK: Only National Roles can access this Settings Page
        if (! $user->hasAnyRole(['Superadmin', 'Direktur Kornas', 'Staf Kornas', 'Staf Akuntan Kornas'])) {
             abort(403, 'Halaman ini khusus untuk pengaturan kriteria verifikasi oleh tingkat Pusat (Kornas).');
        }

        // Load Global Settings
        $globalSetting = ModelsProductionVerificationSetting::first();
        
        // If not exists, maybe create one or just empty
        $this->productionVerificationSetting = $globalSetting ?? new ModelsProductionVerificationSetting();
        
        $this->form->fill([
            'checklist_data' => $globalSetting?->checklist_data ?? []
        ]);
    }

    public function getFormSchema(): array
    {
        return [
            Repeater::make('checklist_data')
                ->label('Checklist yang perlu diverifikasi saat produksi')
                ->schema([
                    TextInput::make('item_name')
                        ->label('Ketentuan')
                        ->required(),
                ])
                ->columns(1)
                // ->helperText('Add all data points that need to be verified.')
                ->defaultItems(1) // Start with one item if the list is empty
                ->addActionLabel('Tambah Checklist')
                ->compact()
                ->grid(1)
                ->columnSpanFull(),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Settings')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        try {
            // 1. Get the user's SPPG ID
            // $user = Auth::user();

            // $sppgId = null;

            // if ($user->hasRole('Kepala SPPG')) {
            //     $sppgId = User::find($user->id)->sppgDikepalai->id;
            // } else {
            //     $sppgId = User::find($user->id)->unitTugas->first()->id;
            // }

            // if (blank($sppgId)) {
            //     Notification::make()
            //         ->title('Error')
            //         ->body('Your user account is not associated with an SPPG.')
            //         ->danger()
            //         ->send();

            //     return;
            // }

            $verificationSetting = ModelsProductionVerificationSetting::first();
            $data = $this->form->getState();

            ModelsProductionVerificationSetting::updateOrCreate(
                ['id' => $verificationSetting?->id],
                ['checklist_data' => $data['checklist_data'] ?? []]
            );

            Notification::make()
                ->title('Settings Saved')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error saving settings')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
