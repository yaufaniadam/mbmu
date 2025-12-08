<x-filament-panels::page>
    {{-- Page content --}}
    {{ $this->form }}
    <div class="flex flex-col justify-between pb-24">
        <div>
            <div class="flex w-full bg-emerald-300 rounded-2xl">
                <div class="p-6 flex flex-col">
                    <span class="text-slate-800 font-semibold">Dikirim ke</span>
                    <span class="text-black font-bold text-2xl">{{ $record->school->alamat }}</span>
                </div>
            </div>

            <div class="flex flex-col w-full bg-slate-700 rounded-2xl mt-6">
                <div class="p-6 flex flex-col">
                    <span class="font-bold text-slate-100 text-2xl">Menu :</span>

                    <p class="font-semibold text-slate-100">{{ $record->productionSchedule->menu_hari_ini }}</p>
                </div>
                <div class="mt-2 px-6 pb-6 flex flex-col gap-1">
                    <div class="flex justify-between">
                        <span>Porsi Besar</span>
                        <span>{{ $record->jumlah_porsi_besar }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Porsi Kecil</span>
                        <span>{{ $record->jumlah_porsi_kecil }}</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-col w-full bg-slate-700 rounded-2xl mt-6">
                <div class="p-6 flex flex-col">
                    <span class="font-bold text-slate-100 text-2xl">Status Pengantaran :</span>

                    <p class="font-semibold text-slate-100">{{ $record->status_pengantaran }}</p>
                </div>
            </div>
        </div>
        <div class="flex mt-4 w-full">
            @if ($record->status_pengantaran === 'Menunggu')
                <x-filament::button wire:click="save" class="w-full">
                    Antarkan
                </x-filament::button>
            @elseif ($record->status_pengantaran === 'Sedang Dikirim')
                <x-filament::button wire:click="save" class="w-full">
                    Selesaikan Pengantaran
                </x-filament::button>
            @endif

        </div>
    </div>
</x-filament-panels::page>
