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
    <div style="display: flex !important; flex-direction: row !important; align-items: center !important; gap: 8px !important; margin-left: 12px !important; margin-right: 8px !important;">
        @foreach ($panels as $panel)
            <a href="{{ $panel['url'] }}" 
               title="Pindah ke {{ $panel['label'] }}"
               style="display: flex !important; align-items: center !important; justify-content: center !important; color: #6b7280 !important; background-color: #f3f4f6 !important; padding: 6px !important; border-radius: 8px !important; border: 1px solid #e5e7eb !important; transition: all 0.2s !important;"
               onmouseover="this.style.backgroundColor='#e5e7eb'"
               onmouseout="this.style.backgroundColor='#f3f4f6'">
                <x-filament::icon
                    :icon="$panel['icon']"
                    class="h-5 w-5"
                    style="width: 20px !important; height: 20px !important;"
                />
            </a>
        @endforeach
    </div>
@endif
