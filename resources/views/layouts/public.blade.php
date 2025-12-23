<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'MBM App') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased bg-gray-50 text-slate-800 font-[Inter]">
        
        <!-- Navbar -->
        <nav class="fixed w-full z-50 bg-white/95 backdrop-blur-md border-b border-gray-100 h-20 shadow-sm transition-all duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full">
                <div class="flex justify-between items-center h-full">
                    <!-- Left: Logos -->
                    <div class="flex-shrink-0 flex items-center gap-4">
                        <a href="{{ url('/') }}" class="flex items-center gap-3">
                            <img src="{{ asset('logokornas.png') }}" alt="Logo Kornas" class="h-10 w-auto">
                            <div class="h-8 w-px bg-gray-200"></div>
                            <img src="{{ asset('logobgn.png') }}" alt="Logo BGN" class="h-10 w-auto">
                        </a>
                    </div>
                    
                    <!-- Right: Menu & Actions -->
                    <div class="hidden md:flex items-center gap-8">
                        <div class="flex items-center gap-6">
                            <a href="{{ route('profile.public') }}" class="text-gray-600 hover:text-blue-600 font-medium text-sm lg:text-base transition">Profil</a>
                            <a href="{{ route('team.public') }}" class="text-gray-600 hover:text-blue-600 font-medium text-sm lg:text-base transition">Tim</a>
                            <a href="{{ route('sppg.public.index') }}" class="text-gray-600 hover:text-blue-600 font-medium text-sm lg:text-base transition">SPPG</a>
                            <a href="{{ route('contact.public') }}" class="text-gray-600 hover:text-blue-600 font-medium text-sm lg:text-base transition">Kontak</a>
                        </div>

                        <!-- Login Button (kept for functionality, can be removed if strictly text only desired) -->
                        <!-- Login Button hidden as requested to move to footer -->
                        <!-- 
                        <div>
                            @if (Route::has('login'))
                                @auth
                                    <a href="{{ url('/admin') }}" class="inline-flex items-center px-5 py-2 border border-transparent text-sm font-medium rounded-full text-white bg-blue-600 hover:bg-blue-700 transition shadow-sm">
                                        Dashboard
                                    </a>
                                @else
                                    <a href="{{ route('filament.admin.auth.login') }}" class="inline-flex items-center px-5 py-2 border border-blue-600 text-sm font-medium rounded-full text-blue-600 hover:bg-blue-50 transition">
                                        Masuk
                                    </a>
                                @endauth
                            @endif
                        </div>
                        -->
                    </div>

                    <!-- Mobile Menu Button (Hamburger) -->
                    <div class="md:hidden flex items-center">
                        <button type="button" class="text-gray-500 hover:text-gray-900 focus:outline-none" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-gray-100">
                <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                    <a href="{{ route('profile.public') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">Profil</a>
                    <a href="{{ route('team.public') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">Tim</a>
                    <a href="{{ route('sppg.public.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">SPPG</a>
                    <a href="{{ route('contact.public') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">Kontak</a>
                    @auth
                        <a href="{{ url('/admin') }}" class="block px-3 py-2 rounded-md text-base font-medium text-white bg-blue-600 hover:bg-blue-700 mt-4 text-center">Dashboard</a>
                    @else
                        <a href="{{ route('filament.admin.auth.login') }}" class="block px-3 py-2 rounded-md text-base font-medium text-blue-600 border border-blue-600 hover:bg-blue-50 mt-4 text-center">Masuk</a>
                    @endauth
                </div>
            </div>
        </nav>

        <main>
            @yield('content')
        </main>

        <!-- Footer -->
        <footer id="footer" class="bg-gray-900 text-white py-12 border-t border-gray-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                    <!-- Brand & Info -->
                    <div class="text-center md:text-left">
                        <span class="text-xl font-bold tracking-wider">MBM App</span>
                        <p class="text-gray-400 text-sm mt-2">© {{ date('Y') }} Muhammadiyah. All rights reserved.</p>
                        <p class="text-gray-500 text-xs mt-1">Gedung PP Muhammadiyah, Jl. K.H. Ahmad Dahlan, Yogyakarta</p>
                    </div>

                    <!-- Login Links -->
                    <div class="flex flex-wrap justify-center gap-4 text-sm font-medium">
                        <a href="{{ route('filament.admin.auth.login') }}" class="px-4 py-2 bg-gray-800 hover:bg-gray-700 rounded-lg text-gray-300 hover:text-white transition">
                            Login Kornas
                        </a>
                        <div class="hidden md:block w-px bg-gray-700 h-8 self-center"></div>
                        <a href="{{ route('filament.sppg.auth.login') }}" class="px-4 py-2 bg-gray-800 hover:bg-gray-700 rounded-lg text-gray-300 hover:text-white transition">
                            Login SPPG
                        </a>
                        <div class="hidden md:block w-px bg-gray-700 h-8 self-center"></div>
                        <!-- Assumption: 'Distribusi' maps to 'Production' panel based on available routes, or arguably could be Admin too. 
                             Given 'production' route exists, likely related to distribution/production flow. -->
                        <a href="{{ route('filament.production.auth.login') }}" class="px-4 py-2 bg-gray-800 hover:bg-gray-700 rounded-lg text-gray-300 hover:text-white transition">
                            Login Distribusi
                        </a>
                    </div>
                </div>
            </div>
        </footer>
    </body>
</html>
