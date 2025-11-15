<x-filament-panels::page>
    {{-- Page content --}}

    <div class="flex justify-center">
        <p class="text-2xl font-semibold">
            {{ Carbon\Carbon::parse($record->tanggal)->locale('id')->translatedFormat('l, d F Y') }}
        </p>
    </div>

    <div class="flex justify-center">
        @if ($record->status === 'Direncanakan')
            <p class="font-light border border-blue-300 p-2 rounded-lg bg-blue-950">
                Menunggu Verifikasi
            </p>
        @endif

        @if ($record->status === 'Terverifikasi')
            <p class="font-light border border-blue-300 p-2 rounded-lg bg-blue-950">
                Siap didistribusikan
            </p>
        @endif

        @if ($record->status === 'Ditolak')
            <p class="font-light border border-red-300 p-2 rounded-lg bg-red-950">
                Produk pangan ditolak
            </p>
        @endif

        @if ($record->status === 'Didistribusikan')
            <p class="font-light border border-emerald-300 p-2 rounded-lg bg-emerald-950">
                Pengantaran selesai
            </p>
        @endif
    </div>

    <div>
        <div class="grid grid-cols-7 gap-x-2 gap-y-1">

            <span class="col-span-3">Daftar Menu :</span>
            <span class="col-span-4 break-words"> {{ $record->menu_hari_ini }}
            </span>

            <span class="col-span-3">Jumlah Porsi Besar :</span>
            <span class="col-span-4">
                {{ $record->getTotalPorsiBesarAttribute() }} Porsi
            </span>

            <span class="col-span-3">Jumlah Porsi Besar :</span>
            <span class="col-span-4">
                {{ $record->getTotalPorsiKecilAttribute() }} Porsi
            </span>

        </div>

        @if ($record->status === 'Ditolak')
            <div class="flex flex-col mt-4">
                <span>
                    Alasan :
                </span>
                <p class="mt-2 border border-red-300 bg-red-950 p-4 rounded-lg">
                    {{ $record->verification->catatan }}
                </p>
            </div>
        @endif
    </div>

    {{ $this->form }}
    @if ($this->isEditable)
        <x-filament::button wire:click="save" class="mt-4">
            Simpan
        </x-filament::button>
    @endif
</x-filament-panels::page>
