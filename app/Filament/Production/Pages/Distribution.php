<?php

namespace App\Filament\Production\Pages;

use App\Models\FoodVerification;
use App\Models\ProductionSchedule;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\FontWeight;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\HtmlString;

class Distribution extends Page implements HasInfolists
{
    use InteractsWithForms;
    use InteractsWithInfolists;

    protected string $view = 'filament.production.pages.distribution';

    protected static ?string $navigationLabel = 'Pengantaran';

    protected ?string $heading = '';

    public ?array $data = [];

    public ?ProductionSchedule $record = null;

    public $pendingPickups = null; // Distributions waiting for utensil pickup

    protected bool $isEditable = true;

    protected ?FoodVerification $verificationNote = null;

    public function getLayout(): string
    {
        return 'layouts.mobile-navigation';
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-truck';
    }

    public static function shouldRegisterNavigation(): bool
    {
        // This checks the permission you just generated
        return auth()->user()->can('View:Distribution');
    }

    public function mount(): void
    {
        Gate::authorize('View:Distribution');

        $user = Auth::user();
        $organizationId = $user->unitTugas()->first()?->id;

        if (!$organizationId) {
            Notification::make()
                ->title('SPPG tidak ditemukan.')
                ->danger()
                ->send();
            return;
        }

        // Get active production schedule for delivery
        $this->record = ProductionSchedule::where('sppg_id', $organizationId)
            ->whereNotIn('status', ['Direncanakan', 'Ditolak', 'Selesai', 'Menunggu ACC Kepala SPPG'])
            ->with('sppg', 'sppg.schools')
            ->latest()
            ->first();

        // Get distributions waiting for utensil pickup
        $this->pendingPickups = \App\Models\Distribution::whereHas('productionSchedule', function ($q) use ($organizationId) {
                $q->where('sppg_id', $organizationId);
            })
            ->where('status_pengantaran', 'Terkirim')
            ->whereIn('pickup_status', ['Menunggu', 'Sedang Dijemput'])
            ->with(['school', 'productionSchedule', 'courier'])
            ->get();

        if (!$this->record && $this->pendingPickups->isEmpty()) {
            // No delivery or pickup pending
            return;
        }

        if ($this->record) {
            $this->isEditable = $this->record->status === 'Terverifikasi';
            $this->verificationNote = FoodVerification::where('jadwal_produksi_id', $this->record->id)->latest()->first();
        }
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->record($this->record)
            ->components([
                // 1. Date and Status Section
                Section::make()
                    ->schema([
                        TextEntry::make('tanggal')
                            ->hiddenLabel()
                            ->date('l, d F Y') // Filament handles locale automatically based on config
                            ->size('lg')
                            ->weight(FontWeight::Bold)
                            ->alignCenter(),

                        TextEntry::make('status')
                            ->hiddenLabel()
                            ->badge()
                            ->alignCenter()
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'Terverifikasi' => 'Siap didistribusikan',
                                'Ditolak' => 'Produk pangan tidak memenuhi kriteria',
                                'Didistribusikan' => 'Produk pangan sedang didistribusikan',
                                'Selesai' => 'Produk pangan selesai didistribusikan',
                                default => $state,
                            })
                            ->color(fn (string $state): array|string => match ($state) {
                                'Terverifikasi' => Color::Blue,
                                'Ditolak' => Color::Red,
                                'Didistribusikan', 'Selesai' => Color::Emerald,
                                default => 'gray',
                            }),
                    ]),

                // 2. Menu and Portions Grid
                Section::make('Detail Produksi')
                    ->schema([
                        TextEntry::make('menu_hari_ini')
                            ->label('Daftar Menu')
                            ->columnSpan(2),

                        TextEntry::make('total_porsi_besar')
                            ->label('Jumlah Porsi Besar')
                            ->state(fn (ProductionSchedule $record) => $record->getTotalPorsiBesarAttribute().' Porsi'),

                        TextEntry::make('total_porsi_kecil')
                            ->label('Jumlah Porsi Kecil')
                            ->state(fn (ProductionSchedule $record) => $record->getTotalPorsiKecilAttribute().' Porsi'),
                    ])
                    ->columns(2),

                // 3. Distribution List (The Loop)
                Section::make('Daftar Sekolah Penerima')
                    ->headerActions([
                        // Optional: Add a general action here if needed
                    ])
                    ->schema([
                        RepeatableEntry::make('distributions')
                            ->hiddenLabel()
                            ->contained(false) // Makes it look like cards/blocks
                            ->visible(function ($record) {
                                $user = Auth::user();

                                return $record->user_id == null || $record->user_id == $user->id;
                            })
                            ->schema([
                                TextEntry::make('details')
                                    ->hiddenLabel()
                                    ->columnSpanFull()
                                    ->url(fn ($record) => Delivery::getUrl(['distribution' => $record->id]))
                                    ->html()
                                    ->state(fn ($record) => new HtmlString(
                                        '<div class="flex flex-col gap-3 text-left">'
                                            // 1. School Name
                                            .'<div class="font-bold text-lg text-gray-900 dark:text-white">'
                                            .e($record->school->nama_sekolah)
                                            .'</div>'

                                            // 2. Address Container
                                            .'<div class="bg-gray-50 dark:bg-white/5 border border-gray-100 dark:border-white/5 rounded-lg p-3">'
                                            .'<div class="flex items-center gap-2 mb-1 text-gray-500 dark:text-gray-400">'
                                            .'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">'
                                            .'<path fill-rule="evenodd" d="M9.293 2.293a1 1 0 011.414 0l7 7A1 1 0 0117 11h-1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-3a1 1 0 00-1-1H9a1 1 0 00-1 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1v-6H3a1 1 0 01-.707-1.707l7-7z" clip-rule="evenodd" />'
                                            .'</svg>'
                                            .'<span class="text-xs font-semibold uppercase tracking-wider">Alamat Sekolah</span>'
                                            .'</div>'
                                            .'<div class="text-sm text-gray-700 dark:text-gray-300">'
                                            .e($record->school->alamat)
                                            .'</div>'
                                            .'</div>'

                                            // 3. Courier Status Logic
                                            .($record->user_id
                                                ? ($record->user_id == auth()->id()
                                                    ? '<div>'
                                                    .'<span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-700/10 dark:bg-blue-400/10 dark:text-blue-400 dark:ring-blue-400/30">'
                                                    .'Anda ditugaskan untuk mengantarkan ke alamat ini'
                                                    .'</span>'
                                                    .'</div>'
                                                    : '<div>'
                                                    .'<span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-600/20 dark:bg-amber-400/10 dark:text-amber-400 dark:ring-amber-400/30">'
                                                    .'Pengirim: '.e($record->courier->name)
                                                    .'</span>'
                                                    .'</div>'
                                                )
                                                : ''
                                            )
                                            .'</div>'
                                    ))
                                    ->extraAttributes([
                                        'class' => 'block w-full p-4 bg-white rounded-xl border border-gray-200 shadow-sm hover:bg-gray-50 dark:bg-gray-900 dark:border-white/10 dark:hover:bg-white/5 transition duration-200',
                                    ]),
                            ]),
                    ]),
            ]);
    }

    // public function save(): void
    // {
    //     if (! $this->isEditable || ! $this->record) {
    //         Notification::make()
    //             ->title('Data tidak dapat diedit.')
    //             ->warning()
    //             ->send();
    //         return;
    //     }

    //     // update production schedule status
    //     $this->record->update([
    //         'status' => "Didistribusikan",
    //     ]);

    //     Notification::make()
    //         ->title('Data berhasil diperbarui!')
    //         ->success()
    //         ->send();

    //     // **Important**: refresh model and refill form so disabled states re-evaluate immediately
    //     $this->record->refresh();
    //     $this->isEditable = $this->record->status === 'Terverifikasi';
    // }

}
