@extends('layouts.public')

@section('content')
<!-- Main Layout -->
<div class="flex flex-1 justify-center py-5 px-4 md:px-10 lg:px-40">
    <div class="layout-content-container flex flex-col max-w-[1200px] flex-1">
        <!-- Breadcrumbs -->
        <nav aria-label="Breadcrumb" class="flex flex-wrap gap-2 px-4 py-2">
            <a class="text-text-secondary dark:text-gray-400 text-sm font-medium leading-normal hover:text-primary transition-colors" href="{{ url('/') }}">Home</a>
            <span class="text-text-secondary dark:text-gray-400 text-sm font-medium leading-normal">/</span>
            <span class="text-text-main dark:text-white text-sm font-medium leading-normal">Direktori SPPG</span>
        </nav>
        <!-- Page Heading -->
        <div class="flex flex-wrap justify-between items-end gap-4 p-4">
            <div class="flex min-w-72 flex-col gap-2">
                <h1 class="text-text-main dark:text-white text-4xl font-black leading-tight tracking-[-0.033em]">Daftar Dapur SPPG</h1>
                <p class="text-text-secondary dark:text-gray-400 text-base font-normal leading-normal max-w-2xl">
                    Dapur SPPG yang terdaftar di Kornas Makan Bergizi Muhammadiyah.
                </p>
            </div>
            <!-- Removed 'Add Kitchen' button as this is public view -->
        </div>
        <!-- Filters & Search -->
        <form method="GET" action="{{ route('sppg.public.index') }}" class="flex flex-col gap-4 px-4 py-2">
            <!-- Search Bar -->
            <div class="w-full">
                <label class="flex flex-col h-12 w-full">
                    <div class="flex w-full flex-1 items-stretch rounded-xl h-full shadow-sm bg-surface-light dark:bg-surface-dark border border-[#e6e2de] dark:border-neutral-700 overflow-hidden group focus-within:ring-2 focus-within:ring-primary/50 transition-all">
                        <div class="text-text-secondary flex items-center justify-center pl-4 pr-2">
                            <span class="material-symbols-outlined">search</span>
                        </div>
                        <input name="search" value="{{ request('search') }}" class="flex w-full min-w-0 flex-1 resize-none bg-transparent text-text-main dark:text-white focus:outline-0 border-none h-full placeholder:text-text-secondary px-2 text-base font-normal leading-normal" placeholder="Cari dapur SPPG berdasarkan nama, ID, atau daerah..."/>
                    </div>
                </label>
            </div>
            <!-- Filter Chips -->
            <div class="flex gap-3 flex-wrap items-center">
                <span class="text-sm font-medium text-text-secondary dark:text-gray-400 mr-2">Filters:</span>
                <div class="relative">
                    <select name="province" onchange="this.form.submit()" class="appearance-none flex h-9 shrink-0 items-center justify-center gap-x-2 rounded-lg border border-[#e6e2de] dark:border-neutral-600 bg-surface-light dark:bg-surface-dark pl-3 pr-8 hover:bg-gray-50 dark:hover:bg-neutral-700 transition-colors cursor-pointer text-text-main dark:text-white text-sm font-medium focus:ring-2 focus:ring-primary/50 focus:border-primary focus:outline-none">
                        <option value="">Propinsi: Semua</option>
                        @foreach($provinces as $code => $name)
                            <option value="{{ $code }}" {{ request('province') == $code ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                    <span class="material-symbols-outlined text-text-secondary text-[20px] absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none">expand_more</span>
                </div>
                
                @if(request('search') || request('province'))
                <div class="ml-auto">
                    <a href="{{ route('sppg.public.index') }}" class="text-sm text-primary font-medium hover:underline">Bersihkan</a>
                </div>
                @endif
            </div>
        </form>
        <!-- Data Table -->
        <div class="p-4">
            <div class="w-full">
                <!-- Desktop Table View -->
                <div class="hidden md:block overflow-hidden rounded-xl border border-[#e6e2de] dark:border-neutral-700 bg-surface-light dark:bg-surface-dark shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-[#f5f3f0] dark:bg-[#362e26] border-b border-[#e6e2de] dark:border-neutral-700">
                                    <th class="p-4 text-sm font-semibold text-text-secondary uppercase tracking-wider min-w-[200px]">Nama SPPG</th>
                                    <th class="p-4 text-sm font-semibold text-text-secondary uppercase tracking-wider min-w-[150px]">Lokasi</th>
                                    <th class="p-4 text-sm font-semibold text-text-secondary uppercase tracking-wider text-right">Kapasitas <span class="text-xs normal-case font-normal">(makanan/hari)</span></th>
                                    <th class="p-4 text-sm font-semibold text-text-secondary uppercase tracking-wider">Status</th>
                                    <th class="p-4 text-sm font-semibold text-text-secondary uppercase tracking-wider text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#e6e2de] dark:divide-neutral-700">
                                @forelse($sppgs as $sppg)
                                <tr class="group hover:bg-[#fcfbf9] dark:hover:bg-[#362e26] transition-colors">
                                    <td class="p-4">
                                        <div class="flex flex-col">
                                            <a href="{{ route('sppg.public.show', $sppg) }}" class="text-text-main dark:text-white font-bold text-base hover:text-primary transition-colors">
                                                {{ $sppg->nama_sppg }}
                                            </a>
                                            <span class="text-xs text-text-secondary">ID: {{ $sppg->kode_sppg ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-2 text-text-main dark:text-gray-300 text-sm">
                                            <span class="material-symbols-outlined text-text-secondary text-[18px]">location_on</span>
                                            <span class="truncate max-w-xs">{{ $sppg->alamat ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td class="p-4 text-right">
                                        <span class="text-text-main dark:text-white font-mono font-medium">{{ number_format($sppg->porsi_besar + $sppg->porsi_kecil) }}</span>
                                    </td>
                                    <td class="p-4">
                                        @if($sppg->status === 'Operasional / Siap Berjalan')
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-green-100 dark:bg-green-900/30 px-2.5 py-1 text-xs font-bold text-green-700 dark:text-green-400">
                                            <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                                            Operasional
                                        </span>
                                        @elseif($sppg->status === 'Proses Persiapan')
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-yellow-100 dark:bg-yellow-900/30 px-2.5 py-1 text-xs font-bold text-yellow-700 dark:text-yellow-400">
                                            <span class="h-1.5 w-1.5 rounded-full bg-yellow-500"></span>
                                            Proses Persiapan
                                        </span>
                                        @elseif($sppg->status === 'Verifikasi dan Validasi')
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-100 dark:bg-blue-900/30 px-2.5 py-1 text-xs font-bold text-blue-700 dark:text-blue-400">
                                            <span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span>
                                            Verifikasi
                                        </span>
                                        @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 dark:bg-neutral-700 px-2.5 py-1 text-xs font-bold text-gray-600 dark:text-gray-300">
                                            <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span>
                                            {{ $sppg->status ?? '-' }}
                                        </span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-right">
                                        <a href="{{ route('sppg.public.show', $sppg) }}" class="text-text-secondary hover:text-text-main dark:hover:text-white p-1 rounded hover:bg-gray-100 dark:hover:bg-neutral-700 inline-block">
                                            <span class="material-symbols-outlined">visibility</span>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="p-8 text-center text-text-secondary">
                                        No SPPG units found.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile Card View -->
                <div class="grid grid-cols-1 gap-4 md:hidden">
                    @forelse($sppgs as $sppg)
                    <div class="bg-white dark:bg-surface-dark rounded-xl p-4 border border-[#e6e2de] dark:border-neutral-700 shadow-sm flex flex-col gap-3">
                        <div class="flex flex-col gap-1">
                            <a href="{{ route('sppg.public.show', $sppg) }}" class="text-text-main dark:text-white font-bold text-lg hover:text-primary transition-colors block">
                                {{ $sppg->nama_sppg }}
                            </a>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-xs text-text-secondary">ID: {{ $sppg->kode_sppg ?? '-' }}</span>
                                @if($sppg->status === 'Operasional / Siap Berjalan')
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-green-100 dark:bg-green-900/30 px-2 py-0.5 text-[10px] font-bold text-green-700 dark:text-green-400 shrink-0">
                                    <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                                    Operasional
                                </span>
                                @elseif($sppg->status === 'Proses Persiapan')
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-yellow-100 dark:bg-yellow-900/30 px-2 py-0.5 text-[10px] font-bold text-yellow-700 dark:text-yellow-400 shrink-0">
                                    <span class="h-1.5 w-1.5 rounded-full bg-yellow-500"></span>
                                    Persiapan
                                </span>
                                @elseif($sppg->status === 'Verifikasi dan Validasi')
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-100 dark:bg-blue-900/30 px-2 py-0.5 text-[10px] font-bold text-blue-700 dark:text-blue-400 shrink-0">
                                    <span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span>
                                    Verifikasi
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 dark:bg-neutral-700 px-2 py-0.5 text-[10px] font-bold text-gray-600 dark:text-gray-300 shrink-0">
                                    <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span>
                                    {{ $sppg->status ?? '-' }}
                                </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="flex flex-col gap-2 border-t border-dashed border-gray-200 dark:border-gray-700 pt-3 mt-1">
                            <div class="flex items-center gap-2 text-text-secondary text-sm">
                                <span class="material-symbols-outlined text-[18px] text-gray-400">location_on</span>
                                <span class="truncate">{{ $sppg->alamat ?? '-' }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-text-secondary text-sm">
                                <span class="material-symbols-outlined text-[18px] text-gray-400">soup_kitchen</span>
                                <span>Kapasitas: <strong class="text-text-main dark:text-white">{{ number_format($sppg->porsi_besar + $sppg->porsi_kecil) }}</strong> porsi/hari</span>
                            </div>
                        </div>

                        <div class="mt-1 pt-2">
                             <a href="{{ route('sppg.public.show', $sppg) }}" class="flex items-center justify-center gap-2 w-full py-2.5 rounded-lg bg-gray-50 hover:bg-gray-100 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-sm font-semibold text-text-main dark:text-white transition-colors border border-gray-200 dark:border-neutral-600">
                                <span>Lihat Detail</span>
                                <span class="material-symbols-outlined text-sm">arrow_forward</span>
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="bg-gray-50 dark:bg-neutral-800 rounded-xl p-8 text-center text-text-secondary">
                        No SPPG units found.
                    </div>
                    @endforelse
                </div>
            </div>
            <!-- Pagination -->
            <div class="mt-4">
                 {{ $sppgs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
