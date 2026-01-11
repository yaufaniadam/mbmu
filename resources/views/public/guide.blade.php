@extends('layouts.public')

@section('content')
<div class="w-full bg-surface-light dark:bg-surface-dark py-12">
    <div class="w-full max-w-[1024px] mx-auto px-4 md:px-8">
        <!-- Header -->
        <div class="mb-10 text-center">
            <h1 class="text-3xl md:text-4xl font-black text-text-main dark:text-white mb-4">Panduan Aplikasi SPPG</h1>
            <p class="text-text-secondary dark:text-gray-400 text-lg max-w-2xl mx-auto">
                Petunjuk teknis penggunaan Aplikasi Manajemen SPPG untuk operasional harian, pelaporan, dan keuangan.
            </p>
        </div>

        <!-- Content Card -->
        <div class="bg-white dark:bg-[#1a1612] rounded-2xl p-6 md:p-10 shadow-sm border border-[#e6e2de] dark:border-neutral-800">
            <div class="prose prose-lg dark:prose-invert max-w-none text-text-main dark:text-gray-300">
                <h2>Selamat Datang</h2>
                <p>
                    Aplikasi ini dirancang untuk memudahkan Kepala SPPG dalam mengelola operasional harian, 
                    mulai dari pendataan staf, relawan, hingga pelaporan distribusi makanan.
                </p>

                <hr class="border-[#e6e2de] dark:border-neutral-700 my-8">

                <div class="space-y-4">
                    <!-- Section 1 -->
                    <details class="group bg-surface-light dark:bg-[#26201a] p-4 rounded-xl cursor-pointer transition-all open:bg-primary/5 dark:open:bg-primary/10" open>
                        <summary class="font-bold text-lg list-none flex items-center justify-between">
                            <span>1. Langkah Awal (Onboarding)</span>
                            <span class="material-symbols-outlined transition-transform group-open:rotate-180">expand_more</span>
                        </summary>
                        <div class="mt-4 text-base text-text-secondary dark:text-gray-400">
                            <p class="mb-2">
                                Saat pertama kali login, pastikan Anda melengkapi data-data berikut agar akun SPPG Anda berstatus <strong>Active</strong>:
                            </p>
                            <ul class="list-disc pl-5 space-y-2">
                                <li><strong>Profil SPPG:</strong> Lengkapi alamat, titik lokasi (maps), dan nomor rekening/VA.</li>
                                <li><strong>Staf:</strong> Tambahkan data Staf Akuntan dan Ahli Gizi.</li>
                                <li><strong>Relawan:</strong> Data relawan yang terlibat.</li>
                                <li><strong>Penerima Manfaat:</strong> Daftar sekolah atau panti yang dilayani.</li>
                            </ul>
                        </div>
                    </details>

                    <!-- Section 2 -->
                    <details class="group bg-surface-light dark:bg-[#26201a] p-4 rounded-xl cursor-pointer transition-all open:bg-primary/5 dark:open:bg-primary/10">
                        <summary class="font-bold text-lg list-none flex items-center justify-between">
                            <span>2. Manajemen Operasional</span>
                            <span class="material-symbols-outlined transition-transform group-open:rotate-180">expand_more</span>
                        </summary>
                        <div class="mt-4 text-base text-text-secondary dark:text-gray-400">
                            <p class="mb-4">Setelah data dasar lengkap, Anda dapat mulai menggunakan fitur harian:</p>
                            
                            <div class="space-y-4 pl-4 border-l-2 border-gray-200 dark:border-gray-700">
                                <div>
                                    <h4 class="font-bold text-text-main dark:text-gray-200">A. Jadwal Produksi</h4>
                                    <p>Tentukan menu dan jumlah porsi harian. Sistem akan mencatat riwayat produksi untuk laporan tagihan.</p>
                                </div>
                                <div>
                                    <h4 class="font-bold text-text-main dark:text-gray-200">B. Distribusi Makanan</h4>
                                    <p>Catat pengantaran makanan ke sekolah. Pastikan mengunggah foto bukti serah terima (Berita Acara).</p>
                                </div>
                                <div>
                                    <h4 class="font-bold text-text-main dark:text-gray-200">C. Absensi Harian</h4>
                                    <p>Fitur ini digunakan untuk mencatat kehadiran relawan setiap harinya.</p>
                                </div>
                            </div>
                        </div>
                    </details>

                    <!-- Section 3 -->
                    <details class="group bg-surface-light dark:bg-[#26201a] p-4 rounded-xl cursor-pointer transition-all open:bg-primary/5 dark:open:bg-primary/10">
                        <summary class="font-bold text-lg list-none flex items-center justify-between">
                            <span>3. Laporan Keuangan</span>
                            <span class="material-symbols-outlined transition-transform group-open:rotate-180">expand_more</span>
                        </summary>
                        <div class="mt-4 text-base text-text-secondary dark:text-gray-400">
                            <p>
                                Upload laporan keuangan excel pada menu Keuangan.
                            </p>
                        </div>
                    </details>
                </div>

                <div class="bg-primary/5 border-l-4 border-primary p-6 rounded-r-xl my-8">
                    <h4 class="font-bold text-primary text-lg m-0 mb-2">Butuh Bantuan Lebih Lanjut?</h4>
                    <p class="text-base m-0 text-text-main dark:text-gray-300">
                        Jika Anda mengalami kendala teknis, hubungi <strong>Helpdesk Kornas</strong> melalui WhatsApp di 
                        <a href="#" class="text-primary font-bold hover:underline">0812-3456-7890</a>.
                    </p>
                </div>
            </div>
            
            <div class="mt-10 pt-6 border-t border-[#e6e2de] dark:border-neutral-700 flex justify-center">
                 <a href="/sppg/login" class="inline-flex items-center justify-center rounded-lg bg-primary hover:bg-primary-dark text-white font-bold py-3 px-8 shadow-lg shadow-orange-500/20 transition-all transform hover:-translate-y-1">
                    <span class="material-symbols-outlined mr-2">login</span>
                    Login ke Dashboard SPPG
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
