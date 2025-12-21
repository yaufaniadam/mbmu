<x-filament-panels::page>
    {{-- Page content --}}

    {{ $this->form }}
    <x-filament::button wire:click="save" class="w-full">
        Simpan
    </x-filament::button>
</x-filament-panels::page>
