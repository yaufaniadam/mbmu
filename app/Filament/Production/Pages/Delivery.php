<?php

namespace App\Filament\Production\Pages;

use App\Models\Distribution;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Panel;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;

class Delivery extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.production.pages.delivery';

    protected static bool $shouldRegisterNavigation = false;

    protected ?string $heading = '';

    public ?Distribution $record;

    public bool $isEditable = false;
    public ?string $photo_of_proof = null;
    public ?string $notes = null;

    public function getLayout(): string
    {
        return 'layouts.mobile-navigation';
    }

    public static function routes(Panel $panel): void
    {
        Route::get('/delivery/{distribution}', static::class)
            ->name('delivery');
    }

    // public static function shouldRegisterNavigation(): bool
    // {
    //     // This checks the permission you just generated
    //     return auth()->user()->can('View:Delivery');
    // }

    public function mount(Distribution $distribution): void
    {
        Gate::authorize('View:Delivery');

        $this->record = $distribution;
        // dd($distribution->productionSchedule->menu_hari_ini);
        $this->form->fill([
            'menu_hari_ini' => $distribution->productionSchedule->menu_hari_ini,
            'school.nama_sekolah' => $distribution->school->nama_sekolah,
            'school.alamat' => $distribution->school->alamat,
            'jumlah_porsi_besar' => $distribution->jumlah_porsi_besar,
            'jumlah_porsi_kecil' => $distribution->jumlah_porsi_kecil,
            'courier.name' => $distribution->courier->name ?? null,
            'status_pengantaran' => $distribution->status_pengantaran,
            'delivered_at' => $distribution->delivered_at,
            'productionSchedule.tanggal_produksi' => optional($distribution->productionSchedule)->tanggal_produksi,
        ]);

        // dd($distribution->school);

        $this->isEditable = $distribution->status_pengantaran === 'Menunggu';
    }

    public function getFormSchema(): array
    {
        return [
            Section::make('menu_info')
                ->heading('Menu')
                ->icon('heroicon-m-information-circle')
                ->columns(2)
                ->schema([
                    TextEntry::make('menu_hari_ini')
                        ->label('Menu')
                        ->state(fn() => $this->record->productionSchedule->menu_hari_ini)
                        ->columnSpanFull(),

                    TextEntry::make('jumlah_porsi_besar')
                        ->label('Porsi Besar')
                        ->state(fn() => $this->record->jumlah_porsi_besar),

                    TextEntry::make('jumlah_porsi_kecil')
                        ->label('Porsi Kecil')
                        ->state(fn() => $this->record->jumlah_porsi_kecil),
                ]),
            Section::make('address_info')
                ->heading('Alamat Tujuan')
                ->icon('heroicon-m-home-modern')
                ->columns(2)
                ->schema([
                    TextEntry::make('school.nama_sekolah')
                        ->label('Nama Sekolah')
                        ->state(fn() => $this->record->school->nama_sekolah ?? '-')
                        ->columnSpanFull(),
                    TextEntry::make('school.alamat')
                        ->label('Alamat Tujuan')
                        ->state(fn() => $this->record->school->alamat ?? '-')
                        ->columnSpanFull(),
                ]),
            Section::make('delivery_status')
                ->heading('Status Pengantaran')
                ->icon('heroicon-m-truck')
                ->columns(2)
                ->schema([
                    TextEntry::make('courier.name')
                        ->label('Petugas Pengantar')
                        ->state(fn() => $this->record->courier->name ?? null)
                        ->placeholder('Belum Ditugaskan')
                        ->badge()
                        ->color('info'),

                    TextEntry::make('status_pengantaran')
                        ->label('Status')
                        ->state(fn() => $this->record->status_pengantaran)
                        ->badge()
                        ->color(fn($state) => match ($state) {
                            'Menunggu' => 'warning',
                            'Sedang Dikirim' => 'info',
                            'Terkirim' => 'success',
                            default => 'gray',
                        }),

                    Action::make('startDelivery')
                        ->label('Antarkan')
                        ->icon('heroicon-m-truck')
                        ->color('primary')
                        ->visible(fn() => $this->record->status_pengantaran === 'Menunggu')
                        ->action(function () {
                            $this->save(); // triggers your existing logic
                            $this->dispatch('refresh-page');
                        }),

                    // 2️⃣ ACTION BUTTON (Sedang Dikirim)
                    Action::make('openProofModal')
                        ->label('Selesaikan Pengantaran')
                        ->icon('heroicon-m-check-circle')
                        ->color('success')
                        ->visible(fn() => $this->record->status_pengantaran === 'Sedang Dikirim')
                        ->action(function (array $data) {
                            $this->saveProof($data);
                        })
                        ->modalHeading('Selesaikan Pengantaran')
                        ->modalCancelActionLabel('Batal')
                        ->schema([
                            FileUpload::make('photo_of_proof')
                                ->label('Foto Bukti')
                                ->image()
                                ->disk('local')
                                ->directory(fn() => "delivery/{$this->record->id}/proof")
                                ->preserveFilenames()
                                ->visibility('private')
                                ->required(),

                            Textarea::make('notes')
                                ->label('Catatan'),
                        ]),

                    // 3️⃣ SHOW PROOF IF TERKIRIM
                    ImageEntry::make('photo_of_proof')
                        ->label('Foto Bukti')
                        ->visible(fn() => $this->record->status_pengantaran === 'Terkirim')
                        ->columnSpanFull()
                        ->imageHeight('300px')
                        ->imageWidth('100%')
                        ->state(fn() => $this->record->photo_of_proof),

                    TextEntry::make('notes')
                        ->label('Catatan')
                        ->visible(fn() => $this->record->status_pengantaran === 'Terkirim')
                        ->state(fn() => $this->record->notes ?? '-')
                        ->columnSpanFull(),
                ]),
        ];
    }

    public function saveProof(array $data)
    {
        DB::transaction(function () use ($data) {
            $this->record->update([
                'status_pengantaran' => 'Terkirim',
                'notes' => $data['notes'] ?? null,
                'photo_of_proof' => $data['photo_of_proof'], // path inside private storage
                'delivered_at' => now(),
            ]);

            if ($this->record->productionSchedule->getIsFullyDeliveredAttribute()) {
                $this->record->productionSchedule->update([
                    'status' => 'Selesai',
                ]);
            }
        });

        Notification::make()
            ->title('Pengantaran diselesaikan')
            ->success()
            ->send();
    }

    public function save(): void
    {
        Gate::authorize('View:Delivery');

        $user = Auth::user();
        $distribution = $this->record;
        $production = $this->record->productionSchedule;

        if ($this->record->status_pengantaran === 'Terkirim') {
            return;
        }

        // dd($production->getIsFullyDeliveredAttribute());

        if ($production->status === 'Ditolak') {
            Notification::make()
                ->title('Distribusi ditolak, makanan batal untuk dikirim.')
                ->success()
                ->send();

            return;
        }

        if ($distribution->user_id != null && $distribution->user_id != $user->id) {
            Notification::make()
                ->title('Makanan sedang dikirim oleh petugas lain.')
                ->success()
                ->send();

            return;
        }

        if ($distribution->status_pengantaran === 'Terkirim') {
            Notification::make()
                ->title('Makanan sudah terkirim.')
                ->success()
                ->send();

            return;
        }

        if ($distribution->status_pengantaran === 'Menunggu') {
            $distribution->update([
                'status_pengantaran' => 'Sedang Dikirim',
                'user_id' => $user->id,
            ]);

            if ($production->status === 'Terverifikasi') {
                $production->update([
                    'status' => 'Didistribusikan',
                ]);
            }

            Notification::make()
                ->title('Anda telah ditugaskan untuk mengantar makanan ini.')
                ->success()
                ->send();

            return;
        }

        if ($distribution->status_pengantaran === 'Sedang Dikirim') {
            $distribution->update([
                'status_pengantaran' => 'Terkirim',
                'delivered_at' => now(),
            ]);

            if ($production->getIsFullyDeliveredAttribute()) {
                $production->update([
                    'status' => 'Selesai',
                ]);
            }

            Notification::make()
                ->title('Anda telah menyelesaikan pengiriman makanan ini.')
                ->success()
                ->send();

            return;
        }
    }
}
