<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'NutriMeals') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;700;900&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>

    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        // On page load or when changing themes, best to add inline in `head` to avoid FOUC
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    @stack('styles')
</head>
<body class="bg-background-light dark:bg-background-dark text-text-main dark:text-white font-display overflow-x-hidden w-full transition-colors duration-300">
    <div class="relative flex flex-col min-h-screen w-full">
        <!-- Header -->
        <header class="sticky top-0 z-50 w-full bg-surface-light dark:bg-surface-dark border-b border-[#f5f3f0] dark:border-gray-800 shadow-sm transition-colors duration-300 relative">
            <div class="px-4 md:px-10 py-3 max-w-[1280px] mx-auto w-full">
                <div class="flex items-center justify-between whitespace-nowrap">
                    <!-- Logos -->
                    <div class="flex items-center gap-4">
                        <a href="{{ url('/') }}" class="block">
                            <img src="{{ asset('logokornas.png') }}" alt="Kornas Logo" class="h-10 md:h-12 w-auto object-contain dark:brightness-0 dark:invert transition-all">
                        </a>
                        <a href="{{ url('/') }}" class="block">
                             <img src="{{ asset('logobgn.png') }}" alt="Badan Gizi Nasional Logo" class="h-10 md:h-12 w-auto object-contain dark:brightness-0 dark:invert transition-all">
                        </a>
                    </div>

                    <div class="hidden md:flex flex-1 justify-end items-center gap-8">
                        <div class="flex items-center gap-6">
                            <a class="text-sm font-medium hover:text-primary transition-colors" href="{{ url('/') }}">Beranda</a>
                            <a class="text-sm font-medium hover:text-primary transition-colors" href="{{ route('team.public') }}">Tentang</a>

                        </div>
                        
                        <!-- Dark Mode Toggle -->
                        <button id="theme-toggle" type="button" class="text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-3 transition-colors">
                            <span id="theme-toggle-dark-icon" class="hidden material-symbols-outlined text-xl">dark_mode</span>
                            <span id="theme-toggle-light-icon" class="hidden material-symbols-outlined text-xl">light_mode</span>
                        </button>
                    </div>

                    <!-- Mobile Menu Button -->
                    <button id="mobile-menu-btn" class="md:hidden text-text-main dark:text-white p-2 focus:outline-none">
                        <span class="material-symbols-outlined text-2xl">menu</span>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu (Hidden by default, Absolute Overlay) -->
            <div id="mobile-menu" class="absolute top-full left-0 w-full bg-surface-light dark:bg-surface-dark hidden md:hidden border-b border-gray-100 dark:border-gray-800 shadow-md animate-fade-in-down z-40">
                <div class="flex flex-col gap-4 p-4">
                    <a class="text-base font-medium text-text-main dark:text-white hover:text-primary transition-colors px-2" href="{{ url('/') }}">Beranda</a>
                    <a class="text-base font-medium text-text-main dark:text-white hover:text-primary transition-colors px-2" href="{{ route('team.public') }}">Tentang</a>

                    
                    <!-- Mobile Dark Mode Toggle -->
                    <button id="mobile-theme-toggle" class="flex items-center gap-2 text-base font-medium text-text-main dark:text-white hover:text-primary transition-colors px-2 text-left">
                        <span class="material-symbols-outlined text-xl">dark_mode</span>
                        <span>Toggle Theme</span>
                    </button>
                </div>
            </div>
        </header>

        <main class="flex-grow">
            @hasSection('content')
                @yield('content')
            @else
                {{ $slot ?? '' }}
            @endif
        </main>

        <!-- Footer -->
        <footer class="bg-surface-light dark:bg-surface-dark border-t border-[#e6e1db] dark:border-gray-800 pt-16 pb-8">
            <div class="px-4 md:px-10 max-w-[1280px] mx-auto flex flex-col gap-12">
                <div class="flex flex-col md:flex-row justify-between gap-10">
                    <div class="flex flex-col gap-4 max-w-xs">
                        <a href="{{ url('/') }}" class="block">
                            <img src="{{ asset('logokornas.png') }}" alt="Kornas Logo" class="h-10 w-auto object-contain dark:brightness-0 dark:invert transition-all">
                        </a>
                        <p class="text-text-secondary text-sm leading-relaxed">
                            Kornas Makan Bergizi Muhammadiyah hadir untuk memastikan pemenuhan gizi anak bangsa melalui program makan bergizi di sekolah-sekolah seluruh Indonesia.
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-12 md:gap-24">
                        <div class="flex flex-col gap-4">
                            <h4 class="text-sm font-bold text-text-main dark:text-white uppercase tracking-wider">Organisasi</h4>
                            <a class="text-sm text-text-secondary hover:text-primary transition-colors" href="{{ route('team.public') }}">Tentang Kami</a>
                            <a class="text-sm text-text-secondary hover:text-primary transition-colors" href="{{ route('team.public') }}">Tim Kami</a>
                        </div>
                        <div class="flex flex-col gap-4">
                            <h4 class="text-sm font-bold text-text-main dark:text-white uppercase tracking-wider">Bantuan</h4>
                            <a class="text-sm text-text-secondary hover:text-primary transition-colors" href="{{ route('guide.public') }}">Panduan Aplikasi</a>

                        </div>
                        <div class="flex flex-col gap-4">
                            <h4 class="text-sm font-bold text-text-main dark:text-white uppercase tracking-wider">Area Login</h4>
                            <a class="text-sm text-text-secondary hover:text-primary transition-colors" href="{{ route('filament.admin.auth.login') }}">Login Kornas</a>
                            <a class="text-sm text-text-secondary hover:text-primary transition-colors" href="{{ route('filament.admin.auth.login', ['role' => 'pengusul']) }}">Login Lembaga Pengusul</a>
                            <a class="text-sm text-text-secondary hover:text-primary transition-colors" href="{{ route('filament.sppg.auth.login') }}">Login SPPG</a>
                            <a class="text-sm text-text-secondary hover:text-primary transition-colors" href="{{ route('filament.production.auth.login') }}">Login Distribusi</a>
                        </div>
                    </div>
                </div>
                


                <div class="border-t border-[#e6e1db] dark:border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                    <p class="text-xs text-text-secondary">© {{ date('Y') }} Kornas Makan Bergizi Muhammadiyah. Hak cipta dilindungi.</p>
                    <div class="flex gap-4">
                        <a class="text-text-secondary hover:text-primary" href="#"><span class="sr-only">Facebook</span>
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"></path></svg>
                        </a>
                        <a class="text-text-secondary hover:text-primary" href="#"><span class="sr-only">Twitter</span>
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"></path></svg>
                        </a>
                        <a class="text-text-secondary hover:text-primary" href="#"><span class="sr-only">Instagram</span>
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path clip-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772 4.902 4.902 0 011.772-1.153c.636-.247 1.363-.416 2.427-.465 1.067-.047 1.407-.06 3.808-.06h.63zm1.51 1.565c-2.645 0-2.964.01-4.015.058-.988.045-1.52.2-1.884.341a3.368 3.368 0 00-1.229.801 3.364 3.364 0 00-.801 1.228c-.14.364-.295.896-.34 1.885-.049 1.05-.058 1.37-.058 4.041v.08c0 2.646.009 2.965.059 4.015.044.989.199 1.52.339 1.885.2.525.47.962.802 1.228.326.332.763.602 1.229.802.364.14.896.295 1.884.34 1.05.049 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.2 1.85-.34a3.366 3.366 0 001.228-.802 3.361 3.361 0 00.802-1.228c.14-.365.295-.897.34-1.885.048-1.05.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.046-.976-.2-1.505-.34-1.85a3.364 3.364 0 00-.802-1.228 3.368 3.368 0 00-1.228-.801c-.365-.14-.897-.295-1.885-.34-.94-.045-1.246-.056-3.834-.056h-.85c-.3 0-.6.01-.89.028zm-4.004 3.94a4.345 4.345 0 110 8.69 4.345 4.345 0 010-8.69zm0 1.54a2.805 2.805 0 100 5.61 2.805 2.805 0 000-5.61zm5.955-3.033a1.033 1.033 0 110 2.066 1.033 1.033 0 010-2.066z" fill-rule="evenodd"></path></svg>
                        </a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
    <script>
        var themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
        var themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

        // Change the icons inside the button based on previous settings
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            themeToggleLightIcon.classList.remove('hidden');
        } else {
            themeToggleDarkIcon.classList.remove('hidden');
        }

        var themeToggleBtn = document.getElementById('theme-toggle');

        themeToggleBtn.addEventListener('click', function() {

            // toggle icons inside button
            themeToggleDarkIcon.classList.toggle('hidden');
            themeToggleLightIcon.classList.toggle('hidden');

            // if set via local storage previously
            if (localStorage.getItem('color-theme')) {
                if (localStorage.getItem('color-theme') === 'light') {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                }

            // if NOT set via local storage previously
            } else {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                }
            }
            
        });

        // Mobile Menu Toggle
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');

        if (mobileMenuBtn && mobileMenu) {
            mobileMenuBtn.addEventListener('click', () => {
                 mobileMenu.classList.toggle('hidden');
            });
        }

        // Mobile Theme Toggle (Reusing existing button logic)
        const mobileThemeToggle = document.getElementById('mobile-theme-toggle');
        if (mobileThemeToggle) {
            mobileThemeToggle.addEventListener('click', () => {
                var themeToggleBtn = document.getElementById('theme-toggle');
                themeToggleBtn.click();
            });
        }
    </script>
    @stack('scripts')
</body>
</html>
