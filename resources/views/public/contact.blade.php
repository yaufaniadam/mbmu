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
                <div>
                    <h2 class="text-text-main dark:text-white text-3xl font-bold mb-2">Kirim Pesan</h2>
                    <p class="text-text-main/70 dark:text-gray-400">Isi formulir di bawah ini dan tim kami akan segera menghubungi Anda.</p>
                </div>

                <form class="flex flex-col gap-5">
                    <!-- Name Input -->
                    <div class="flex flex-col w-full gap-2">
                        <label class="text-text-main dark:text-white font-medium">Nama Lengkap</label>
                        <input type="text" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-[#1a1612] text-gray-900 dark:text-white focus:ring-primary focus:border-primary px-4 py-3 shadow-sm transition-colors" placeholder="Nama Anda">
                    </div>

                    <!-- Email Input -->
                    <div class="flex flex-col w-full gap-2">
                        <label class="text-text-main dark:text-white font-medium">Email Address</label>
                        <input type="email" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-[#1a1612] text-gray-900 dark:text-white focus:ring-primary focus:border-primary px-4 py-3 shadow-sm transition-colors" placeholder="email@contoh.com">
                    </div>

                    <!-- Subject Input -->
                    <div class="flex flex-col w-full gap-2">
                        <label class="text-text-main dark:text-white font-medium">Subjek</label>
                        <select class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-[#1a1612] text-gray-900 dark:text-white focus:ring-primary focus:border-primary px-4 py-3 shadow-sm transition-colors">
                            <option>Pertanyaan Umum</option>
                            <option>Kemitraan Sekolah</option>
                            <option>Donasi</option>
                            <option>Relawan</option>
                        </select>
                    </div>

                    <!-- Message Textarea -->
                    <div class="flex flex-col w-full gap-2">
                        <label class="text-text-main dark:text-white font-medium">Pesan</label>
                        <textarea class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-[#1a1612] text-gray-900 dark:text-white focus:ring-primary focus:border-primary px-4 py-3 min-h-[150px] shadow-sm transition-colors" placeholder="Tulis pesan Anda di sini..."></textarea>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="mt-2 w-full md:w-auto self-start bg-primary hover:bg-primary-dark text-white font-bold py-3 px-8 rounded-lg shadow-md transition-all transform active:scale-95 flex items-center justify-center gap-2">
                        <span>Kirim Pesan</span>
                        <span class="material-symbols-outlined text-[20px]">send</span>
                    </button>
                </form>
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

                <!-- Embedded Map Block -->
                <div class="w-full h-80 rounded-2xl overflow-hidden shadow-sm border border-gray-100 dark:border-gray-800 relative group">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3952.9698973792074!2d110.36034131477793!3d-7.801194994378129!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a5788d4001d71%3A0x6c6670860533100c!2sGedung%20Pimpinan%20Pusat%20Muhammadiyah!5e0!3m2!1sen!2sid!4v1672345678901!5m2!1sen!2sid" 
                        width="100%" 
                        height="100%" 
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy" 
                        class="grayscale group-hover:grayscale-0 transition-all duration-700"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>

                <!-- FAQ Link Block -->
                <div class="bg-primary/5 border border-primary/20 p-6 rounded-2xl flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <span class="material-symbols-outlined text-primary text-4xl">help</span>
                        <div>
                            <h3 class="text-gray-900 dark:text-white font-bold text-lg">Punya pertanyaan singkat?</h3>
                            <p class="text-gray-500 text-sm">Cek FAQ kami untuk jawaban instan.</p>
                        </div>
                    </div>
                    <button class="bg-white dark:bg-[#1a1612] text-gray-900 dark:text-white font-medium py-2 px-5 rounded-lg border border-gray-200 dark:border-gray-700 hover:shadow-md transition-all">
                        Lihat FAQ
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
