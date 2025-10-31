<x-filament-panels::page>
    {{-- Page content --}}

    @if ($record)
        <div class="">
            <p>
                Menu hari {{ Carbon\Carbon::parse($record->tanggal)->locale('id')->translatedFormat('l, d F Y') }}
            </p>
            <p class="mt-4">
                {{ $record->menu_hari_ini }}
            </p>
            <p class="mt-4">
                Jumlah Porsi : {{ $record->jumlah }}
            </p>
        </div>

        <div>
            <p class="text-2xl">
                Daftar sekolah penerima makanan
            </p>
            <div class="mt-8">
                <ul>
                    @foreach ($record->sppg->schools as $item)
                        <li class="mb-2">
                            <p>{{ $item->nama_sekolah }}</p>
                            <p>{{ $item->alamat }}</p>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        @if ($this->isEditable)
            <x-filament::button wire:click="save" class="mt-4">
                Selesaikan Pengiriman.
            </x-filament::button>
        @else
            <x-filament::badge color="warning" class="mt-4">
                Distribusi makanan selesai.
            </x-filament::badge>
        @endif
    @else
        <x-filament::badge color="warning" class="mt-4">
            Belum ada makanan yang siap didistribusikan.
        </x-filament::badge>
    @endif




</x-filament-panels::page>
