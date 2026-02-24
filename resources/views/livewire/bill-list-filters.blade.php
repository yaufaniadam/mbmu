<div class="fi-ta-header-actions flex gap-2">
    @foreach ($options as $key => $data)
        <x-filament::button :color="in_array($key, $selected) ? $data['color'] : 'gray'" :outlined="!in_array($key, $selected)" size="sm"
            wire:click="$dispatch('filter-{{ $key }}')">
            <x-filament::icon :icon="$data['icon']" class="w-4 h-4" />
            {{ $data['label'] }}
        </x-filament::button>
    @endforeach
</div>
