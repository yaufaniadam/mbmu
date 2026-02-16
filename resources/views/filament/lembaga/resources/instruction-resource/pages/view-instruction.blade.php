<x-filament-panels::page>
    <div class="max-w-4xl mx-auto space-y-6">
        {{-- Card Container --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg ring-1 ring-gray-950/5 dark:ring-white/10 overflow-hidden">
            
            {{-- Header (Title & Status) --}}
            <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 flex flex-col md:flex-row md:items-start justify-between gap-4">
                <div class="space-y-1">
                    <h2 class="text-xl font-bold tracking-tight text-gray-900 dark:text-white">
                        {{ $record->title }}
                    </h2>
                    <div class="flex items-center gap-3 text-sm text-gray-500 dark:text-gray-400">
                        <div class="flex items-center gap-2">
                             <x-filament::avatar
                                src="{{ \Filament\Facades\Filament::getUserAvatarUrl($record->creator) }}" 
                                alt="{{ $record->creator->name }}"
                                size="xs"
                                class="ring-1 ring-white dark:ring-gray-700"
                            />
                            <span class="font-medium text-gray-700 dark:text-gray-300">{{ $record->creator->name }}</span>
                        </div>
                        <span>&bull;</span>
                        <span>{{ $record->created_at->translatedFormat('d F Y, H:i') }}</span>
                    </div>
                </div>
                
                {{-- Status Badge --}}
                <div class="flex-shrink-0 self-start">
                    @if($record->isAcknowledgedBy(auth()->id()))
                        <div class="px-3 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700 dark:bg-green-500/10 dark:text-green-400 border border-green-200 dark:border-green-500/20 flex items-center gap-1.5">
                            <x-filament::icon icon="heroicon-m-check-circle" class="h-4 w-4" />
                            <span>Sudah Dibaca</span>
                        </div>
                    @else
                        <div class="px-3 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400 border border-amber-200 dark:border-amber-500/20 flex items-center gap-1.5">
                            <x-filament::icon icon="heroicon-m-clock" class="h-4 w-4" />
                            <span>Belum Dibaca</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Body Content --}}
            <div class="p-6 md:p-8">
                <div class="prose prose-sm sm:prose-base dark:prose-invert max-w-none text-gray-700 dark:text-gray-300 leading-relaxed">
                    {!! $record->content !!}
                </div>
            </div>

            {{-- Attachments --}}
            @if($record->attachment_path)
                @php
                    $extension = pathinfo($record->attachment_path, PATHINFO_EXTENSION);
                    $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                    $url = \Illuminate\Support\Facades\Storage::disk('public')->url($record->attachment_path);
                @endphp

                <div class="px-6 pb-6 md:px-8 md:pb-8">
                    <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                <x-filament::icon icon="heroicon-m-paper-clip" class="h-4 w-4 text-gray-500" />
                                Lampiran
                            </h4>
                            <a href="{{ $url }}" target="_blank" class="text-xs font-medium text-primary-600 hover:text-primary-500 hover:underline dark:text-primary-400">
                                Download File &rarr;
                            </a>
                        </div>
                        
                        @if($isImage)
                            <div class="mt-2 rounded bg-white dark:bg-gray-800 p-2 shadow-sm border border-gray-200 dark:border-gray-700 inline-block overflow-hidden">
                                <a href="{{ $url }}" target="_blank">
                                    <img src="{{ $url }}" alt="Lampiran" class="max-w-full h-auto max-h-[300px] rounded object-contain hover:opacity-90 transition cursor-pointer">
                                </a>
                            </div>
                        @else
                            <div class="flex items-center gap-3 p-3 bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-700">
                                <div class="p-2 bg-gray-100 dark:bg-gray-700 rounded text-gray-500 dark:text-gray-400">
                                    <x-filament::icon icon="heroicon-o-document" class="h-6 w-6" />
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate max-w-[200px]">{{ basename($record->attachment_path) }}</p>
                                    <p class="text-xs text-gray-500 uppercase">{{ $extension }} File</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Footer / Actions --}}
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/80 border-t border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row sm:justify-end sm:items-center gap-4">
                 @if(!$record->isAcknowledgedBy(auth()->id()))
                 <span class="text-xs text-gray-500 dark:text-gray-400 italic text-center sm:text-right">
                    Silakan konfirmasi bahwa Anda telah membaca instruksi ini.
                </span>
                @endif
                <div class="w-full sm:w-auto">
                    @livewire('instruction-acknowledge', ['instructionId' => $record->id])
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
