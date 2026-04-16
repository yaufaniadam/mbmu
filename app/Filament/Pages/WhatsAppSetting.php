<?php

namespace App\Filament\Pages;

use App\Models\SystemSetting;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Gate;

class WhatsAppSetting extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static string $view = 'filament.pages.whatsapp-setting';

    protected static ?string $navigationGroup = 'Pengaturan';

    protected static ?string $navigationLabel = 'WhatsApp Setting';

    protected static ?int $navigationSort = 2;

    public ?array $data = [];

    public function mount(): void
    {
        // Restriction to Admin/Staff Kornas
        $user = auth()->user();
        if (!$user->hasAnyRole(['Superadmin', 'Staf Kornas'])) {
             abort(403, 'Akses ditolak. Fitur ini hanya untuk Superadmin dan Staf Kornas.');
        }

        $template = SystemSetting::where('key', 'whatsapp_bulk_message')->first();

        $this->form->fill([
            'template' => $template?->value,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\Section::make('Template Pesan WhatsApp')
                    ->description('Gunakan template ini untuk mengirim pesan instruksi pengisian SPPG.')
                    ->schema([
                        Textarea::make('template')
                            ->label('Isi Pesan')
                            ->rows(15)
                            ->required()
                            ->helperText('Pesan ini akan dikirim secara manual melalui WhatsApp Web/App.'),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Perubahan')
                ->submit('save')
                ->color('primary'),
        ];
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();

            SystemSetting::updateOrCreate(
                ['key' => 'whatsapp_bulk_message'],
                ['value' => $data['template']]
            );

            Notification::make()
                ->title('Berhasil!')
                ->body('Template WhatsApp telah diperbarui.')
                ->success()
                ->send();
                
        } catch (\Exception $e) {
            Notification::make()
                ->title('Gagal menyimpan!')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
