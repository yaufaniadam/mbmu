@extends('layouts.public')

@section('content')
<!-- Navigation is in layout -->

<!-- Main Content Wrapper -->
<div class="w-full flex-grow">
    <!-- Hero Section -->
    <div class="relative w-full bg-surface-light dark:bg-surface-dark">
        <div class="w-full max-w-[1280px] mx-auto px-4 md:px-10 lg:px-20 py-8 lg:py-12">
            <div class="relative w-full rounded-2xl overflow-hidden shadow-lg h-[320px] md:h-[400px] flex items-center justify-center text-center p-8 bg-cover bg-center" 
                style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.7)), url('https://lh3.googleusercontent.com/aida-public/AB6AXuBEBZaqfYDKpm137PBDLsBRLUWeeJ5mT9jLNf14m_JR5lT-huZzmJ2gv0kW4XOul5V8R0hrhaiUROgYVDpNCfkfSXFCRw1rrVt6B4NNcAoqqU9dlFvKj7hFqyVIAK8GEGNOK_MHY6MYT_IDuLkwmBfCmBNn7UtSLzQ2ed-1KHgfMVwoFISorp2TcYVg3MzhTM-vzfoCSxdL60CTilJqwOn33esHpqh4Vd_3-3OYYgpveZml13lk4OwiUSenB3vKHM4_pQR0Y9MebjQk');">
                <div class="relative z-10 max-w-3xl space-y-4">
                    <h1 class="text-white text-4xl md:text-6xl font-black leading-tight tracking-tight drop-shadow-sm">
                        Hubungi Kami
                    </h1>
                    <p class="text-white/90 text-lg md:text-xl font-medium leading-relaxed drop-shadow-sm">
                         Punya pertanyaan tentang program? Kami siap membantu. Hubungi kami untuk informasi lebih lanjut, kemitraan, atau pertanyaan umum.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Split Layout -->
    <div class="w-full max-w-[1280px] mx-auto px-4 md:px-10 lg:px-20 pb-16 lg:pb-24">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-16">
            
            <!-- Left Column: Contact Form -->
            <div class="flex flex-col gap-8">


                <!-- OpenStreetMap Frame -->
                <div class="w-full h-[500px] rounded-2xl overflow-hidden shadow-sm border border-gray-100 dark:border-gray-800 relative z-0">
                    <iframe 
                        width="100%" 
                        height="100%" 
                        frameborder="0" 
                        scrolling="no" 
                        marginheight="0" 
                        marginwidth="0" 
                        {{-- Centered around Gedung PP Muhammadiyah Yogyakarta approx -7.8012, 110.3603 --}}
                        src="https://www.openstreetmap.org/export/embed.html?bbox=110.3553%2C-7.8062%2C110.3653%2C-7.7962&amp;layer=mapnik&amp;marker=-7.8012%2C110.3603" 
                        style="border: 0">
                    </iframe>
                </div>
            </div>

            <!-- Right Column: Contact Info & Map -->
            <div class="flex flex-col gap-8">
                <!-- Contact Details Card -->
                <div class="bg-white dark:bg-[#1a1612] p-8 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm flex flex-col gap-8">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Informasi Kontak</h2>
                        <div class="flex flex-col gap-6">
                            <!-- Address -->
                            <div class="flex items-start gap-4">
                                <div class="bg-primary/10 text-primary p-3 rounded-xl shrink-0">
                                    <span class="material-symbols-outlined text-[24px]">location_on</span>
                                </div>
                                <div>
                                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Kantor Pusat</h3>
                                    <p class="text-gray-900 dark:text-white font-medium text-lg leading-relaxed">
                                        Gedung PP Muhammadiyah<br>
                                        Jl. K.H. Ahmad Dahlan<br>
                                        Yogyakarta
                                    </p>
                                </div>
                            </div>
                            <!-- Phone -->
                            <div class="flex items-start gap-4">
                                <div class="bg-primary/10 text-primary p-3 rounded-xl shrink-0">
                                    <span class="material-symbols-outlined text-[24px]">call</span>
                                </div>
                                <div>
                                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Telepon</h3>
                                    <p class="text-gray-900 dark:text-white font-medium text-lg cursor-pointer hover:text-primary transition-colors">(0274) 123456</p>
                                </div>
                            </div>
                            <!-- Email -->
                            <div class="flex items-start gap-4">
                                <div class="bg-primary/10 text-primary p-3 rounded-xl shrink-0">
                                    <span class="material-symbols-outlined text-[24px]">mail</span>
                                </div>
                                <div>
                                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Email</h3>
                                    <p class="text-gray-900 dark:text-white font-medium text-lg cursor-pointer hover:text-primary transition-colors">mbm@muhammadiyah.or.id</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>




            </div>
        </div>
    </div>
</div>
@endsection
