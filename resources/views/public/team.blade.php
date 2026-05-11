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
            <div class="flex justify-center">
                <div class="w-full md:w-[calc(33.333%-1.33rem)]">
                    <div class="bg-white dark:bg-neutral-dark rounded-lg shadow-lg overflow-hidden group hover:translate-y-[-4px] transition-transform duration-300 border border-gray-100 dark:border-gray-800">
                        <div class="aspect-[4/3] w-full bg-gray-100 dark:bg-gray-900 relative overflow-hidden flex items-center justify-center">
                            @if($member->photo_path && Storage::disk('public')->exists($member->photo_path))
                                <img class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-500" alt="{{ $member->name }}" src="{{ Storage::disk('public')->url($member->photo_path) }}"/>
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-primary/10">
                                    <span class="text-6xl font-bold text-primary opacity-20">{{ strtoupper(substr($member->name, 0, 1)) }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">{{ $member->name }}</h3>
                            <p class="text-xs font-bold text-primary uppercase tracking-widest">{{ $positionLabels[$member->position] ?? $member->position }}</p>
                        </div>
                    </div>
                </div>
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
                                <span class="text-6xl font-bold text-primary opacity-20">
                                    {{ $member->position === 'wakil_direktur_operasional' ? 'W' : ($member->position === 'wakil_direktur_keuangan' ? 'K' : 'M') }}
                                </span>
                            @endif
                        </div>
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">{{ $member->name }}</h3>
                            <p class="text-xs font-bold text-primary uppercase tracking-widest mb-4">{{ $positionLabels[$member->position] ?? $member->position }}</p>
                            <div class="w-full h-px bg-gray-100 dark:bg-gray-800 mb-4"></div>
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
                                <span class="text-3xl font-bold text-primary">{{ strtoupper(substr($member->name, 0, 1)) }}</span>
                            @endif
                        </div>
                        <h4 class="text-lg font-bold text-gray-900 dark:text-white leading-tight">{{ $member->name }}</h4>
                        <p class="text-xs font-bold text-primary mt-2 uppercase tracking-wide">{{ $member->bio ?? 'STAF' }}</p>
                    </div>
                    @endforeach
                </div>
            </section>
            @endif
        </div>
    </div>
</div>
@endsection
