<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="icon">
            <x-filament::icon icon="heroicon-o-home-modern" />
        </x-slot>
        <x-slot name="heading">
            SPPG Anda
        </x-slot>
        <x-filament::input.wrapper>
            <x-filament::input.select id="status" wire:model="status">
                <option value="draft">Draft</option>
                <option value="published">Published</option>
                <option value="archived">Archived</option>
            </x-filament::input.select>
        </x-filament::input.wrapper>
    </x-filament::section>
</x-filament-widgets::widget>
