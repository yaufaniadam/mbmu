<div class="min-h-screen pt-24 pb-12 bg-gradient-to-br from-blue-50 via-white to-emerald-50 dark:from-gray-950 dark:via-gray-900 dark:to-gray-950">
    <div class="max-w-lg mx-auto px-4">
        
        {{-- Header --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-blue-500 to-emerald-500 rounded-2xl shadow-lg mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Pendaftaran Akun MBM</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Daftar untuk mengakses sistem SPPG</p>
        </div>

        {{-- Step Indicator --}}
        <div class="flex items-center justify-center mb-8">
            @foreach([1 => 'Validasi', 2 => 'Isi Data', 3 => 'Selesai'] as $num => $label)
                <div class="flex items-center">
                    <div class="flex flex-col items-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300
                            {{ $step >= $num ? 'bg-blue-600 text-white shadow-lg' : 'bg-gray-200 text-gray-500' }}">
                            @if($step > $num)
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            @else
                                {{ $num }}
                            @endif
                        </div>
                        <span class="text-xs mt-1 {{ $step >= $num ? 'text-blue-600 font-medium' : 'text-gray-400' }}">{{ $label }}</span>
                    </div>
                    @if($num < 3)
                        <div class="w-12 h-0.5 mx-2 {{ $step > $num ? 'bg-blue-600' : 'bg-gray-200' }}"></div>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Card --}}
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-800 overflow-hidden">
            
            {{-- Step 1: Token Validation --}}
            @if($step === 1)
                <div class="p-6">
                    <div class="text-center mb-6">
                        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                            </svg>
                        </div>
                        <h2 class="text-lg font-semibold text-gray-900">Masukkan Kode Registrasi</h2>
                        <p class="text-sm text-gray-500 mt-1">Kode diberikan oleh admin atau PJ SPPG Anda</p>
                    </div>

                    <form wire:submit.prevent="validateToken">
                        <div class="mb-4">
                            <input 
                                type="text" 
                                wire:model="tokenCode"
                                placeholder="Contoh: ABCD1234"
                                class="w-full px-4 py-3 text-center text-lg font-mono uppercase tracking-wider bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition"
                                maxlength="10"
                                autofocus
                            >
                        </div>

                        @if($tokenError)
                            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl">
                                <div class="flex items-center text-red-600">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="text-sm">{{ $tokenError }}</span>
                                </div>
                            </div>
                        @endif

                        <button 
                            type="submit"
                            class="w-full py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-xl hover:from-blue-700 hover:to-blue-800 transition shadow-lg shadow-blue-200"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-50 cursor-wait"
                        >
                            <span wire:loading.remove>Lanjutkan</span>
                            <span wire:loading>Memvalidasi...</span>
                        </button>
                    </form>
                </div>
            @endif

            {{-- Step 2: Registration Form --}}
            @if($step === 2 && $registrationToken)
                <div class="p-6">
                    {{-- SPPG Info Banner --}}
                    <div class="bg-gradient-to-r from-emerald-50 to-blue-50 dark:from-gray-800 dark:to-gray-800 border border-emerald-200 dark:border-emerald-900 rounded-xl p-4 mb-6 text-gray-900 dark:text-gray-100">
                        <div class="flex items-start">
                            <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Mendaftar sebagai</p>
                                <p class="font-bold">{{ $roleLabels[$role] ?? $role }}</p>
                                <p class="text-sm text-emerald-700 dark:text-emerald-400 font-medium mt-1">{{ $registrationToken->sppg->nama_sppg }}</p>
                            </div>
                        </div>
                    </div>

                    <form wire:submit.prevent="register" onsubmit="return false;" x-data="{ showPassword: false }">
                        @if(session('error'))
                            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-red-600 text-sm">
                                {{ session('error') }}
                            </div>
                        @endif

                        {{-- Nama --}}
                        @if($hasTokenData && $name)
                            <div class="mb-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700">
                                <label class="block text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1">Nama Lengkap</label>
                                <p class="text-gray-900 dark:text-white font-medium">{{ $name }}</p>
                            </div>
                        @else
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Lengkap *</label>
                                <input 
                                    type="text" 
                                    wire:model="name"
                                    placeholder="Masukkan nama lengkap"
                                    class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition"
                                >
                                @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        @endif

                        {{-- No HP --}}
                        @if($hasTokenData && $telepon)
                            <div class="mb-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700">
                                <label class="block text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1">Nomor HP (WhatsApp)</label>
                                <p class="text-gray-900 dark:text-white font-medium">+62 {{ $telepon }}</p>
                            </div>
                        @else
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nomor HP (WhatsApp) *</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">+62</span>
                                    <input 
                                        type="tel" 
                                        wire:model="telepon"
                                        placeholder="81234567890"
                                        class="w-full pl-12 pr-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition"
                                    >
                                </div>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Akan digunakan untuk login & menerima notifikasi</p>
                                @error('telepon') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        @endif

                        {{-- Email (Optional) --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email <span class="text-gray-400">(opsional)</span></label>
                            <input 
                                type="email" 
                                wire:model="email"
                                placeholder="email@contoh.com"
                                class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition"
                            >
                            @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- Password --}}
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password *</label>
                            <div class="relative">
                                <input 
                                    :type="showPassword ? 'text' : 'password'" 
                                    wire:model="password"
                                    placeholder="Masukkan password untuk login"
                                    class="w-full px-4 py-3 pr-12 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition"
                                >
                                <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                                    <span class="material-symbols-outlined text-xl" x-show="!showPassword">visibility</span>
                                    <span class="material-symbols-outlined text-xl" x-show="showPassword" x-cloak>visibility_off</span>
                                </button>
                            </div>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Minimal 8 karakter</p>
                            @error('password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>



                        <button 
                            type="submit"
                            class="w-full py-3 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white font-semibold rounded-xl hover:from-emerald-700 hover:to-emerald-800 transition shadow-lg shadow-emerald-200"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-50 cursor-wait"
                        >
                            <span wire:loading.remove>Aktivasi Akun</span>
                            <span wire:loading>Memproses...</span>
                        </button>
                    </form>
                </div>
            @endif

            {{-- Step 3: Success --}}
            @if($step === 3)
                <div class="p-6 text-center">
                    <div class="w-20 h-20 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg shadow-emerald-200">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>

                    <div class="mb-4">
                        <h2 class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mb-2">Aktivasi Berhasil! 🎉</h2>
                        <p class="text-gray-600 dark:text-gray-400">Akun Anda telah diaktifkan dan siap digunakan.</p>
                    </div>

                    {{-- Credentials Card --}}
                    <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4 mb-6 text-left">
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Informasi Login Anda:</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500 dark:text-gray-400">Nomor HP:</span>
                                <span class="font-mono font-semibold text-gray-900 dark:text-white">{{ $telepon }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500 dark:text-gray-400">Password:</span>
                                <span class="font-mono font-semibold text-gray-900 dark:text-white">{{ $generatedPassword }}</span>
                            </div>
                        </div>
                        <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                            <p class="text-xs text-emerald-600 dark:text-emerald-400 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                                Info login juga dikirim ke WhatsApp Anda
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <a 
                            href="{{ route('filament.sppg.auth.login') }}"
                            class="inline-flex items-center justify-center py-3 bg-white dark:bg-gray-800 text-blue-600 dark:text-blue-400 font-semibold rounded-xl border-2 border-blue-600 dark:border-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition shadow-md"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            Login SPPG
                        </a>

                        <a 
                            href="{{ route('filament.lembaga.auth.login') }}"
                            class="inline-flex items-center justify-center py-3 bg-blue-600 dark:bg-blue-700 text-white font-semibold rounded-xl hover:bg-blue-700 dark:hover:bg-blue-800 transition shadow-lg shadow-blue-200 dark:shadow-none"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Login Lembaga
                        </a>
                    </div>
                </div>
            @endif

        </div>

        {{-- Help Text --}}
        <div class="text-center mt-6 text-sm text-gray-500">
            <p>Butuh bantuan? Hubungi Admin Kornas MBM</p>
        </div>

    </div>
</div>
