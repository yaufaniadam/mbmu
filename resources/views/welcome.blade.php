@extends('layouts.public')

@section('content')
<!-- Hero Section -->
<section class="bg-center bg-no-repeat bg-cover bg-gray-700 bg-blend-multiply" style="background-image: url('{{ asset('hero-children-wide.png') }}');">
    <div class="px-4 mx-auto max-w-screen-xl text-center py-24 lg:py-56">
        <h1 class="mb-4 text-4xl font-extrabold tracking-tight leading-none text-white md:text-5xl lg:text-6xl">
            <span class="text-blue-400">Makan Bergizi Muhammadiyah</span><br>
            Untuk Generasi Masa Depan
        </h1>
        <p class="mb-8 text-lg font-normal text-gray-300 lg:text-xl sm:px-16 lg:px-48">
            Sistem manajemen terintegrasi untuk pengelolaan Program Makan Bergizi Muhammadiyah (MBM). Transparan, Akuntabel, dan Berkelanjutan.
        </p>
        <div class="flex flex-col space-y-4 sm:flex-row sm:justify-center sm:space-y-0">
            <a href="/admin/login" class="inline-flex justify-center items-center py-3 px-5 text-base font-medium text-center text-white rounded-lg bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-900">
                <svg class="w-3.5 h-3.5 mr-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Masuk sebagai Kornas
            </a>
            <a href="/sppg/login" class="inline-flex justify-center items-center py-3 px-5 sm:ms-4 text-base font-medium text-center text-white rounded-lg bg-pink-600 hover:bg-pink-700 focus:ring-4 focus:ring-pink-300 dark:focus:ring-pink-900">
                <svg class="w-3.5 h-3.5 mr-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                Masuk sebagai SPPG
            </a>  
            <a href="/production/login" class="inline-flex justify-center items-center py-3 px-5 sm:ms-4 text-base font-medium text-center text-white rounded-lg bg-emerald-600 hover:bg-emerald-700 focus:ring-4 focus:ring-emerald-300 dark:focus:ring-emerald-900">
                <svg class="w-3.5 h-3.5 mr-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                     <path d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                </svg>
                Masuk Tim Produksi
            </a>
        </div>
    </div>
</section>

<!-- Stats/Features Grid -->
<div id="about" class="py-16 bg-white dark:bg-gray-900">
    <div class="max-w-screen-xl mx-auto px-4 lg:px-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Card 1 -->
            <div class="max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700 transition duration-300">
                <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center mb-4 dark:bg-blue-900 dark:text-blue-300">
                     <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <a href="#">
                    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Manajemen Relawan</h5>
                </a>
                <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">Pengelolaan data relawan yang terstruktur mulai dari tingkat pusat hingga unit pelayanan.</p>
            </div>

            <!-- Card 2 -->
            <div class="max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700 transition duration-300">
                <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center mb-4 dark:bg-emerald-900 dark:text-emerald-300">
                    <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <a href="#">
                    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Transparansi Dana</h5>
                </a>
                <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">Pencatatan dan pelaporan arus dana yang akuntabel dan dapat dipertanggungjawabkan.</p>
            </div>

            <!-- Card 3 -->
            <div class="max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700 transition duration-300">
                <div class="w-12 h-12 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center mb-4 dark:bg-purple-900 dark:text-purple-300">
                     <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                </div>
                <a href="#">
                    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Pelaporan Real-time</h5>
                </a>
                <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">Monitor distribusi makanan dan kegiatan operasional secara langsung melalui dashboard.</p>
            </div>
        </div>
    </div>
</div>
<!-- Blog Section -->
<section class="bg-gray-900 py-16">
    <div class="max-w-screen-xl mx-auto px-4 lg:px-6">
        <h2 class="mb-8 text-3xl font-extrabold tracking-tight text-white">Artikel Terkait</h2>
        <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
            @forelse($posts as $post)
                <article class="flex flex-col h-full bg-gray-900 border-none">
                    <a href="{{ route('blog.public.show', $post->slug) }}">
                         @if($post->featured_image)
                            <img class="rounded-lg mb-5 object-cover w-full h-48 hover:opacity-90 transition" src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->title }}">
                        @else
                            <div class="rounded-lg mb-5 w-full h-48 bg-gray-800 flex items-center justify-center hover:bg-gray-700 transition">
                                <svg class="w-12 h-12 text-gray-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path></svg>
                            </div>
                        @endif
                    </a>
                    <h2 class="mb-2 text-xl font-bold tracking-tight text-white hover:underline">
                        <a href="{{ route('blog.public.show', $post->slug) }}">{{ $post->title }}</a>
                    </h2>
                    <p class="mb-4 font-light text-gray-400 text-sm flex-grow line-clamp-3">
                        {{ $post->excerpt ?? Str::limit(strip_tags($post->content), 100) }}
                    </p>
                    <a href="{{ route('blog.public.show', $post->slug) }}" class="inline-flex items-center font-medium text-blue-500 hover:underline text-sm">
                        Baca selengkapnya
                    </a>
                </article>
            @empty
                <div class="col-span-full text-center text-gray-400 py-10">
                    Belum ada artikel terbaru.
                </div>
            @endforelse
        </div>
    </div>
</section>

@endsection
