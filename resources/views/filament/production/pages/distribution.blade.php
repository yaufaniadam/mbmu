@php
    // 1. Tambahkan ini di bagian atas file blade Anda
    use App\Filament\Production\Pages\Delivery;
@endphp

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
                    Produk pangan sedang didistribusikan
                </p>
            @endif

            @if ($record->status === 'Selesai')
                <p class="font-light border border-emerald-300 p-2 rounded-lg bg-emerald-950">
                    Produk pangan selesai didistribusikan
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
                    {{ $record->total_porsi_besar }} Porsi
                </span>

                <span class="col-span-3">Jumlah Porsi Kecil :</span>
                <span class="col-span-4">
                    {{ $record->total_porsi_kecil }} Porsi
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
                @foreach ($record->distributions as $item)
                    @if ($loop->first)
                        <li class="bg-slate-800 rounded-t-lg p-4 mb-1">
                            <div class="flex justify-between">
                                <div>
                                    <p class="mb-1">{{ $item->school->nama_sekolah }}</p>
                                    <p>{{ $item->school->alamat }}</p>
                                    @if ($item->user_id)
                                        <x-filament::badge color="warning" class="mt-4">
                                            {{ $item->courier->name }}
                                        </x-filament::badge>
                                    @endif
                                </div>
                                <div>
                                    <x-filament::button tag="a"
                                        href="{{ Delivery::getUrl(['distribution' => $item->id]) }}" size="sm">
                                        Antarkan
                                    </x-filament::button>
                                </div>
                            </div>
                        </li>
                    @elseif ($loop->last)
                        <li class="bg-slate-800 rounded-b-lg p-4">
                            <div class="flex justify-between">
                                <div>
                                    <p class="mb-1">{{ $item->school->nama_sekolah }}</p>
                                    <p>{{ $item->school->alamat }}</p>
                                    @if ($item->user_id)
                                        <x-filament::badge color="warning" class="mt-4">
                                            {{ $item->courier->name }}
                                        </x-filament::badge>
                                    @endif
                                </div>
                                <div>
                                    <x-filament::button tag="a"
                                        href="{{ Delivery::getUrl(['distribution' => $item->id]) }}" size="sm">
                                        Antarkan
                                    </x-filament::button>
                                </div>
                            </div>
                        </li>
                    @else
                        <li class="bg-slate-800 p-4 mb-1">
                            <div class="flex justify-between">
                                <div>
                                    <p class="mb-1">{{ $item->school->nama_sekolah }}</p>
                                    <p>{{ $item->school->alamat }}</p>
                                    @if ($item->user_id)
                                        <x-filament::badge color="warning" class="mt-4">
                                            {{ $item->courier->name }}
                                        </x-filament::badge>
                                    @endif
                                </div>
                                <div>
                                    <x-filament::button tag="a"
                                        href="{{ Delivery::getUrl(['distribution' => $item->id]) }}" size="sm">
                                        Antarkan
                                    </x-filament::button>
                                </div>
                            </div>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
    @else
        <x-filament::empty-state icon="heroicon-o-truck">
            <x-slot name="heading">
                Belum ada makanan yang siap distribusikan.
            </x-slot>
        </x-filament::empty-state>
    @endif
</x-filament-panels::page>
