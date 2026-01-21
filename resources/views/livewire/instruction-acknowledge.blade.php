<div class="flex flex-col items-center justify-center p-6 bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
    <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-4">
        Konfirmasi bahwa Anda telah membaca instruksi ini
    </h4>

    <button
        type="button"
        wire:click="acknowledge"
        wire:loading.attr="disabled"
        @if($acknowledgment) disabled @endif
        class="
            relative flex items-center justify-center gap-2 px-8 py-3 rounded-full font-bold text-sm transition-all duration-300 shadow-sm
            {{ $acknowledgment
                ? 'bg-green-500 text-white ring-2 ring-green-500 ring-offset-2 dark:ring-offset-gray-900 cursor-default'
                : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 ring-1 ring-gray-300 dark:ring-gray-600 hover:ring-primary-500 hover:text-primary-600 dark:hover:text-primary-400'
            }}
        "
    >
        @if($acknowledgment)
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
            </svg>
            <span>SUDAH DIBACA</span>
        @else
            <span class="w-5 h-5 border-2 border-gray-300 border-t-transparent rounded-full animate-spin" wire:loading></span>
            <span wire:loading.remove>
                SAYA SUDAH MEMBACA
            </span>
        @endif
    </button>

    @if($acknowledgment)
        <p class="mt-3 text-xs text-gray-400 dark:text-gray-500 font-mono">
            {{ $acknowledgment->acknowledged_at->format('d M Y • H:i') }} WIB
        </p>
    @endif
</div>

