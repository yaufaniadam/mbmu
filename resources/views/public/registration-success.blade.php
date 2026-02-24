@extends('layouts.public')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center bg-gradient-to-br from-blue-50 via-white to-emerald-50 dark:from-gray-950 dark:via-gray-900 dark:to-gray-950 px-4">
    <div class="max-w-lg w-full bg-white dark:bg-gray-900 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-800 p-8 text-center">
        <div class="w-20 h-20 bg-emerald-100 dark:bg-emerald-900/50 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>

        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Aktivasi Berhasil! 🎉</h1>
        <p class="text-gray-600 dark:text-gray-400 mb-8">Selamat! Akun Anda telah berhasil diaktifkan. Silakan login ke portal yang sesuai di bawah ini.</p>

        <div class="grid grid-cols-1 gap-4 mb-8">
            <a 
                href="{{ route('filament.sppg.auth.login') }}"
                class="flex items-center justify-center gap-3 py-4 bg-white dark:bg-gray-800 text-blue-600 dark:text-blue-400 font-bold rounded-2xl border-2 border-blue-600 dark:border-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition shadow-sm"
            >
                <span class="material-symbols-outlined">corporate_fare</span>
                Login Portal SPPG
            </a>

            <a 
                href="{{ route('filament.lembaga.auth.login') }}"
                class="flex items-center justify-center gap-3 py-4 bg-blue-600 dark:bg-blue-700 text-white font-bold rounded-2xl hover:bg-blue-700 dark:hover:bg-blue-800 transition shadow-lg shadow-blue-200 dark:shadow-none"
            >
                <span class="material-symbols-outlined">business_center</span>
                Login Portal Lembaga
            </a>
        </div>

        <div class="pt-6 border-t border-gray-100 dark:border-gray-800">
            <p class="text-sm text-gray-500 mb-4">Informasi login juga telah kami kirimkan melalui WhatsApp.</p>
            <a href="{{ url('/') }}" class="text-blue-600 dark:text-blue-400 font-medium hover:underline flex items-center justify-center gap-1">
                <span class="material-symbols-outlined text-sm">arrow_back</span>
                Kembali ke Beranda
            </a>
        </div>
    </div>
</div>
@endsection
