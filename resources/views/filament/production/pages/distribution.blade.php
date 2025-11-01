<x-filament-panels::page>
    {{-- Page content --}}

    @if ($record)
        <div class="flex justify-center">
            <p class="text-2xl font-semibold">
                {{ Carbon\Carbon::parse($record->tanggal)->locale('id')->translatedFormat('l, d F Y') }}
            </p>
        </div>
        <div class="flex justify-center">
            @if ($record->status === 'Terverifikasi')
                <p class="font-light border border-blue-300 p-2 rounded-lg bg-blue-950">
                    Siap didistribusikan
                </p>
            @endif

            @if ($record->status === 'Ditolak')
                <p class="font-light border border-red-300 p-2 rounded-lg bg-red-950">
                    Produk pangan tidak memenuhi kriteria
                </p>
            @endif

            @if ($record->status === 'Didistribusikan')
                <p class="font-light border border-emerald-300 p-2 rounded-lg bg-emerald-950">
                    Pengantaran selesai
                </p>
            @endif
        </div>
        <div>
            <div class="flex justify-between">
                <span>
                    Daftar Menu :
                </span>
                <span>
                    {{ $record->menu_hari_ini }}
                </span>
            </div>

            <div class="flex justify-between">
                <span>
                    Jumlah Porsi :
                </span>
                <span>
                    {{ $record->jumlah }} Porsi
                </span>
            </div>
        </div>
        <div>
            <div class="mb-2 bg-slate-800 p-4 rounded-lg">
                <p class="text-lg">
                    Daftar sekolah penerima :
                </p>
            </div>
            <ul>
                @foreach ($record->sppg->schools as $item)
                    @if ($loop->first)
                        <li class="bg-slate-800 rounded-t-lg p-4 mb-1">
                            <p class="mb-1">{{ $item->nama_sekolah }}</p>
                            <p>{{ $item->alamat }}</p>
                        </li>
                    @elseif ($loop->last)
                        <li class="bg-slate-800 rounded-b-lg p-4">
                            <p class="mb-1">{{ $item->nama_sekolah }}</p>
                            <p>{{ $item->alamat }}</p>
                        </li>
                    @else
                        <li class="bg-slate-800 p-4 mb-1">
                            <p class="mb-1">{{ $item->nama_sekolah }}</p>
                            <p>{{ $item->alamat }}</p>
                        </li>
                    @endif
                @endforeach
            </ul>
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
