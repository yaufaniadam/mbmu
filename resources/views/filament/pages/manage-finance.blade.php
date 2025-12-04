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
        @if (auth()->user()->hasAnyRole(['Pimpinan Lembaga Pengusul', 'Staf Kornas', 'Direktur Kornas']))
            <x-filament::tabs.item :active="$activeTab === 'verify'" wire:click="$set('activeTab', 'verify')"
                icon="heroicon-o-check-badge">
                Verifikasi Pembayaran
            </x-filament::tabs.item>
        @endif

        {{-- Tab 3: Transaksi Masuk --}}
        {{-- Only show this tab to Pengusul or Kornas --}}
        @if (auth()->user()->hasAnyRole(['Pimpinan Lembaga Pengusul', 'Staf Kornas', 'Direktur Kornas']))
            <x-filament::tabs.item :active="$activeTab === 'incoming_payment'" wire:click="$set('activeTab', 'incoming_payment')"
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
        @if ($activeTab === 'transaction')
            {{-- This renders the TransactionList widget --}}
            {{-- This renders the IncomingVerificationTable widget --}}
            @livewire(\App\Livewire\TransactionList::class)
        @endif

        {{-- Content for Tab 3 --}}
        @if ($activeTab === 'incoming_payment')
            {{-- This renders the incomeList widget --}}
            {{-- This renders the IncomingVerificationTable widget --}}
            @livewire(\App\Livewire\IncomingPayment::class)
        @endif

        {{-- Content for Tab 4 --}}
        @if ($activeTab === 'verify')
            {{-- This renders the VerifyPaymentList widget --}}
            {{-- This renders the VerifyPaymentList widget --}}
            @livewire(\App\Livewire\VerifyPaymentList::class)
        @endif

    </div>

</x-filament-panels::page>
