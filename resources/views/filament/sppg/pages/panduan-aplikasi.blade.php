<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Main Content (Markdown Area) -->
        <div class="lg:col-span-3 space-y-6">
            <x-filament::section>
                <div class="prose dark:prose-invert max-w-none">
                    <h2>Selamat Datang di Aplikasi Manajemen SPPG</h2>
                    <p>
                        Aplikasi ini dirancang untuk memudahkan Kepala SPPG dalam mengelola operasional harian, 
                        mulai dari pendataan staf, relawan, hingga pelaporan distribusi makanan.
                    </p>

                    <hr>

                    <h3>1. Langkah Awal (Onboarding)</h3>
                    <p>
                        Saat pertama kali login, pastikan Anda melengkapi data-data berikut agar akun SPPG Anda berstatus <strong>Active</strong>:
                    </p>
                    <ul>
                        <li><strong>Profil SPPG:</strong> Lengkapi alamat, titik lokasi (maps), dan nomor rekening/VA.</li>
                        <li><strong>Staf:</strong> Tambahkan data Staf AKuntan dan Ahli Gizi.</li>
                        <li><strong>Relawan:</strong> Data relawan yang terlibat.</li>
                        <li><strong>Penerima Manfaat:</strong> Daftar sekolah atau panti yang dilayani.</li>
                    </ul>

                    <h3>2. Manajemen Operasional</h3>
                    <p>Setelah data dasar lengkap, Anda dapat mulai menggunakan fitur harian:</p>
                    
                    <details class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg my-2 cursor-pointer">
                        <summary class="font-bold">A. Jadwal Produksi</summary>
                        <p class="mt-2 text-sm">
                            Tentukan menu dan jumlah porsi harian. Sistem akan mencatat riwayat produksi untuk laporan tagihan.
                        </p>
                    </details>

                    <details class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg my-2 cursor-pointer">
                        <summary class="font-bold">B. Distribusi Makanan</summary>
                        <p class="mt-2 text-sm">
                            Catat pengantaran makanan ke sekolah. Pastikan mengunggah foto bukti serah terima (Berita Acara).
                        </p>
                    </details>

                    <details class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg my-2 cursor-pointer">
                        <summary class="font-bold">C. Absensi Harian</summary>
                        <p class="mt-2 text-sm">
                            Fitur ini digunakan untuk mencatat kehadiran relawan setiap harinya.
                        </p>
                    </details>

                    <h3>3. Laporan Keuangan</h3>
                    <p>
                        Upload laporan keuangan excel pada menu Keuangan.
                    </p>

                    <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 p-4 my-4">
                        <p class="font-bold text-blue-700 dark:text-blue-400 m-0">Butuh Bantuan Lebih Lanjut?</p>
                        <p class="text-sm m-0">
                            Jika Anda mengalami kendala teknis, hubungi <strong>Helpdesk Kornas</strong> melalui WhatsApp di 
                            <a href="#" class="underline">0812-3456-7890</a>.
                        </p>
                    </div>
                </div>
            </x-filament::section>
        </div>

        <!-- Sidebar / Table of Contents equivalent -->
        <div class="lg:col-span-1 hidden lg:block">
            <x-filament::section>
                <h3 class="font-bold mb-4">Pintas Menu</h3>
                <nav class="flex flex-col gap-2">
                    <a href="{{ route('filament.sppg.pages.dashboard') }}" class="text-primary-600 hover:underline text-sm">
                        ← Kembali ke Dashboard
                    </a>
                    <a href="{{ route('filament.sppg.pages.sppg-profile') }}" class="text-gray-600 dark:text-gray-400 hover:text-primary-500 text-sm">
                        Edit Profil SPPG
                    </a>
                </nav>
            </x-filament::section>
        </div>
    </div>
</x-filament-panels::page>
