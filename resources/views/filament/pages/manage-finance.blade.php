<x-filament-panels::page>
    {{-- Page content --}}
    {{-- {{ $this->form }} --}}
    {{-- 1. The Tab Navigation Bar --}}
    <x-filament::tabs label="Finance Tabs">

        {{-- Tab 1: Pembayaran (Tagihan Saya) --}}
        @if (auth()->user()->hasAnyRole(['Pimpinan Lembaga Pengusul', 'Kepala SPPG', 'PJ Pelaksana']))
            <x-filament::tabs.item :active="$activeTab === 'pay'" wire:click="$set('activeTab', 'pay')" icon="heroicon-o-credit-card">
                Pembayaran & Tagihan
            </x-filament::tabs.item>

            <x-filament::tabs.item :active="$activeTab === 'transaction'" wire:click="$set('activeTab', 'transaction')"
                icon="heroicon-o-credit-card">
                Riwayat Transaksi
            </x-filament::tabs.item>
        @endif

        {{-- Tab 2: Verifikasi (Uang Masuk) --}}
        {{-- Only show this tab to Pengusul or Kornas --}}
        @if (auth()->user()->hasAnyRole(['Pimpinan Lembaga Pengusul', 'Staf Kornas']))
            <x-filament::tabs.item :active="$activeTab === 'verify'" wire:click="$set('activeTab', 'verify')"
                icon="heroicon-o-check-badge">
                Verifikasi Pembayaran
            </x-filament::tabs.item>
        @endif

        {{-- Tab 3: Transaksi Masuk --}}
        {{-- Only show this tab to Pengusul or Kornas --}}
        @if (auth()->user()->hasAnyRole(['Pimpinan Lembaga Pengusul', 'Staf Kornas']))
            <x-filament::tabs.item :active="$activeTab === 'income'" wire:click="$set('activeTab', 'income')"
                icon="heroicon-o-arrow-trending-up">
                Transaksi Masuk
            </x-filament::tabs.item>
        @endif

    </x-filament::tabs>

    {{-- 2. The Tab Content (Lazy Loaded Widgets) --}}
    <div class="mt-4">

        {{-- Content for Tab 1 --}}
        @if ($activeTab === 'pay')
            {{-- This renders the MyBillsTable widget --}}
            @livewire(\App\Livewire\BillList::class)
        @endif

        {{-- Content for Tab 2 --}}
        @if ($activeTab === 'verify')
            {{-- This renders the IncomingVerificationTable widget --}}
            {{-- @livewire(\App\Filament\Widgets\IncomingVerificationTable::class) --}}
        @endif

    </div>

</x-filament-panels::page>
