<x-filament-panels::page>
    {{-- Summary Cards (Moved to Top) --}}
    @if(count($attendances) > 0 || !empty($search))
        @php
            // We need to calculate summary based on ALL records, not just filtered ones if possible, 
            // but for now let's show summary of what's visible or maybe pass full counts from backend?
            // For simplicity and accuracy with search, let's recalculate or just use visible.
            // Actually user probably wants summary of the day.
            // Let's stick to visible for now or improve later if requested.
            $summary = collect($attendances)->countBy('status');
        @endphp
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
             <div class="fi-wi-stats-overview-stat relative rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="grid gap-y-1">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Hadir</span>
                    <div class="text-2xl font-bold text-green-600 dark:text-green-500">
                        {{ $summary->get('Hadir', 0) }}
                    </div>
                </div>
            </div>
             <div class="fi-wi-stats-overview-stat relative rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="grid gap-y-1">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Izin</span>
                    <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-500">
                        {{ $summary->get('Izin', 0) }}
                    </div>
                </div>
            </div>
             <div class="fi-wi-stats-overview-stat relative rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="grid gap-y-1">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Sakit</span>
                    <div class="text-2xl font-bold text-orange-600 dark:text-orange-500">
                        {{ $summary->get('Sakit', 0) }}
                    </div>
                </div>
            </div>
             <div class="fi-wi-stats-overview-stat relative rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="grid gap-y-1">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Alpha</span>
                    <div class="text-2xl font-bold text-red-600 dark:text-red-500">
                        {{ $summary->get('Alpha', 0) }}
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Toolbar: Date Filter & Search --}}
    <div class="flex flex-col md:flex-row items-center justify-between gap-4 p-4 bg-white dark:bg-gray-900 rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10">
        <div class="flex items-center gap-4 w-full md:w-auto">
            <div class="w-full md:w-64">
                <label class="text-sm font-medium text-gray-700 dark:text-gray-200 block mb-1">Tanggal Presensi</label>
                <input 
                    type="date" 
                    wire:model.live="selected_date" 
                    wire:change="loadAttendances"
                    class="block w-full rounded-lg border-gray-300 shadow-sm transition duration-75 focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70 dark:bg-gray-900 dark:border-gray-600 dark:text-white dark:focus:border-primary-500"
                />
            </div>
        </div>
        
        <div class="w-full md:w-80">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-200 block mb-1 invisible">Cari</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <x-heroicon-m-magnifying-glass class="w-5 h-5 text-gray-400" />
                </div>
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari nama relawan..."
                    class="block w-full rounded-lg border-gray-300 pl-10 shadow-sm transition duration-75 focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70 dark:bg-gray-900 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 dark:focus:border-primary-500"
                />
            </div>
        </div>
    </div>

    <div class="fi-ta-ctn divide-y divide-gray-200 overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:divide-white/10 dark:bg-gray-900 dark:ring-white/10">
        @if(count($attendances) > 0)
            <div class="fi-ta-content relative divide-y divide-gray-200 dark:divide-white/10 overflow-x-auto">
                {{-- Table content --}}
                <table class="fi-ta-table w-full table-auto divide-y divide-gray-200 text-start dark:divide-white/5">
                    <thead class="bg-gray-50 dark:bg-white/5">
                        <tr>
                            <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 text-start text-sm font-semibold text-gray-950 dark:text-white">
                                #
                            </th>
                            <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 text-start text-sm font-semibold text-gray-950 dark:text-white">
                                Nama Relawan
                            </th>
                            <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 text-start text-sm font-semibold text-gray-950 dark:text-white">
                                Posisi
                            </th>
                            <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 text-center text-sm font-semibold text-gray-950 dark:text-white" style="min-width: 400px;">
                                Status Kehadiran
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 whitespace-nowrap dark:divide-white/5">
                        @foreach($attendances as $index => $attendance)
                            <tr class="fi-ta-row [@media(hover:hover)]:transition [@media(hover:hover)]:duration-75 hover:bg-gray-50 dark:hover:bg-white/5">
                                <td class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3">
                                    <div class="fi-ta-col-wrp">
                                        <div class="flex w-full disabled:pointer-events-none justify-start text-start">
                                            <div class="fi-ta-text grid gap-y-1 px-3 py-4">
                                                <div class="text-sm leading-6 text-gray-950 dark:text-white">
                                                    {{ $index + 1 }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3">
                                    <div class="fi-ta-col-wrp">
                                       <div class="flex w-full disabled:pointer-events-none justify-start text-start">
                                            <div class="fi-ta-text grid gap-y-1 px-3 py-4">
                                                <div class="text-sm font-medium leading-6 flex items-center gap-2 {{ ($attendance['is_recorded'] ?? false) ? 'text-gray-950 dark:text-white' : 'text-red-600 dark:text-red-400 font-bold' }}">
                                                     {{ $attendance['nama_relawan'] }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3">
                                     <div class="fi-ta-col-wrp">
                                        <div class="flex w-full disabled:pointer-events-none justify-start text-start">
                                            <div class="px-3 py-4">
                                                <x-filament::badge color="info">
                                                    {{ $attendance['posisi'] }}
                                                </x-filament::badge>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3">
                                    <div class="px-3 py-4 flex justify-center">
                                        <div class="flex items-center justify-center gap-2 p-2 rounded-lg border-2 border-pink-300 bg-pink-50 dark:bg-pink-900/20 dark:border-pink-700">
                                            @foreach(['Hadir' => '✓', 'Izin' => '📝', 'Sakit' => '🏥', 'Alpha' => '✗'] as $status => $icon)
                                                <label class="flex items-center gap-2 cursor-pointer px-3 py-1.5 rounded-md transition-all
                                                    {{ $attendance['status'] === $status ? 
                                                        ($status === 'Hadir' ? 'bg-green-500 text-white font-semibold shadow-sm' : 
                                                        ($status === 'Izin' ? 'bg-yellow-500 text-white font-semibold shadow-sm' : 
                                                        ($status === 'Sakit' ? 'bg-orange-500 text-white font-semibold shadow-sm' : 
                                                        'bg-red-500 text-white font-semibold shadow-sm'))) 
                                                        : 'bg-white text-gray-700 hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700' 
                                                    }}">
                                                    <input 
                                                        type="radio" 
                                                        name="status_{{ $index }}"
                                                        wire:click="updateStatus({{ $index }}, '{{ $status }}')"
                                                        @checked($attendance['status'] === $status)
                                                        class="sr-only" 
                                                    />
                                                    <span class="text-sm whitespace-nowrap">{{ $icon }} {{ $status }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="fi-ta-empty-state px-6 py-12">
                <div class="fi-ta-empty-state-content mx-auto grid max-w-lg justify-items-center text-center">
                    <div class="fi-ta-empty-state-icon-ctn mb-4 rounded-full bg-gray-100 p-3 dark:bg-gray-500/20">
                        <x-heroicon-o-users class="fi-ta-empty-state-icon h-6 w-6 text-gray-500 dark:text-gray-400" />
                    </div>
                    <h4 class="fi-ta-empty-state-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                        {{ !empty($search) ? 'Tidak ditemukan relawan dengan nama "' . $search . '"' : 'Tidak ada relawan yang terdaftar' }}
                    </h4>
                    @if(empty($search))
                    <p class="fi-ta-empty-state-description text-sm text-gray-500 dark:text-gray-400 mt-1">
                         Tambahkan relawan terlebih dahulu di menu Data Master untuk mulai mengisi presensi.
                    </p>
                    @endif
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
