@extends('layouts.public')

@section('content')
<!-- Top Navigation (Breadcrumbs mostly) -->
<header class="sticky top-0 z-40 w-full bg-card-light dark:bg-card-dark border-b border-slate-200 dark:border-slate-800 shadow-sm hidden md:block">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 gap-4">
             <!-- Breadcrumbs moved here for desktop header feel or keep in main -->
             <div class="flex items-center gap-4">
                <a href="{{ route('sppg.public.index') }}" class="flex items-center gap-2 text-primary font-bold">
                    <span class="material-symbols-outlined">arrow_back</span>
                    Kembali ke Direktori
                </a>
             </div>
        </div>
    </div>
</header>

<main class="flex-grow w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-8">
    <!-- Breadcrumbs (Mobile) -->
    <nav aria-label="Breadcrumb" class="flex md:hidden">
        <ol class="flex items-center space-x-2">
            <li>
                <a class="text-slate-500 dark:text-slate-400 hover:text-primary" href="{{ url('/') }}">Home</a>
            </li>
            <li><span class="text-slate-400">/</span></li>
            <li>
                <a class="text-slate-500 dark:text-slate-400 hover:text-primary" href="{{ route('sppg.public.index') }}">Kitchen Units</a>
            </li>
            <li><span class="text-slate-400">/</span></li>
            <li>
                <span aria-current="page" class="text-slate-900 dark:text-white font-medium truncate max-w-[150px]">{{ $sppg->nama_sppg }}</span>
            </li>
        </ol>
    </nav>

    <!-- Hero Section -->
    <div class="relative w-full rounded-2xl overflow-hidden shadow-md group">
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent z-10"></div>
        <div class="h-64 sm:h-80 md:h-96 w-full bg-cover bg-center transition-transform duration-700 group-hover:scale-105" 
             style="background-image: url('{{ $sppg->photo_path ? Storage::disk('public')->url($sppg->photo_path) : 'https://lh3.googleusercontent.com/aida-public/AB6AXuDw4vpMOupvk-105x4TP-eU7uSMwm6KYsbhUJ3kNzrrV45qTqs71Zz1eK_GPddtRghHsT9Qf8J2N6FSeelPAzZxgiqiGl3unwiq9wrJvxKouRC40LBqnnPUguVFTI-OEFU_1sNGywlLFII_r8aOjwiG4WtZfdixkMo13mfw5OWIqWQzaj53Hrw7Jxuxr2DMgt3FAv4_TKQswQJOK7wQHgCYFZkYi4_fqfRmsCoiSl28y1VWfstg_-aX0hWZjJOcv2hxqrFC-VdjO6PH' }}');">
        </div>
        <div class="absolute bottom-0 left-0 right-0 p-6 sm:p-8 z-20 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    @if($sppg->is_active)
                    <span class="inline-flex items-center gap-1 rounded-full bg-green-500/20 backdrop-blur-sm px-3 py-1 text-xs font-semibold text-green-100 border border-green-500/30">
                        <span class="material-symbols-outlined text-[16px] text-green-400">check_circle</span>
                        Operational
                    </span>
                    @else
                    <span class="inline-flex items-center gap-1 rounded-full bg-red-500/20 backdrop-blur-sm px-3 py-1 text-xs font-semibold text-red-100 border border-red-500/30">
                        <span class="material-symbols-outlined text-[16px] text-red-400">cancel</span>
                        Inactive
                    </span>
                    @endif
                    <span class="inline-flex items-center gap-1 rounded-full bg-white/10 backdrop-blur-sm px-3 py-1 text-xs font-semibold text-white border border-white/20">
                        ID: {{ $sppg->kode_sppg }}
                    </span>
                </div>
                <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold text-white tracking-tight">{{ $sppg->nama_sppg }}</h1>
                <p class="text-slate-200 mt-2 max-w-2xl text-sm sm:text-base font-light">
                    {{ $sppg->alamat ?? 'Lokasi tidak tersedia.' }}
                </p>
            </div>
        </div>
    </div>

    <!-- Key Metrics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- Capacity -->
        <div class="bg-card-light dark:bg-card-dark rounded-xl p-6 shadow-sm border border-slate-100 dark:border-slate-800 flex flex-col justify-between group hover:border-primary/50 transition-all">
            <div class="flex justify-between items-start mb-4">
                <div class="p-2 bg-orange-50 dark:bg-orange-900/20 rounded-lg text-primary">
                    <span class="material-symbols-outlined text-2xl">soup_kitchen</span>
                </div>
            </div>
            <div>
                <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Daily Capacity</p>
                <h3 class="text-slate-900 dark:text-white text-2xl font-bold mt-1">
                    {{ number_format($sppg->porsi_besar + $sppg->porsi_kecil) }} Meals
                </h3>
                <p class="text-xs text-slate-500 mt-1">
                    (L: {{ number_format($sppg->porsi_besar) }}, S: {{ number_format($sppg->porsi_kecil) }})
                </p>
            </div>
        </div>
        <!-- Staff -->
        <div class="bg-card-light dark:bg-card-dark rounded-xl p-6 shadow-sm border border-slate-100 dark:border-slate-800 flex flex-col justify-between group hover:border-primary/50 transition-all">
            <div class="flex justify-between items-start mb-4">
                <div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg text-blue-500">
                    <span class="material-symbols-outlined text-2xl">groups</span>
                </div>
            </div>
            <div>
                <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Active Staff</p>
                <h3 class="text-slate-900 dark:text-white text-2xl font-bold mt-1">{{ number_format($sppg->volunteers()->count()) }}</h3>
                <p class="text-xs text-slate-500 mt-1">Volunteers</p>
            </div>
        </div>
        <!-- Status -->
        <div class="bg-card-light dark:bg-card-dark rounded-xl p-6 shadow-sm border border-slate-100 dark:border-slate-800 flex flex-col justify-between group hover:border-primary/50 transition-all">
            <div class="flex justify-between items-start mb-4">
                <div class="p-2 bg-purple-50 dark:bg-purple-900/20 rounded-lg text-purple-500">
                    <span class="material-symbols-outlined text-2xl">verified</span>
                </div>
            </div>
            <div>
                <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Grade</p>
                <h3 class="text-slate-900 dark:text-white text-2xl font-bold mt-1">{{ $sppg->grade ?? 'N/A' }}</h3>
            </div>
        </div>
        <!-- Location -->
        <div class="bg-card-light dark:bg-card-dark rounded-xl p-6 shadow-sm border border-slate-100 dark:border-slate-800 flex flex-col justify-between group hover:border-primary/50 transition-all">
            <div class="flex justify-between items-start mb-4">
                <div class="p-2 bg-rose-50 dark:bg-rose-900/20 rounded-lg text-rose-500">
                    <span class="material-symbols-outlined text-2xl">location_on</span>
                </div>
            </div>
            <div>
                <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Region</p>
                <h3 class="text-slate-900 dark:text-white text-2xl font-bold mt-1">{{ $sppg->city?->name ?? 'Unknown' }}</h3>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8">
        <!-- Left Column: Gallery & Details (Span 2) -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Gallery Section -->
            <div class="bg-card-light dark:bg-card-dark rounded-xl p-6 shadow-sm border border-slate-100 dark:border-slate-800">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white">Facility Visuals</h3>
                </div>
                <div class="flex flex-col gap-4">
                    <!-- Main Preview -->
                    <div class="w-full aspect-video rounded-lg overflow-hidden bg-slate-100 dark:bg-slate-800 relative">
                         @if($sppg->photo_path)
                            <img src="{{ Storage::disk('public')->url($sppg->photo_path) }}" alt="{{ $sppg->nama_sppg }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-slate-400 bg-slate-200 dark:bg-slate-700">
                                <span class="material-symbols-outlined text-6xl">image_not_supported</span>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Gallery Grid -->
                    @if($sppg->gallery_photos && count($sppg->gallery_photos) > 0)
                    <div class="grid grid-cols-3 sm:grid-cols-4 gap-2 mt-4">
                        @foreach($sppg->gallery_photos as $photo)
                        <div class="aspect-square rounded-lg overflow-hidden bg-slate-100 dark:bg-slate-800 cursor-pointer hover:opacity-80 transition-opacity">
                             <img src="{{ Storage::disk('public')->url($photo) }}" alt="Gallery Image" class="w-full h-full object-cover" onclick="window.open(this.src, '_blank')">
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column: Sidebar (Span 1) -->
        <div class="space-y-6">
            <!-- Contact Person Card -->
            <div class="bg-card-light dark:bg-card-dark rounded-xl p-6 shadow-sm border border-slate-100 dark:border-slate-800">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Contact Person</h3>
                <div class="flex items-center gap-4 mb-6">
                    <div class="size-16 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center overflow-hidden border-2 border-slate-100 dark:border-slate-700">
                         <span class="material-symbols-outlined text-4xl text-slate-400">person</span>
                    </div>
                    <div>
                        <p class="text-slate-900 dark:text-white font-bold text-lg leading-tight">{{ $sppg->pjSppg->name ?? 'Belum Ditentukan' }}</p>
                        <p class="text-slate-500 dark:text-slate-400 text-sm">Penanggung Jawab</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <button class="flex justify-center items-center gap-2 bg-primary hover:bg-primary-dark text-white py-2.5 px-4 rounded-lg text-sm font-medium transition-colors">
                        <span class="material-symbols-outlined text-[18px]">call</span>
                        Call
                    </button>
                    <button class="flex justify-center items-center gap-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 py-2.5 px-4 rounded-lg text-sm font-medium transition-colors">
                        <span class="material-symbols-outlined text-[18px]">mail</span>
                        Email
                    </button>
                </div>
            </div>
            
            <!-- Location Map Card -->
            <div class="bg-card-light dark:bg-card-dark rounded-xl p-6 shadow-sm border border-slate-100 dark:border-slate-800">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Location</h3>
                     @if($sppg->latitude && $sppg->longitude)
                    <a class="text-primary text-xs font-medium hover:underline flex items-center gap-1" href="https://www.google.com/maps/search/?api=1&query={{ $sppg->latitude }},{{ $sppg->longitude }}" target="_blank">
                        Open Maps
                        <span class="material-symbols-outlined text-[14px]">open_in_new</span>
                    </a>
                    @endif
                </div>
                <!-- Map Container -->
                <div id="map" class="aspect-square w-full rounded-lg overflow-hidden bg-slate-200 dark:bg-slate-700 mb-4 relative z-0"></div>
                
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-slate-400 mt-1">pin_drop</span>
                    <div>
                        <p class="text-slate-900 dark:text-white font-medium text-sm">{{ $sppg->nama_sppg }}</p>
                        <p class="text-slate-500 dark:text-slate-400 text-sm mt-0.5 leading-relaxed">
                            {{ $sppg->alamat }}<br/>
                             @if($sppg->city) {{ $sppg->city->name }}, @endif
                             @if($sppg->province) {{ $sppg->province->name }} @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    .leaflet-control-attribution a { text-decoration: none; }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Default to Indonesia center if no data
        var lat = {{ $sppg->latitude ?? -2.5489 }};
        var lng = {{ $sppg->longitude ?? 118.0149 }};
        var zoom = {{ ($sppg->latitude && $sppg->longitude) ? 15 : 5 }};
        
        var map = L.map('map').setView([lat, lng], zoom);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        @if($sppg->latitude && $sppg->longitude)
            L.marker([lat, lng]).addTo(map)
                .bindPopup("<b>{{ $sppg->nama_sppg }}</b><br>{{ $sppg->alamat }}")
                .openPopup();
        @else
            // Fallback: Try to geocode if no coordinates
            var query = "{{ $sppg->nama_sppg }}, {{ $sppg->city?->name }}, {{ $sppg->province?->name }}";
            fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(query))
                .then(response => response.json())
                .then(data => {
                    if (data && data.length > 0) {
                        var result = data[0];
                        var newLat = result.lat;
                        var newLon = result.lon;
                        map.setView([newLat, newLon], 15);
                        L.marker([newLat, newLon]).addTo(map)
                            .bindPopup("<b>{{ $sppg->nama_sppg }}</b><br>{{ $sppg->alamat }}");
                    }
                })
                .catch(err => console.log('Geocoding failed', err));
        @endif
    });
</script>
@endpush
@endsection
