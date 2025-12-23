@extends('layouts.public')

@section('content')
<div class="bg-white py-24 sm:py-32">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="mx-auto max-w-2xl lg:text-center mb-16">
            <h2 class="text-base font-semibold leading-7 text-blue-600">Tentang Kami</h2>
            <p class="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Profil MBM</p>
            <p class="mt-6 text-lg leading-8 text-gray-600">
                Makan Bergizi Muhammadiyah (MBM) adalah inisiatif strategis untuk mendukung program ketahanan pangan dan gizi nasional melalui jaringan persyarikatan Muhammadiyah.
            </p>
        </div>

        <div class="mx-auto max-w-7xl bg-white/50 border border-gray-100 rounded-3xl p-8 lg:p-12 mb-16 shadow-lg">
            <h3 class="text-2xl font-bold text-gray-900 mb-8 border-b pb-4">Struktur Organisasi</h3>
            
            <!-- Organizational Tree Visual (Simplified) -->
            <div class="relative overflow-x-auto">
                <div class="min-w-[800px] flex flex-col items-center gap-8">
                    <!-- Level 1 -->
                    <div class="flex flex-col items-center">
                        <div class="bg-blue-600 text-white px-8 py-4 rounded-xl shadow-md text-center w-64">
                            <span class="block text-sm opacity-80 mb-1">Pimpinan Pusat</span>
                            <span class="block font-bold">Kornas MBM</span>
                        </div>
                        <div class="h-8 w-px bg-gray-300"></div>
                    </div>

                    <!-- Level 2 -->
                    <div class="flex justify-center gap-16 relative">
                        <!-- Connector Line -->
                        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[60%] h-px bg-gray-300"></div>
                        <div class="absolute top-0 left-[20%] h-8 w-px bg-gray-300"></div>
                        <div class="absolute top-0 right-[20%] h-8 w-px bg-gray-300"></div>
                        
                        <div class="flex flex-col items-center mt-8">
                            <div class="bg-white border md:border-l-4 border-l-blue-500 border-gray-200 px-6 py-3 rounded-lg shadow-sm text-center w-56">
                                <span class="block font-bold text-gray-800">Direktur Eksekutif</span>
                            </div>
                        </div>

                        <div class="flex flex-col items-center mt-8">
                            <div class="bg-white border md:border-l-4 border-l-green-500 border-gray-200 px-6 py-3 rounded-lg shadow-sm text-center w-56">
                                <span class="block font-bold text-gray-800">Badan Gizi Nasional</span>
                                <span class="text-xs text-gray-500">(Mitra Strategis)</span>
                            </div>
                        </div>
                    </div>

                    <!-- Level 3 -->
                    <div class="flex flex-col items-center mt-4">
                        <div class="h-8 w-px bg-gray-300"></div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                             <div class="bg-gray-50 px-6 py-3 rounded-lg text-center border border-gray-200 w-48">
                                <span class="font-medium text-gray-700">Divisi Operasional</span>
                             </div>
                             <div class="bg-gray-50 px-6 py-3 rounded-lg text-center border border-gray-200 w-48">
                                <span class="font-medium text-gray-700">Divisi Keuangan</span>
                             </div>
                             <div class="bg-gray-50 px-6 py-3 rounded-lg text-center border border-gray-200 w-48">
                                <span class="font-medium text-gray-700">Divisi Monev</span>
                             </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mx-auto max-w-2xl text-center">
            <h3 class="text-2xl font-bold text-gray-900 mb-4">Visi & Misi</h3>
            <ul class="text-left space-y-4 text-gray-600 bg-gray-50 p-8 rounded-2xl">
                <li class="flex items-start">
                    <svg class="h-6 w-6 text-blue-500 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Menyediakan makanan bergizi, halal, dan thayyib bagi peserta didik dan kelompok sasaran.</span>
                </li>
                <li class="flex items-start">
                    <svg class="h-6 w-6 text-blue-500 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Memberdayakan ekonomi lokal melalui kemitraan dengan UMKM dan petani setempat.</span>
                </li>
                <li class="flex items-start">
                    <svg class="h-6 w-6 text-blue-500 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Membangun sistem tata kelola bantuan pangan yang transparan dan akuntabel berbasis teknologi.</span>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection
