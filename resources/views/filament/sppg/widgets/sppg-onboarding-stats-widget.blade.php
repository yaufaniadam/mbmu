<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center gap-x-3 mb-4">
            <x-filament::icon
                icon="heroicon-o-clipboard-document-check"
                class="w-8 h-8 text-primary-500"
            />
            
            <div class="flex-1">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                    Status Kelengkapan Data SPPG
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Selesaikan langkah berikut agar SPPG Anda siap beroperasi penuh.
                </p>
            </div>

            @if($progress['is_complete'] ?? false)
                <span class="inline-flex items-center rounded-full bg-success-50 px-2.5 py-1 text-xs font-medium text-success-700 ring-1 ring-inset ring-success-600/20">
                    🎉 Semua Lengkap
                </span>
            @else
                <div class="flex items-center gap-2">
                    <span class="text-sm font-semibold text-primary-600">
                        {{ $progress['percentage'] ?? 0 }}% Selesai
                    </span>
                    <div class="w-24 bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                        <div class="bg-primary-600 h-2.5 rounded-full" style="width: {{ $progress['percentage'] ?? 0 }}%"></div>
                    </div>
                </div>
            @endif
        </div>

        @if($hasSppg)
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                @foreach($progress['steps'] as $key => $step)
                    <div @class([
                        'rounded-xl border p-4 transition-all hover:shadow-md',
                        'bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-800' => !$step['completed'],
                        'bg-success-50/50 dark:bg-success-900/10 border-success-200 dark:border-success-800' => $step['completed'],
                    ])>
                        <div class="flex items-start justify-between mb-2">
                            <div @class([
                                'p-2 rounded-lg',
                                'bg-primary-50 text-primary-600 dark:bg-primary-900/20 dark:text-primary-400' => !$step['completed'],
                                'bg-success-100 text-success-600 dark:bg-success-900/20 dark:text-success-400' => $step['completed'],
                            ])>
                                <x-filament::icon
                                    :icon="$step['icon']"
                                    class="w-6 h-6"
                                />
                            </div>
                            @if($step['completed'])
                                <x-filament::icon
                                    icon="heroicon-m-check-circle"
                                    class="w-6 h-6 text-success-500"
                                />
                            @endif
                        </div>

                        <h3 class="font-bold text-gray-900 dark:text-white mb-1">
                            {{ $step['title'] }}
                        </h3>
                        
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4 h-10 line-clamp-2">
                            {{ $step['description'] }}
                        </p>

                        <x-filament::button
                            tag="a"
                            :href="route($step['route_name'])"
                            :color="$step['completed'] ? 'gray' : 'primary'"
                            :outlined="$step['completed']"
                            size="sm"
                            class="w-full"
                        >
                            {{ $step['completed'] ? 'Perbarui Data' : 'Lengkapi Sekarang' }}
                        </x-filament::button>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-4">
                <p class="text-danger-500">
                    Akun Anda belum terhubung dengan data SPPG. Harap hubungi Admin Kornas.
                </p>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
