<x-filament-panels::page>
    <div class="space-y-6">
        <x-filament::section>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold">Pending Invoice Cycles</h2>
                    <p class="text-sm text-gray-500">
                        The following SPPGs have completed a 10-day cycle but have not been invoiced yet.
                        This may indicate the automated scheduler is stuck or inactive.
                    </p>
                </div>
                <div>
                    <x-filament::button 
                        wire:click="generateAll"
                        color="success"
                        wire:loading.attr="disabled"
                    >
                        Generate ALL Invoices
                    </x-filament::button>
                </div>
            </div>

            <div class="mt-6 overflow-x-auto rounded-lg border border-gray-200 dark:border-white/10">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 dark:bg-white/5 font-medium text-gray-700 dark:text-gray-200">
                        <tr>
                            <th class="px-4 py-3">SPPG Name</th>
                            <th class="px-4 py-3">Billing Period</th>
                            <th class="px-4 py-3 text-right">Active Days</th>
                            <th class="px-4 py-3 text-right">Est. Amount</th>
                            <th class="px-4 py-3 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                        @forelse($pendingInvoices as $invoice)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                            <td class="px-4 py-3 font-medium">
                                {{ $invoice['sppg_name'] }}
                            </td>
                            <td class="px-4 py-3">
                                {{ \Carbon\Carbon::parse($invoice['start_date'])->format('d M Y') }} - 
                                {{ \Carbon\Carbon::parse($invoice['end_date'])->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                {{ $invoice['active_days'] }} days
                            </td>
                            <td class="px-4 py-3 text-right font-mono">
                                Rp {{ number_format($invoice['amount'], 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button 
                                    wire:click="generateSingle({{ $invoice['sppg_id'] }})"
                                    wire:loading.attr="disabled"
                                    class="text-primary-600 hover:text-primary-500 font-bold text-xs uppercase tracking-wide"
                                >
                                    Generate
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <x-filament::icon
                                        icon="heroicon-o-check-circle"
                                        class="w-12 h-12 text-success-500 mb-2"
                                    />
                                    <p>All active SPPGs are up to date. No pending invoices found.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
