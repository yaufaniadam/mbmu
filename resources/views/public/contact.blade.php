@extends('layouts.public')

@section('content')
<div class="bg-gray-50 py-24 sm:py-32 min-h-screen">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="mx-auto max-w-2xl lg:text-center mb-16">
            <h2 class="text-base font-semibold leading-7 text-blue-600">Hubungi Kami</h2>
            <p class="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Kontak & Lokasi</p>
        </div>

        <div class="mx-auto max-w-4xl bg-white rounded-3xl shadow-xl overflow-hidden">
            <div class="grid grid-cols-1 md:grid-cols-2">
                <!-- Contact Info -->
                <div class="p-8 lg:p-12 bg-blue-600 text-white flex flex-col justify-between">
                    <div>
                        <h3 class="text-2xl font-bold mb-6">Kantor Pusat</h3>
                        <div class="space-y-6">
                            <div class="flex items-start gap-4">
                                <svg class="h-6 w-6 text-blue-200 mt-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <div>
                                    <p class="font-medium text-blue-100 mb-1">Alamat:</p>
                                    <p class="font-semibold text-lg leading-relaxed">
                                        Gedung PP Muhammadiyah<br>
                                        Jl. K.H. Ahmad Dahlan<br>
                                        Yogyakarta
                                    </p>
                                </div>
                            </div>
                            
                            <div class="flex items-start gap-4">
                                <svg class="h-6 w-6 text-blue-200 mt-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <div>
                                    <p class="font-medium text-blue-100 mb-1">Email:</p>
                                    <p class="font-semibold">mbm@muhammadiyah.or.id</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <svg class="h-6 w-6 text-blue-200 mt-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <div>
                                    <p class="font-medium text-blue-100 mb-1">Telepon:</p>
                                    <p class="font-semibold">(0274) 123456</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Map Placeholder (Functional Iframe can be added later) -->
                <div class="bg-gray-200 h-96 md:h-auto min-h-[400px] relative">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3952.9698973792074!2d110.36034131477793!3d-7.801194994378129!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a5788d4001d71%3A0x6c6670860533100c!2sGedung%20Pimpinan%20Pusat%20Muhammadiyah!5e0!3m2!1sen!2sid!4v1672345678901!5m2!1sen!2sid" 
                        width="100%" 
                        height="100%" 
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
