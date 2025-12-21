<x-filament-widgets::widget>

    <x-filament::section>
        <x-slot name="icon">
            <x-filament::icon icon="heroicon-o-home-modern" />
        </x-slot>
        <x-slot name="heading">
            SPPG Anda
        </x-slot>
        <x-slot name="description">
            {{ $sppg->nama_sppg }}
        </x-slot>
    </x-filament::section>
</x-filament-widgets::widget>
