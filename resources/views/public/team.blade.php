@extends('layouts.public')

@section('content')
<div class="flex-1 flex flex-col">
    <!-- Hero Section (Hidden) -->
    {{-- <section class="@container w-full">
        <div class="w-full relative h-[480px] lg:h-[520px] bg-cover bg-center flex flex-col items-center justify-center text-center px-4" data-alt="Group of diverse people working together in a kitchen environment, smiling" style='background-image: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.6)), url("https://lh3.googleusercontent.com/aida-public/AB6AXuDMw3TTXpSMKBsoz1w_UWC5ADYtsJDWBZ6TXRaAj07Y6J32iUpXAxYl1dnxOcFQ1Dh4sjblc9jMSD58H4_lyAc1eMhjQ3eWylcUNWiThjuRQHEety9znCV-w7RQGNlAaZ1U30D_UAp_Q8B9AdCdmZo25vzPv9h9wzOEx7Ae0b9JPLDPHf9ZGqXafXEg5zC3vznNdPXJBftX4nF3Zle-cX_vmMAHM5C_lBONVsPTAN6xyEtA_Dogcp5Kjkfc5Mleb6hvHHtY_BT2aShp");'>
            <div class="max-w-[800px] flex flex-col items-center gap-6 animate-fade-in-up">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/20 backdrop-blur-sm border border-primary/30 text-primary-light text-xs font-bold uppercase tracking-wider text-white">
                    <span class="material-symbols-outlined text-sm">groups</span> Our People
                </span>
                <h1 class="text-white text-4xl md:text-5xl lg:text-6xl font-black leading-tight tracking-tight drop-shadow-sm">
                    Nourishing Minds,<br class="hidden sm:block"/> One Meal at a Time
                </h1>
                <p class="text-gray-200 text-base md:text-lg lg:text-xl font-normal max-w-2xl leading-relaxed">
                    Meet the dedicated professionals, nutritionists, and logistics experts working behind the scenes to ensure every child gets a healthy start.
                </p>
                <div class="flex gap-4 mt-2">
                    <button class="flex items-center justify-center rounded-lg h-12 px-8 bg-primary text-[#181511] text-base font-bold hover:scale-105 transition-transform shadow-lg shadow-orange-900/20">
                        Join Our Mission
                    </button>
                    <button class="flex items-center justify-center rounded-lg h-12 px-8 bg-white/10 backdrop-blur-md border border-white/30 text-white text-base font-bold hover:bg-white/20 transition-all">
                        Volunteer
                    </button>
                </div>
            </div>
        </div>
    </section> --}}

    <div class="w-full max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        <!-- Leadership Section -->
        <div class="mb-16">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 pb-4 border-b border-[#e6e2de] dark:border-[#3a3025]">
                <div>
                    <h2 class="text-[#181511] dark:text-white text-3xl font-bold leading-tight tracking-tight">Struktur Kepemimpinan</h2>
                    <p class="text-gray-500 dark:text-gray-400 mt-2">Mengarahkan visi dan misi organisasi menuju generasi Indonesia yang sehat dan berdaya.</p>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($teamMembers->whereIn('position', ['ketua', 'sekretaris', 'bendahara']) as $member)
                <!-- Team Card -->
                <div class="group bg-white dark:bg-neutral-dark rounded-xl overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border border-[#e6e2de] dark:border-[#3a3025]">
                    <div class="relative h-64 overflow-hidden">
                        @php
                            $imageUrl = null;
                            if ($member->photo_path) {
                                $imageUrl = Storage::disk('public')->url($member->photo_path);
                            } elseif ($member->position === 'ketua') {
                                $imageUrl = asset('ketua.png');
                            } elseif ($member->position === 'sekretaris') {
                                $imageUrl = asset('sekre.png');
                            }
                        @endphp
                        
                        @if($imageUrl)
                        <img alt="{{ $member->name }}" class="w-full h-full object-cover object-top group-hover:scale-105 transition-transform duration-500" src="{{ $imageUrl }}"/>
                        @else
                        <div class="w-full h-full bg-gradient-to-br from-primary/20 to-secondary-green/20 flex items-center justify-center">
                            <span class="text-6xl text-primary">{{ strtoupper(substr($member->name, 0, 1)) }}</span>
                        </div>
                        @endif
                        <div class="absolute bottom-0 left-0 w-full h-1/2 bg-gradient-to-t from-black/60 to-transparent opacity-60"></div>
                    </div>
                    <div class="p-5 flex flex-col gap-2">
                        <div>
                            <h3 class="text-lg font-bold text-[#181511] dark:text-white">{{ $member->name }}</h3>
                            <span class="inline-block text-primary text-sm font-bold uppercase tracking-wide mt-1">
                                {{ $member->position === 'ketua' ? 'Ketua' : ($member->position === 'sekretaris' ? 'Sekretaris' : 'Bendahara') }}
                            </span>
                        </div>
                        @if($member->bio)
                        <p class="text-gray-600 dark:text-gray-300 text-sm line-clamp-3 leading-relaxed">
                            {{ $member->bio }}
                        </p>
                        @endif
                        <div class="mt-4 pt-4 border-t border-[#f0ede9] dark:border-[#3a3025] flex gap-3">
                            @if($member->email)
                            <a class="text-gray-400 hover:text-primary transition-colors" href="mailto:{{ $member->email }}"><span class="material-symbols-outlined text-xl">mail</span></a>
                            @endif
                            @if($member->phone)
                            <a class="text-gray-400 hover:text-primary transition-colors" href="tel:{{ $member->phone }}"><span class="material-symbols-outlined text-xl">phone</span></a>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full text-center py-12">
                    <p class="text-gray-500 dark:text-gray-400">Belum ada data kepemimpinan.</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Operations Staff Section -->
        <div>
            <div class="mb-8 pb-4 border-b border-[#e6e2de] dark:border-[#3a3025]">
                <h2 class="text-[#181511] dark:text-white text-3xl font-bold leading-tight tracking-tight">Tim Pelaksana</h2>
                <p class="text-gray-500 dark:text-gray-400 mt-2">Garda terdepan yang berdedikasi mewujudkan aksi nyata di lapangan setiap harinya.</p>
            </div>
            <!-- Grid Layout for Personnel - More compact card style -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-5">
                @forelse($teamMembers->where('position', 'staf') as $member)
                <!-- Staff Card -->
                <div class="flex flex-col items-center bg-white dark:bg-neutral-dark p-6 rounded-xl border border-[#e6e2de] dark:border-[#3a3025] text-center hover:shadow-md transition-shadow">
                    <div class="size-24 rounded-full overflow-hidden mb-4 border-4 border-primary/20">
                        @if($member->photo_path)
                        <img alt="{{ $member->name }}" class="w-full h-full object-cover" src="{{ Storage::disk('public')->url($member->photo_path) }}"/>
                        @else
                        <div class="w-full h-full bg-gradient-to-br from-primary/20 to-secondary-green/20 flex items-center justify-center">
                            <span class="text-3xl text-primary font-bold">{{ strtoupper(substr($member->name, 0, 1)) }}</span>
                        </div>
                        @endif
                    </div>
                    <h4 class="font-bold text-[#181511] dark:text-white text-base">{{ $member->name }}</h4>
                    <p class="text-xs font-semibold text-primary uppercase tracking-wide mb-2">Staf</p>
                    @if($member->bio)
                    <p class="text-gray-500 dark:text-gray-400 text-xs">{{ $member->bio }}</p>
                    @endif
                </div>
                @empty
                <div class="col-span-full text-center py-12">
                    <p class="text-gray-500 dark:text-gray-400">Belum ada data staff.</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Join The Team CTA -->

    </div>
</div>
@endsection
