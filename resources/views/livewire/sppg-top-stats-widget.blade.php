<x-filament-widgets::widget>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Box 1: Unit SPPG (2/3 width or 3/3 if no alert) --}}
        <div class="{{ $pendingCount > 0 ? 'md:col-span-2' : 'md:col-span-3' }}">
            <div class="fi-wi-stats-overview-stat relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="grid gap-y-2">
                    <div class="flex items-center gap-x-2">
                        <x-filament::icon
                            icon="heroicon-o-home"
                            class="fi-wi-stats-overview-stat-icon h-5 w-5 text-gray-400 dark:text-gray-500"
                        />
                        <span class="fi-wi-stats-overview-stat-label text-sm font-medium text-gray-500 dark:text-gray-400">
                            Unit SPPG
                        </span>
                    </div>

                    <div class="fi-wi-stats-overview-stat-value text-3xl font-semibold tracking-tight text-gray-950 dark:text-white">
                        {{ $isNationalView ? 'Agregat Nasional' : ($sppg?->nama_sppg ?? 'N/A') }}
                    </div>

                    @if(!$isNationalView && isset($sppg?->kepalaSppg))
                        <div class="fi-wi-stats-overview-stat-description text-sm text-gray-500 dark:text-gray-400">
                            {{ $sppg->kepalaSppg->name }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Box 2: Perhatian (1/3 width, only shows if pendingCount > 0) --}}
        @if($pendingCount > 0)
            <div class="md:col-span-1">
                <a href="/sppg/production-schedules" class="block">
                    <div class="fi-wi-stats-overview-stat relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 border-l-4 border-amber-500 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                        <div class="grid gap-y-2">
                            <div class="flex items-center gap-x-2">
                                <x-filament::icon
                                    icon="heroicon-s-exclamation-triangle"
                                    class="fi-wi-stats-overview-stat-icon h-5 w-5 text-amber-500"
                                />
                                <span class="fi-wi-stats-overview-stat-label text-sm font-medium text-amber-500">
                                    Perhatian
                                </span>
                            </div>

                            <div class="fi-wi-stats-overview-stat-value text-3xl font-semibold tracking-tight text-gray-950 dark:text-white">
                                {{ $pendingCount }} Rencana
                            </div>

                            <div class="fi-wi-stats-overview-stat-description text-sm text-gray-500 dark:text-gray-400">
                                Segera lengkapi menu & verifikasi.
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endif
    </div>
</x-filament-widgets::widget>
