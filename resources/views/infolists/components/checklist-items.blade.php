{{-- 
  Di dalam view ini, Anda memiliki akses ke variabel:
  - $getState(): Ini berisi data Anda (array checklist_data)
  - $getRecord(): Ini berisi seluruh model (ProductionSchedule)
--}}
@php
    $items = $getState();
@endphp

@if (is_array($items) && !empty($items))
    <div class="space-y-2">

        {{-- Ini adalah loop @foreach manual Anda! --}}
        <div class="flex items-start space-x-2">

            {{-- 1. Logika Ikon Manual --}}


            <div class="flex-1">
                {{-- 2. Teks Item --}}


                <x-filament::card class="mt-1">
                    @foreach ($items as $item)
                        <div style="margin-bottom: 8px">
                            <div style="display : flex; gap :6px; margin-bottom : 4px">
                                @if (($item['checked'] ?? 'false') == 'true')
                                    {{-- <x-filament::icon icon="heroicon-o-check-circle"
                                    class="h-5 w-5 flex-shrink-0 text-success-600" /> --}}
                                    <x-filament::input.checkbox checked disabled />
                                @else
                                    {{-- <x-filament::icon icon="heroicon-s-squares-2x2"
                                        class="h-5 w-5 flex-shrink-0 text-gray-400" /> --}}
                                    <x-filament::input.checkbox disabled />
                                @endif
                                <span class="text-sm font-medium text-gray-950 dark:text-white">
                                    {{ $item['item_name'] ?? 'Item' }}
                                </span>
                            </div>

                            @if (!empty($item['catatan_item']))
                                <x-filament::section>
                                    <p>
                                        asd
                                        {{ $item['catatan_item'] }}
                                    </p>
                                </x-filament::section>
                            @endif
                        </div>
                    @endforeach

                </x-filament::card>

                {{-- 3. Catatan (Helper Text) Manual --}}

            </div>
        </div>

    </div>
@else
    <span class="text-sm text-gray-500 dark:text-gray-400">
        Tidak ada data ceklis.
    </span>
@endif
