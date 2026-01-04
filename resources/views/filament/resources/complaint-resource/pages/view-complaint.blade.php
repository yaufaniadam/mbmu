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
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2 font-medium">Isi Pengaduan:</p>
            <p class="whitespace-pre-wrap text-gray-900 dark:text-gray-100">{{ $record->content }}</p>
        </div>
    </x-filament::section>

    {{-- Messages --}}
    <x-filament::section>
        <x-slot name="heading">Percakapan</x-slot>
        
        <div class="space-y-0 divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($record->messages as $message)
                @php
                    $isKornas = $message->user->hasAnyRole(['Superadmin', 'Direktur Kornas', 'Staf Akuntan Kornas', 'Staf Kornas']);
                @endphp
                
                <div class="border-l-4 {{ $isKornas ? 'border-primary-500 bg-primary-50 dark:bg-primary-950' : 'border-gray-300 bg-gray-50 dark:bg-gray-900' }} p-4 rounded-r-lg {{ !$loop->first ? 'mt-4' : '' }}">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="font-semibold text-sm {{ $isKornas ? 'text-primary-700 dark:text-primary-300' : 'text-gray-700 dark:text-gray-300' }}">
                            {{ $message->user->name }}
                        </span>
                        @if($isKornas)
                            <x-filament::badge size="sm" color="primary">Kornas</x-filament::badge>
                        @endif
                        <span class="text-xs text-gray-500 ml-auto">{{ $message->created_at->format('d M Y H:i') }}</span>
                    </div>
                    <p class="text-sm whitespace-pre-wrap text-gray-800 dark:text-gray-200">{{ $message->message }}</p>
                </div>
            @empty
                <div class="text-center text-gray-500 py-8">
                    <p>Belum ada percakapan</p>
                </div>
            @endforelse
        </div>
    </x-filament::section>

    {{-- Reply --}}
    @if($record->status !== 'Closed')
        <x-filament::section>
            <x-slot name="heading">Kirim Balasan</x-slot>
            
            <form wire:submit="sendReply">
                <textarea
                    wire:model="replyMessage"
                    rows="4"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 mb-3"
                    placeholder="Ketik balasan Anda di sini..."
                ></textarea>
                
                <x-filament::button type="submit" icon="heroicon-o-paper-airplane">
                    Kirim Balasan
                </x-filament::button>
            </form>
        </x-filament::section>
    @else
        {{-- Updated: 2026-01-04 13:03 --}}
        <div class="rounded-lg border-l-4 border-gray-400 bg-gray-50 dark:bg-gray-900 p-4">
            <p class="font-semibold text-gray-700 dark:text-gray-300">Tiket Ditutup</p>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Percakapan ini sudah selesai dan tidak dapat menerima balasan baru.</p>
        </div>
    @endif
</x-filament-panels::page>
