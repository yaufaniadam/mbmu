<x-filament-panels::page>
    {{-- Header --}}
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-3">
                <x-filament::badge :color="match($record->status) {
                    'Open' => 'danger',
                    'Responded' => 'warning',
                    'Closed' => 'success',
                    default => 'gray',
                }">
                    {{ $record->status }}
                </x-filament::badge>
                <span class="text-lg font-semibold">{{ $record->subject }}</span>
            </div>
        </x-slot>
        <x-slot name="description">
            Dibuat oleh {{ $record->user->name }} pada {{ $record->created_at->format('d M Y H:i') }}
        </x-slot>

        <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg mt-4">
            <p class="whitespace-pre-wrap text-gray-900 dark:text-gray-100">{{ $record->content }}</p>
        </div>
    </x-filament::section>

    {{-- Messages --}}
    <div>
        @forelse($record->messages as $message)
            @php
                $isKornas = $message->user->hasAnyRole(['Superadmin', 'Direktur Kornas', 'Staf Akuntan Kornas', 'Staf Kornas']);
            @endphp
            
            <x-filament::section class="mb-6" style="margin-bottom: 0.75rem !important;">
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <span class="{{ $isKornas ? 'text-primary-700 dark:text-primary-400' : 'text-gray-900 dark:text-gray-100' }}">
                            {{ $message->user->name }}
                        </span>
                        @if($isKornas)
                            <x-filament::badge color="primary">Kornas</x-filament::badge>
                        @endif
                    </div>
                </x-slot>
                
                <x-slot name="description">
                    {{ $message->created_at->format('d M Y H:i') }}
                </x-slot>
                
                <div class="{{ $isKornas ? 'text-gray-900 dark:text-gray-100' : 'text-gray-900 dark:text-gray-100' }}">
                    <p class="whitespace-pre-wrap leading-relaxed">{{ $message->message }}</p>
                </div>
            </x-filament::section>
        @empty
            <x-filament::section>
                <div class="text-center text-gray-500 py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <p class="font-medium">Belum ada percakapan</p>
                    <p class="text-sm mt-1">Mulai percakapan dengan mengirim balasan di bawah</p>
                </div>
            </x-filament::section>
        @endforelse
    </div>

    {{-- Reply --}}
    @if($record->status !== 'Closed')
        <x-filament::section>
            <x-slot name="heading">Kirim Balasan</x-slot>
            
            <form wire:submit="sendReply">
                <div class="w-full mb-5">
                    <textarea
                        wire:model="replyMessage"
                        rows="4"
                        class="block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"
                        placeholder="Ketik balasan Anda di sini..."
                        style="width: 100%;"
                    ></textarea>
                </div>
                
                <div class="w-full">
                    <x-filament::button type="submit" icon="heroicon-o-paper-airplane">
                        Kirim Balasan
                    </x-filament::button>
                </div>
            </form>
        </x-filament::section>
    @else
        <x-filament::section>
            <x-slot name="heading">Tiket Ditutup</x-slot>
            
            <p class="text-sm text-gray-600 dark:text-gray-400">Percakapan ini sudah selesai dan tidak dapat menerima balasan baru.</p>
        </x-filament::section>
    @endif
</x-filament-panels::page>
