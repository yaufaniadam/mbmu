<div>
    @if($acknowledgment)
        <div class="flex items-center gap-3 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
            <svg class="w-4 h-4 text-green-600 dark:text-green-400" style="width: 1rem; height: 1rem;" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <div>
                <div class="font-medium text-green-800 dark:text-green-200">Sudah Dibaca</div>
                <div class="text-sm text-green-600 dark:text-green-400">
                    {{ $acknowledgment->acknowledged_at->format('d M Y H:i') }}
                </div>
            </div>
        </div>
    @else
        <button 
            wire:click="acknowledge" 
            type="button"
            class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors"
        >
            <svg class="w-4 h-4" style="width: 1rem; height: 1rem;" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Saya Sudah Membaca
        </button>
    @endif
</div>

