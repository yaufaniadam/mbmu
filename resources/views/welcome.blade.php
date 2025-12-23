@extends('layouts.public')

@section('content')
<!-- Hero Section -->
<!-- Hero Section -->
<div class="relative h-[700px] flex items-center justify-center overflow-hidden">
    <!-- Background Image -->
    <div class="absolute inset-0 z-0">
        <img src="{{ asset('hero-children-wide.png') }}" alt="Anak-anak Sekolah Dasar Muhammadiyah Ceria" class="w-full h-full object-cover object-center">
        <!-- Overlay Gradient for text readability -->
        <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/20 to-transparent"></div>
    </div>

    <!-- Content -->
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center z-10 pt-20">
        <h1 class="text-4xl tracking-tight font-extrabold text-white sm:text-5xl md:text-6xl mb-6 drop-shadow-md">
            <span class="block text-blue-200 mb-2">Makan Bergizi Muhammadiyah</span>
            <span class="block">Untuk Generasi Masa Depan</span>
        </h1>
        
        <p class="mt-6 max-w-2xl mx-auto text-xl text-gray-100 mb-10 leading-relaxed drop-shadow-sm font-medium">
            Sistem manajemen terintegrasi untuk pengelolaan Program Makan Bergizi Muhammadiyah (MBM). Transparan, Akuntabel, dan Berkelanjutan.
        </p>

        <!-- Login button removed as requested -->
    </div>
</div>

<!-- Stats/Features Grid -->
<div id="about" class="py-16 bg-white border-t border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Card 1 -->
            <div class="p-6 bg-gray-50 rounded-2xl border border-gray-100 hover:shadow-md transition">
                <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Manajemen Relawan</h3>
                <p class="text-gray-600">Pengelolaan data relawan yang terstruktur mulai dari tingkat pusat hingga unit pelayanan.</p>
            </div>

            <!-- Card 2 -->
            <div class="p-6 bg-gray-50 rounded-2xl border border-gray-100 hover:shadow-md transition">
                 <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Transparansi Dana</h3>
                <p class="text-gray-600">Pencatatan dan pelaporan arus dana yang akuntabel dan dapat dipertanggungjawabkan.</p>
            </div>

            <!-- Card 3 -->
            <div class="p-6 bg-gray-50 rounded-2xl border border-gray-100 hover:shadow-md transition">
                 <div class="w-12 h-12 bg-purple-100 text-purple-600 rounded-xl flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Pelaporan Real-time</h3>
                <p class="text-gray-600">Monitor distribusi makanan dan kegiatan operasional secara langsung melalui dashboard.</p>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes blob {
        0% { transform: translate(0px, 0px) scale(1); }
        33% { transform: translate(30px, -50px) scale(1.1); }
        66% { transform: translate(-20px, 20px) scale(0.9); }
        100% { transform: translate(0px, 0px) scale(1); }
    }
    .animate-blob {
        animation: blob 7s infinite;
    }
    .animation-delay-2000 {
        animation-delay: 2s;
    }
    .animation-delay-4000 {
        animation-delay: 4s;
    }
</style>
@endsection
