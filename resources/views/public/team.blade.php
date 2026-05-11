@extends('layouts.public')

@section('content')
<div class="flex-1 flex flex-col py-12 lg:py-16">
    <div class="max-w-[1200px] mx-auto px-4 sm:px-6">
        <!-- Hero Section -->
        <section class="mb-16">
            <h1 class="text-4xl md:text-5xl font-bold text-primary mb-4">Struktur Organisasi</h1>
            <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl leading-relaxed">
                Mengarahkan visi dan misi organisasi menuju generasi Indonesia yang sehat, berdaya, dan unggul melalui tata kelola yang transparan dan profesional.
            </p>
            <div class="w-full h-px bg-gray-200 dark:bg-gray-800 mt-8"></div>
        </section>

        @php
            $positionLabels = [
                'direktur' => 'Direktur Utama',
                'wakil_direktur_operasional' => 'Wadir Bidang Operasional',
                'wakil_direktur_keuangan' => 'Wadir Bidang Keuangan',
                'wakil_direktur_investasi' => 'Wadir Bidang Kemitraan',
                'staf' => 'STAF',
                'sekretaris' => 'Sekretaris',
                'bendahara' => 'Bendahara',
            ];
        @endphp

        <!-- Leadership Pyramid -->
        <div class="flex flex-col gap-16">
            <!-- Row 1: Director -->
            @foreach($teamMembers->where('position', 'direktur') as $member)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Empty column for centering on desktop -->
                <div class="hidden md:block"></div>
                
                <!-- Director Card -->
                <div class="bg-white dark:bg-neutral-dark rounded-lg shadow-lg overflow-hidden group hover:translate-y-[-4px] transition-transform duration-300 border border-gray-100 dark:border-gray-800">
                    <div class="aspect-[4/3] w-full bg-gray-100 dark:bg-gray-900 relative overflow-hidden flex items-center justify-center">
                        @if($member->photo_path && Storage::disk('public')->exists($member->photo_path))
                            <img class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-500" alt="{{ $member->name }}" src="{{ Storage::disk('public')->url($member->photo_path) }}"/>
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gray-200 dark:bg-gray-800 text-gray-400 dark:text-gray-600">
                                <svg class="w-1/3 h-1/3 opacity-50" fill="currentColor" viewBox="0 -960 960 960">
                                    <path d="M480-480q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47ZM160-160v-112q0-34 17.5-62.5T224-378q62-31 126-46.5T480-440q66 0 130 15.5T736-378q29 15 46.5 43.5T800-272v112H160Z" />
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">{{ $member->name }}</h3>
                        <p class="text-xs font-medium text-primary uppercase tracking-widest" style="font-size: 10px;">{{ $positionLabels[$member->position] ?? $member->position }}</p>
                    </div>
                </div>

                <!-- Empty column for centering on desktop -->
                <div class="hidden md:block"></div>
            </div>
            @endforeach

            <!-- Row 2: Vice Directors -->
            @php
                $wadirMembers = $teamMembers->whereIn('position', [
                    'wakil_direktur_operasional', 
                    'wakil_direktur_keuangan', 
                    'wakil_direktur_investasi'
                ]);
            @endphp
            
            @if($wadirMembers->count() > 0)
            <div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    @foreach($wadirMembers as $member)
                    <div class="bg-white dark:bg-neutral-dark rounded-lg shadow-lg overflow-hidden group hover:translate-y-[-4px] transition-transform duration-300 border border-gray-100 dark:border-gray-800">
                        <div class="aspect-[4/3] w-full bg-gray-100 dark:bg-gray-900 relative overflow-hidden flex items-center justify-center">
                            @if($member->photo_path && Storage::disk('public')->exists($member->photo_path))
                                <img class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-500" alt="{{ $member->name }}" src="{{ Storage::disk('public')->url($member->photo_path) }}"/>
                            @else
                                <div class="text-gray-400 dark:text-gray-600 opacity-50 w-1/3 h-1/3">
                                    <svg class="w-full h-full" fill="currentColor" viewBox="0 -960 960 960">
                                        <path d="M480-480q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47ZM160-160v-112q0-34 17.5-62.5T224-378q62-31 126-46.5T480-440q66 0 130 15.5T736-378q29 15 46.5 43.5T800-272v112H160Z" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">{{ $member->name }}</h3>
                            <p class="text-xs font-medium text-primary uppercase tracking-widest" style="font-size: 10px;">{{ $positionLabels[$member->position] ?? $member->position }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Row 3: Staff -->
            @php
                $staffMembers = $teamMembers->where('position', 'staf');
            @endphp

            @if($staffMembers->count() > 0)
            <section>
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Staf</h2>
                    <div class="w-full h-px bg-gray-200 dark:bg-gray-800 mt-4"></div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                    @foreach($staffMembers as $member)
                    <div class="bg-white dark:bg-neutral-dark p-6 rounded-lg shadow-sm flex flex-col items-center text-center group hover:bg-gray-50 dark:hover:bg-gray-900/50 transition-colors duration-300 border border-gray-100 dark:border-gray-800">
                        <div class="w-24 h-24 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-6 ring-4 ring-white dark:ring-neutral-dark group-hover:ring-primary/20 transition-all overflow-hidden">
                            @if($member->photo_path && Storage::disk('public')->exists($member->photo_path))
                                <img class="w-full h-full object-cover" alt="{{ $member->name }}" src="{{ Storage::disk('public')->url($member->photo_path) }}"/>
                            @else
                                <svg class="w-1/2 h-1/2 text-gray-400 dark:text-gray-600 opacity-50" fill="currentColor" viewBox="0 -960 960 960">
                                    <path d="M480-480q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47ZM160-160v-112q0-34 17.5-62.5T224-378q62-31 126-46.5T480-440q66 0 130 15.5T736-378q29 15 46.5 43.5T800-272v112H160Z" />
                                </svg>
                            @endif
                        </div>
                        <h4 class="text-lg font-bold text-gray-900 dark:text-white leading-tight">{{ $member->name }}</h4>
                        <p class="text-xs font-medium text-primary mt-2 uppercase tracking-wide" style="font-size: 10px;">{{ $member->bio ?? 'STAF' }}</p>
                    </div>
                    @endforeach
                </div>
            </section>
            @endif
        </div>
    </div>
</div>
@endsection
