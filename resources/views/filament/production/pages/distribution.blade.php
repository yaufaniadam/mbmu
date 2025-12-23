@php
    // 1. Tambahkan ini di bagian atas file blade Anda
    use App\Filament\Production\Pages\Delivery;
@endphp

<x-filament-panels::page>
    {{-- Pending Pickups Section --}}
    @if ($pendingPickups && $pendingPickups->isNotEmpty())
        <div class="mb-6">
            <div class="mb-2 bg-amber-900 p-4 rounded-lg border border-amber-600">
                <p class="text-lg font-semibold text-amber-200">
                    🥄 Alat Makan Siap Dijemput ({{ $pendingPickups->count() }})
                </p>
            </div>
            <ul>
                @foreach ($pendingPickups as $item)
                    <li class="bg-amber-950 border border-amber-700 rounded-lg p-4 mb-2">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="font-semibold text-amber-200">{{ $item->school->nama_sekolah }}</p>
                                <p class="text-sm text-amber-300">{{ $item->school->alamat }}</p>
                                <div class="flex gap-2 mt-2">
                                    <x-filament::badge color="success">
                                        Terkirim {{ $item->delivered_at?->format('H:i') }}
                                    </x-filament::badge>
                                    <x-filament::badge color="warning">
                                        {{ $item->pickup_status }}
                                    </x-filament::badge>
                                </div>
                            </div>
                            <div>
                                <x-filament::button tag="a"
                                    href="{{ Delivery::getUrl(['distribution' => $item->id]) }}" 
                                    size="sm"
                                    color="warning">
                                    Jemput
                                </x-filament::button>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

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
                Daftar penerima MBM :
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
    @elseif (!$pendingPickups || $pendingPickups->isEmpty())
        <x-filament::empty-state icon="heroicon-o-truck">
            <x-slot name="heading">
                Belum ada makanan yang siap distribusikan.
            </x-slot>
        </x-filament::empty-state>
    @endif
</x-filament-panels::page>
