<div class="space-y-4">
    <div class="prose dark:prose-invert max-w-none">
        <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">
            Dibuat oleh: {{ $instruction->creator->name }} pada {{ $instruction->created_at->format('d M Y') }}
        </div>
        
        <div class="text-gray-900 dark:text-gray-100">
            {!! $instruction->content !!}
        </div>
    </div>

    @if($instruction->attachment_path)
        <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border dark:border-gray-700">
            <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">Lampiran:</h4>
            @php
                $extension = pathinfo($instruction->attachment_path, PATHINFO_EXTENSION);
                $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                $url = \Illuminate\Support\Facades\Storage::disk('public')->url($instruction->attachment_path);
            @endphp

            @if($isImage)
                <div class="mb-1">
                    <img src="{{ $url }}" alt="Lampiran Instruksi" class="max-w-full h-auto rounded-lg shadow-sm" style="max-height: 400px;">
                </div>
            @else
                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>File Lampiran ({{ strtoupper($extension) }})</span>
                </div>
            @endif
        </div>
    @endif

    <div class="border-t dark:border-gray-700 pt-4 mt-4">
        @livewire('instruction-acknowledge', ['instructionId' => $instruction->id])
    </div>
</div>
