<div class="space-y-4">
    <div class="prose dark:prose-invert max-w-none">
        <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">
            Dibuat oleh: {{ $instruction->creator->name }} pada {{ $instruction->created_at->format('d M Y') }}
        </div>
        
        <div class="text-gray-900 dark:text-gray-100">
            {!! $instruction->content !!}
        </div>
    </div>

    <div class="border-t dark:border-gray-700 pt-4 mt-4">
        @livewire('instruction-acknowledge', ['instructionId' => $instruction->id])
    </div>
</div>
