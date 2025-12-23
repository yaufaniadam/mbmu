@extends('layouts.public')

@section('content')
<div class="bg-white py-24 sm:py-32">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="mx-auto max-w-2xl lg:text-center mb-16">
            <h2 class="text-base font-semibold leading-7 text-blue-600">Orang-orang di Balik Layar</h2>
            <p class="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Tim Pengelola MBM</p>
            <p class="mt-6 text-lg leading-8 text-gray-600">
                Berdedikasi untuk memastikan setiap penyaluran program berjalan amanah, transparan, dan tepat sasaran.
            </p>
        </div>

        <!-- Pimpinan Utama -->
        <div class="mx-auto max-w-4xl mb-20">
            <h3 class="text-2xl font-bold text-gray-900 mb-8 border-b pb-4 text-center">Pimpinan</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Ketua -->
                <div class="text-center group">
                    <div class="relative mx-auto h-40 w-40 rounded-full overflow-hidden mb-4 shadow-lg border-4 border-blue-50">
                        <img class="h-full w-full object-cover group-hover:scale-110 transition duration-500" src="https://ui-avatars.com/api/?name=Ketua&background=0D8ABC&color=fff&size=256" alt="Ketua">
                    </div>
                    <h3 class="mt-2 text-xl font-bold text-gray-900 tracking-tight">Nama Ketua</h3>
                    <p class="text-sm font-semibold text-blue-600">Ketua</p>
                </div>
                
                <!-- Sekretaris -->
                <div class="text-center group">
                     <div class="relative mx-auto h-40 w-40 rounded-full overflow-hidden mb-4 shadow-lg border-4 border-blue-50">
                        <img class="h-full w-full object-cover group-hover:scale-110 transition duration-500" src="https://ui-avatars.com/api/?name=Sekretaris&background=0D8ABC&color=fff&size=256" alt="Sekretaris">
                    </div>
                    <h3 class="mt-2 text-xl font-bold text-gray-900 tracking-tight">Nama Sekretaris</h3>
                    <p class="text-sm font-semibold text-blue-600">Sekretaris</p>
                </div>

                <!-- Bendahara -->
                <div class="text-center group">
                     <div class="relative mx-auto h-40 w-40 rounded-full overflow-hidden mb-4 shadow-lg border-4 border-blue-50">
                        <img class="h-full w-full object-cover group-hover:scale-110 transition duration-500" src="https://ui-avatars.com/api/?name=Bendahara&background=0D8ABC&color=fff&size=256" alt="Bendahara">
                    </div>
                    <h3 class="mt-2 text-xl font-bold text-gray-900 tracking-tight">Nama Bendahara</h3>
                    <p class="text-sm font-semibold text-blue-600">Bendahara</p>
                </div>
            </div>
        </div>

        <!-- Operasional & Support -->
        <div class="mx-auto max-w-5xl">
            <h3 class="text-2xl font-bold text-gray-900 mb-8 border-b pb-4 text-center">Operasional & Teknis</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 justify-center">
                 <!-- Akuntan -->
                <div class="text-center group p-6 bg-gray-50 rounded-2xl hover:bg-gray-100 transition">
                     <div class="mx-auto h-24 w-24 rounded-full overflow-hidden mb-4 bg-gray-200">
                        <img class="h-full w-full object-cover grayscale group-hover:grayscale-0 transition" src="https://ui-avatars.com/api/?name=Akuntan&background=random" alt="Akuntan">
                    </div>
                    <h3 class="mt-2 text-lg font-bold text-gray-900 tracking-tight">Nama Akuntan</h3>
                    <p class="text-sm text-gray-600">Akuntan</p>
                </div>

                <!-- Koordinator Lapangan -->
                <div class="text-center group p-6 bg-gray-50 rounded-2xl hover:bg-gray-100 transition">
                     <div class="mx-auto h-24 w-24 rounded-full overflow-hidden mb-4 bg-gray-200">
                        <img class="h-full w-full object-cover grayscale group-hover:grayscale-0 transition" src="https://ui-avatars.com/api/?name=Korlap&background=random" alt="Koordinator Lapangan">
                    </div>
                    <h3 class="mt-2 text-lg font-bold text-gray-900 tracking-tight">Nama Koordinator</h3>
                    <p class="text-sm text-gray-600">Koordinator Lapangan</p>
                </div>

                <!-- Tim IT -->
                <div class="text-center group p-6 bg-gray-50 rounded-2xl hover:bg-gray-100 transition">
                     <div class="mx-auto h-24 w-24 rounded-full overflow-hidden mb-4 bg-gray-200">
                        <img class="h-full w-full object-cover grayscale group-hover:grayscale-0 transition" src="https://ui-avatars.com/api/?name=Tim+IT&background=random" alt="Tim IT">
                    </div>
                    <h3 class="mt-2 text-lg font-bold text-gray-900 tracking-tight">Nama Tim IT</h3>
                    <p class="text-sm text-gray-600">Tim IT</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
