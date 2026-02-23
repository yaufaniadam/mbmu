@php
    $user = auth()->user();
    if (!$user) return;

    $currentPanelId = filament()->getCurrentPanel()->getId();
    $panels = [];

    // Check availability of other panels for this user
    if ($currentPanelId !== 'sppg' && $user->canAccessPanel(filament()->getPanel('sppg'))) {
        $panels[] = [
            'id' => 'sppg',
            'label' => 'Panel SPPG',
            'url' => url('/sppg'),
            'icon' => 'heroicon-m-building-office-2',
        ];
    }

    if ($currentPanelId !== 'lembaga' && $user->canAccessPanel(filament()->getPanel('lembaga'))) {
        $panels[] = [
            'id' => 'lembaga',
            'label' => 'Panel Lembaga',
            'url' => url('/lembaga'),
            'icon' => 'heroicon-m-briefcase',
        ];
    }

    
    if ($currentPanelId !== 'production' && $user->canAccessPanel(filament()->getPanel('production'))) {
        $panels[] = [
            'id' => 'production',
            'label' => 'Panel Produksi',
            'url' => url('/production'),
            'icon' => 'heroicon-m-clipboard-document-check',
        ];
    }
    
    // Add Admin if applicable and not already in it

    if ($currentPanelId !== 'admin' && $user->canAccessPanel(filament()->getPanel('admin'))) {
        $panels[] = [
            'id' => 'admin',
            'label' => 'Panel Admin',
            'url' => url('/admin'),
            'icon' => 'heroicon-m-shield-check',
        ];
    }
@endphp

@if (count($panels) > 0)
    <div class="flex items-center gap-x-3 px-3">
        @foreach ($panels as $panel)
            <a href="{{ $panel['url'] }}" 
               title="Pindah ke {{ $panel['label'] }}"
               class="flex items-center justify-center text-gray-500 hover:text-primary-600 dark:text-gray-400 dark:hover:text-primary-400 transition-colors duration-200 bg-gray-100 dark:bg-gray-800 p-2 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                <x-filament::icon
                    :icon="$panel['icon']"
                    class="h-5 w-5"
                />
            </a>

        @endforeach
    </div>
@endif
