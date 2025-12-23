<x-filament-panels::page>
    {{-- Page content --}}
    @if ($this->record)
        {{-- Call the specifically named infolist --}}
        {{ $this->infolist }}
    @else
        <x-filament::empty-state icon="heroicon-o-truck" heading="Belum ada makanan yang siap didistribusikan." />
    @endif
</x-filament-panels::page>
