<x-filament-panels::page>
    {{-- Page content --}}
    {{-- {{ $this->form }} --}}
    {{-- 1. The Tab Navigation Bar --}}
    <x-filament::tabs label="Finance Tabs">

        {{-- Tab 0: Dashboard Keuangan --}}
        @if (auth()->user()->hasAnyRole(['Superadmin', 'Pimpinan Lembaga Pengusul', 'Kepala SPPG', 'PJ Pelaksana', 'Staf Akuntan', 'Staf Kornas', 'Staf Akuntan Kornas', 'Direktur Kornas']))
            <x-filament::tabs.item :active="$activeTab === 'dashboard'" wire:click="$set('activeTab', 'dashboard')" icon="heroicon-o-chart-bar">
                Dashboard
            </x-filament::tabs.item>
        @endif
        {{-- Tab 1: Buku Kas Pusat (Kornas Only) --}}
        @if (auth()->user()->hasAnyRole(['Superadmin', 'Staf Kornas', 'Staf Akuntan Kornas', 'Direktur Kornas']))
            <x-filament::tabs.item :active="$activeTab === 'buku_kas_pusat'" wire:click="$set('activeTab', 'buku_kas_pusat')" icon="heroicon-o-building-library">
                Buku Kas Pusat
            </x-filament::tabs.item>
            <x-filament::tabs.item :active="$activeTab === 'audit_sppg'" wire:click="$set('activeTab', 'audit_sppg')" icon="heroicon-o-magnifying-glass-circle">
                Monitoring SPPG
            </x-filament::tabs.item>
        @endif


        {{-- Tab 2: Bayar Sewa (SPPG Only) --}}
        @if (auth()->user()->hasAnyRole(['Kepala SPPG', 'PJ Pelaksana', 'Staf Akuntan']))
            <x-filament::tabs.item :active="$activeTab === 'buku_kas'" wire:click="$set('activeTab', 'buku_kas')" icon="heroicon-o-book-open">
                Buku Kas
            </x-filament::tabs.item>
            <x-filament::tabs.item :active="$activeTab === 'pay_rent'" wire:click="$set('activeTab', 'pay_rent')" icon="heroicon-o-credit-card">
                Bayar Sewa
            </x-filament::tabs.item>
        @endif

        {{-- Tab 3: Penerimaan Sewa (LP Only) --}}
        @if (auth()->user()->hasAnyRole(['Superadmin', 'Pimpinan Lembaga Pengusul']))
            <x-filament::tabs.item :active="$activeTab === 'verify_rent'" wire:click="$set('activeTab', 'verify_rent')" icon="heroicon-o-arrow-path-rounded-square">
                Penerimaan Sewa
            </x-filament::tabs.item>
        @endif

        {{-- Tab 4: Bayar Royalti (LP Only) --}}
        @if (auth()->user()->hasAnyRole(['Pimpinan Lembaga Pengusul']))
            <x-filament::tabs.item :active="$activeTab === 'pay_royalty'" wire:click="$set('activeTab', 'pay_royalty')" icon="heroicon-o-banknotes">
                Bayar Royalti
            </x-filament::tabs.item>
        @endif

        {{-- Tab 5: Penerimaan Royalti (Kornas Only) --}}
        @if (auth()->user()->hasAnyRole(['Superadmin', 'Staf Kornas', 'Staf Akuntan Kornas', 'Direktur Kornas']))
            <x-filament::tabs.item :active="$activeTab === 'verify_royalty'" wire:click="$set('activeTab', 'verify_royalty')" icon="heroicon-o-check-badge">
                Penerimaan Royalti
            </x-filament::tabs.item>
        @endif

        {{-- Tab 6: Riwayat Transaksi (All) - HIDDEN --}}
        {{-- @if (auth()->user()->hasAnyRole(['Superadmin', 'Pimpinan Lembaga Pengusul', 'Kepala SPPG', 'PJ Pelaksana', 'Staf Akuntan', 'Staf Kornas', 'Staf Akuntan Kornas', 'Direktur Kornas']))
            <x-filament::tabs.item :active="$activeTab === 'transactions'" wire:click="$set('activeTab', 'transactions')" icon="heroicon-o-clock">
                Riwayat Transaksi
            </x-filament::tabs.item>
        @endif --}}

    </x-filament::tabs>

    {{-- Content --}}
    <div class="mt-4">

        @if ($activeTab === 'dashboard')
            <div style="display: flex; flex-direction: column; gap: 2rem;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                    @livewire(\App\Livewire\OperatingExpensesChart::class)
                    @livewire(\App\Livewire\IncomingFundsChart::class)
                </div>
            </div>
        @endif

        @if ($activeTab === 'buku_kas_pusat')
            <div style="display: flex; flex-direction: column; gap: 2rem;">
                <div style="display: flex; gap: 2rem; width: 100%;">
                    <div style="flex: 1;">
                        @livewire(\App\Livewire\OperatingExpensesStats::class, ['scope' => 'central'])
                    </div>
                    <div style="flex: 1;">
                        @livewire(\App\Livewire\SppgFunds::class, ['scope' => 'central'])
                    </div>
                </div>
                @livewire(\App\Livewire\OperatingExpenses::class, ['scope' => 'central'])
                @livewire(\App\Livewire\IncomingFunds::class, ['scope' => 'central'])
            </div>
        @endif

        @if ($activeTab === 'audit_sppg')
            <div style="display: flex; flex-direction: column; gap: 2rem;">
                <x-filament::section>
                    <x-slot name="heading">Monitoring Keuangan Unit SPPG</x-slot>
                    <p class="text-sm text-gray-500 mb-4">Pilih unit SPPG untuk memantau detail pemasukan dan pengeluaran harian mereka.</p>
                </x-filament::section>
                @livewire(\App\Livewire\OperatingExpenses::class, ['scope' => 'unit'])
                @livewire(\App\Livewire\IncomingFunds::class, ['scope' => 'unit'])
            </div>
        @endif


        @if ($activeTab === 'buku_kas')
            <div style="display: flex; flex-direction: column; gap: 2rem;">
                <div style="display: flex; gap: 2rem; width: 100%;">
                    <div style="flex: 1;">
                        @livewire(\App\Livewire\OperatingExpensesStats::class)
                    </div>
                    <div style="flex: 1;">
                        @livewire(\App\Livewire\SppgFunds::class)
                    </div>
                </div>
                @livewire(\App\Livewire\OperatingExpenses::class)
                @livewire(\App\Livewire\IncomingFunds::class)
            </div>
        @endif

        @if ($activeTab === 'pay_rent')
            @livewire(\App\Livewire\BillList::class, ['type' => 'SPPG_SEWA'])
        @endif

        @if ($activeTab === 'verify_rent')
            <div style="display: flex; flex-direction: column; gap: 2rem;">
                @if (!auth()->user()->hasRole('Superadmin'))
                    @livewire(\App\Livewire\VerifyPaymentList::class, ['type' => 'SPPG_SEWA'])
                @endif
                @livewire(\App\Livewire\IncomingPayment::class, ['type' => 'SPPG_SEWA'])
            </div>
        @endif

        @if ($activeTab === 'pay_royalty')
            @livewire(\App\Livewire\BillList::class, ['type' => 'LP_ROYALTY'])
        @endif

        @if ($activeTab === 'verify_royalty')
             <div style="display: flex; flex-direction: column; gap: 2rem;">
                @if (!auth()->user()->hasAnyRole(['Superadmin', 'Staf Akuntan Kornas']))
                    @livewire(\App\Livewire\VerifyPaymentList::class, ['type' => 'LP_ROYALTY'])
                @endif
                @livewire(\App\Livewire\IncomingPayment::class, ['type' => 'LP_ROYALTY'])
            </div>
        @endif

        {{-- @if ($activeTab === 'transactions')
            @livewire(\App\Livewire\TransactionList::class)
        @endif --}}

    </div>

</x-filament-panels::page>
