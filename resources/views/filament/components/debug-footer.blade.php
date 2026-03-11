<div class="px-4 py-2 text-xs text-gray-500 bg-gray-50/50 border-t border-gray-200 dark:bg-gray-900/50 dark:border-gray-800 flex justify-between items-center">
    <div class="flex items-center gap-4">
        <span>
            <strong>Page load:</strong> {{ number_format(microtime(true) - LARAVEL_START, 3) }}s
        </span>
        <span>
            <strong>Memory:</strong> {{ number_format(memory_get_peak_usage(true) / 1024 / 1024, 2) }}MB
        </span>
        @if(config('octane.server'))
            <span class="flex items-center gap-1">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-success-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-success-500"></span>
                </span>
                <strong>Octane:</strong> {{ config('octane.server') }}
            </span>
        @endif
    </div>
    <div class="hidden sm:block italic">
        Makan Bergizi Muhammadiyah (MBM1912) &copy; {{ date('Y') }}
    </div>
</div>
