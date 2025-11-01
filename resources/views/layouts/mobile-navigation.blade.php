{{-- resources/views/filament/layouts/mobile-navigation.blade.php --}}
<x-filament-panels::layout>
    {{-- Preserve the default Filament head, styles, and scripts --}}
    @vite('resources/css/app.css')
    @filamentStyles
    @filamentScripts

    {{-- Optional: your own custom mobile-specific CSS --}}
    <style>
        @media (max-width: 1024px) {
            .fi-topbar-open-sidebar-btn {
                display: none !important;
            }

            .fi-topbar {
                display: none !important;
            }

            aside {
                display: none !important;
            }
        }
    </style>

    {{-- HEADER / NAVBAR --}}
    <header class="flex items-center justify-between p-4 bg-primary-600 text-white">
        <h1 class="text-lg font-semibold">
            {{ filament()->getCurrentPanel()->getBrandName() ?? config('app.name') }}
        </h1>
    </header>

    {{-- MAIN CONTENT AREA --}}
    <main class="p-4">
        {{ $slot }}
    </main>

    {{-- MOBILE NAV DRAWER --}}
    {{-- Mobile Bottom Navigation --}}
    <nav
        class="lg:hidden fixed bottom-0 left-0 right-0 z-50 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 shadow-lg">
        <div class="flex items-center justify-around h-20">
            @foreach (filament()->getNavigation() as $group)
                @foreach ($group->getItems() as $item)
                    <a href="{{ $item->getUrl() }}" @class([
                        'relative flex flex-col items-center justify-center flex-1 h-full gap-1 transition-colors px-2',
                        'text-orange-600 dark:text-orange-400' => $item->isActive(),
                        'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100' => !$item->isActive(),
                    ]) wire:navigate>
                        {{-- Icon --}}
                        @if ($item->getIcon())
                            <x-filament::icon :icon="$item->getIcon()" class="w-6 h-6" />
                        @endif

                        {{-- Label --}}
                        <span class="text-xs font-medium text-center line-clamp-1">
                            {{ $item->getLabel() }}
                        </span>

                        {{-- Badge --}}
                        @if ($badge = $item->getBadge())
                            <span
                                class="absolute top-2 right-2 flex items-center justify-center min-w-[20px] h-5 px-1.5 text-[10px] font-bold bg-red-500 text-white rounded-full animate-pulse">
                                {{ $badge }}
                            </span>
                        @endif
                    </a>
                @endforeach
            @endforeach
        </div>
    </nav>

</x-filament-panels::layout>
