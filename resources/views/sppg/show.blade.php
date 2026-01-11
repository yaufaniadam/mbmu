@extends('layouts.public')

@section('content')
<div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen font-sans">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb / Back Link -->
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('sppg.public.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                         <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                        Daftar SPPG
                    </a>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400">Detail</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl overflow-hidden border border-gray-100 dark:border-gray-700">
            <!-- Header Image -->
            <div class="relative h-64 md:h-96 w-full object-cover">
                 @if($sppg->photo_path)
                    <img src="{{ Storage::url($sppg->photo_path) }}" alt="{{ $sppg->nama_sppg }}" class="w-full h-full object-cover">
                @else
                    <!-- Dummy Image / Placeholder Pattern -->
                    <div class="w-full h-full bg-gradient-to-br from-blue-600 to-indigo-800 flex items-center justify-center">
                        <svg class="w-24 h-24 text-white/20" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path></svg>
                    </div>
                @endif
                <div class="absolute inset-0 bg-black/30"></div>
                
                <!-- Title Overlay (Optional, or put below for cleanliness) -->
                <div class="absolute bottom-0 left-0 p-6 md:p-10 w-full bg-gradient-to-t from-black/80 to-transparent">
                     <span class="px-3 py-1 text-xs font-bold text-white uppercase bg-blue-600 rounded-full mb-2 inline-block shadow-sm tracking-wide">
                        {{ $sppg->kode_sppg }}
                    </span>
                    <h1 class="text-3xl md:text-5xl font-extrabold text-white drop-shadow-md leading-tight">
                        {{ $sppg->nama_sppg }}
                    </h1>
                     <p class="text-gray-200 mt-2 flex items-center gap-2 text-sm md:text-base font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        {{ $sppg->city?->name ?? 'Lokasi Tidak Diketahui' }}
                    </p>
                </div>
            </div>

            <!-- Content Body -->
            <div class="p-6 md:p-10 grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Main Info Channel -->
                <div class="md:col-span-2 space-y-8">
                    
                    <!-- Description / Welcome -->
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                             <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            Tentang SPPG
                        </h2>
                        <div class="prose max-w-none text-gray-600 dark:text-gray-300 leading-relaxed">
                            <p>
                                <strong>{{ $sppg->nama_sppg }}</strong> adalah salah satu Satuan Pelayanan Program Bergizi Muhammadiyah yang berdedikasi untuk menyediakan makanan bergizi dan halal. 
                                @if($sppg->lembagaPengusul)
                                    Diusulkan oleh <strong>{{ $sppg->lembagaPengusul->nama_lembaga }}</strong>, unit ini berkomitmen menjalankan amanah program dengan transparansi dan profesionalitas.
                                @endif
                            </p>
                            <p class="mt-4">
                                Berlokasi di {{ $sppg->alamat ?? 'lokasi strategis' }}, SPPG ini melayani penerima manfaat dengan dukungan fasilitas dan tim yang solid.
                            </p>
                        </div>
                    </div>

                    <!-- Details Grid -->
                     <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-6 border-t border-gray-100 dark:border-gray-700">
                         <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-xl">
                            <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Status Operasional</dt>
                            <dd class="flex items-center gap-2">
                                <span class="relative flex h-3 w-3">
                                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full {{ $sppg->is_active ? 'bg-green-400' : 'bg-red-400' }} opacity-75"></span>
                                  <span class="relative inline-flex rounded-full h-3 w-3 {{ $sppg->is_active ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                </span>
                                <span class="text-sm font-bold {{ $sppg->is_active ? 'text-green-700 dark:text-green-400' : 'text-red-700 dark:text-red-400' }}">
                                    {{ $sppg->is_active ? 'Aktif Beroperasi' : 'Tidak Aktif' }}
                                </span>
                                @if($sppg->status)
                                    <span class="text-xs bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-300 px-2 py-0.5 rounded-full">{{ $sppg->status }}</span>
                                @endif
                            </dd>
                         </div>
                         
                         <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-xl">
                            <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Grade</dt>
                            <dd class="text-lg font-bold text-gray-900 dark:text-white">
                                {{ $sppg->grade ?? 'Belum Terakreditasi' }}
                            </dd>
                         </div>
                     </div>
                </div>

                <!-- Sidebar Info -->
                <div class="md:col-span-1 space-y-6">
                    <!-- Contact Card -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 p-6 rounded-2xl border border-blue-100 dark:border-blue-800">
                        <h3 class="text-lg font-bold text-blue-900 dark:text-blue-200 mb-4">Informasi Kontak</h3>
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-xs text-blue-500 font-semibold uppercase">Penanggung Jawab</dt>
                                <dd class="text-gray-900 dark:text-white font-medium">{{ $sppg->pj->name ?? 'Belum Ditentukan' }}</dd>
                            </div>
                            
                            @if($sppg->email || ($sppg->pj && $sppg->pj->email))
                            <div>
                                <dt class="text-xs text-blue-500 font-semibold uppercase">Email</dt>
                                <dd class="text-gray-900 dark:text-white text-sm break-all">{{ $sppg->email ?? $sppg->pj->email }}</dd>
                            </div>
                            @endif

                             @if($sppg->telepon || ($sppg->pj && $sppg->pj->telepon))
                            <div>
                                <dt class="text-xs text-blue-500 font-semibold uppercase">Telepon/WA</dt>
                                <dd class="text-gray-900 dark:text-white font-medium">{{ $sppg->telepon ?? $sppg->pj->telepon }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>

                    <!-- Location Card -->
                    <div class="p-6 rounded-2xl border border-gray-100 dark:border-gray-700">
                        <h3 class="text-md font-bold text-gray-900 dark:text-white mb-2">Alamat Lengkap</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                            {{ $sppg->alamat }}<br>
                            {{ $sppg->district?->name ? 'Kec. '.$sppg->district->name.',' : '' }} 
                            {{ $sppg->city?->name ? $sppg->city->name.',' : '' }}
                            {{ $sppg->province?->name }}
                        </p>
                        
                        @if($sppg->latitude && $sppg->longitude)
                        <a href="https://www.google.com/maps/search/?api=1&query={{ $sppg->latitude }},{{ $sppg->longitude }}" target="_blank" class="mt-4 inline-flex items-center text-sm text-blue-600 hover:text-blue-800 font-medium">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            Lihat di Google Maps
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>
@endsection
