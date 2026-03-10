<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Kornas Makan Bergizi Muhammadiyah') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .fade-in-up {
            animation: fadeInUp 1s ease-out forwards;
            opacity: 0;
            transform: translateY(20px);
        }
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.3s; }
        .delay-300 { animation-delay: 0.5s; }
        .delay-400 { animation-delay: 0.7s; }
        
        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(90px);
            opacity: 0.08;
            animation: moveBlob 20s infinite alternate cubic-bezier(0.4, 0, 0.2, 1);
        }
        @keyframes moveBlob {
            0% { transform: translate(0px, 0px) scale(1); }
            100% { transform: translate(150px, -150px) scale(1.3); }
        }
        .delay-2000 {
            animation-delay: 2s;
            animation-direction: alternate-reverse;
        }
    </style>
</head>
<body class="bg-white min-h-screen w-full flex items-center justify-center font-display relative overflow-hidden text-gray-900">
    <!-- Blobs for dynamic background -->
    <div class="blob bg-[#146f46] w-[400px] h-[400px] md:w-[600px] md:h-[600px] -top-[100px] -left-[100px]"></div>
    <div class="blob bg-[#071e49] w-[500px] h-[500px] md:w-[700px] md:h-[700px] -bottom-[200px] -right-[100px] delay-2000"></div>

    <div class="relative z-10 w-full max-w-5xl px-6 py-12 flex flex-col items-center justify-center text-center h-full">
        <!-- Logos -->
        <div class="flex flex-col md:flex-row items-center justify-center gap-6 md:gap-12 mb-12 fade-in-up delay-100">
            <div class="bg-white rounded-3xl p-5 md:p-8 shadow-xl border border-gray-100 transform transition duration-500 hover:scale-105 hover:shadow-2xl">
                <img src="{{ asset('logokornas.png') }}" alt="Kornas Logo" class="h-24 md:h-36 w-auto object-contain">
            </div>
            <div class="bg-white rounded-3xl p-5 md:p-8 shadow-xl border border-gray-100 transform transition duration-500 hover:scale-105 hover:shadow-2xl">
                <img src="{{ asset('logobgn.png') }}" alt="Badan Gizi Nasional" class="h-24 md:h-36 w-auto object-contain">
            </div>
        </div>

        <!-- Typography -->
        <div class="space-y-6 mb-16 fade-in-up delay-200 max-w-4xl">
            <h1 class="text-4xl md:text-6xl lg:text-7xl font-black tracking-tight leading-tight text-gray-900 drop-shadow-sm">
                Makan Bergizi <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#146f46] to-[#071e49] drop-shadow-none">Muhammadiyah</span>
            </h1>
            <p class="text-lg md:text-2xl text-gray-600 font-light max-w-3xl mx-auto leading-relaxed">
                Membangun Generasi Sehat, Kuat dan Cerdas melalui Pemenuhan Gizi Optimal di Sekolah-Sekolah.
            </p>
        </div>

        <!-- Call to Action -->
        <div class="fade-in-up delay-300">
            <a href="{{ route('home') }}" class="group relative inline-flex items-center justify-center gap-4 px-10 py-5 bg-gradient-to-r from-[#146f46] to-[#071e49] text-white rounded-full font-bold text-xl md:text-2xl overflow-hidden transition-all duration-300 hover:scale-105 shadow-xl hover:shadow-2xl"
               style="background: linear-gradient(to right, #146f46, #071e49);">
                <span class="relative">Masuk ke Beranda Utama</span>
                <div class="relative w-10 h-10 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center group-hover:bg-white/30 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 transform group-hover:translate-x-1 transition-transform text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </div>
            </a>
        </div>
        
        <!-- Minor Links -->
        <div class="mt-20 flex gap-8 text-sm md:text-base font-medium text-gray-500 fade-in-up delay-400">
            <a href="{{ route('filament.admin.auth.login') }}" class="hover:text-[#146f46] transition-colors">Admin Area</a>
            <span class="opacity-30">|</span>
            <a href="{{ route('filament.lembaga.auth.login') }}" class="hover:text-[#146f46] transition-colors">Portal Lembaga</a>
            <span class="opacity-30">|</span>
            <a href="{{ route('filament.sppg.auth.login') }}" class="hover:text-[#146f46] transition-colors">Portal SPPG</a>
        </div>
    </div>
</body>
</html>
