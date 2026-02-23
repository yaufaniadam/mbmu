<div class="min-h-[80vh] flex items-center justify-center px-4 py-12">
    <div class="max-w-md w-full space-y-8 bg-surface-light dark:bg-surface-dark p-8 rounded-2xl shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-800">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-text-main dark:text-white">
                Lupa Password?
            </h2>
            <p class="mt-2 text-center text-sm text-text-secondary">
                Masukkan nomor WhatsApp Anda untuk mendapatkan link reset password.
            </p>
        </div>

        @if ($success)
            <div class="rounded-xl bg-green-50 dark:bg-green-900/30 p-4 border border-green-200 dark:border-green-800 animate-fade-in">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <span class="material-symbols-outlined text-green-500 text-2xl">check_circle</span>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-green-800 dark:text-green-300">
                            Berhasil!
                        </h3>
                        <div class="mt-2 text-sm text-green-700 dark:text-green-400">
                            <p>Link reset password telah dikirim ke WhatsApp Anda. Silakan cek pesan Anda dan ikuti instruksi yang ada.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 flex justify-center">
                <a href="{{ route('filament.sppg.auth.login') }}" class="text-primary hover:text-primary-dark font-medium text-sm flex items-center gap-2">
                    <span class="material-symbols-outlined text-base">arrow_back</span>
                    Kembali ke Login
                </a>
            </div>
        @else
            <form class="mt-8 space-y-6" wire:submit.prevent="sendResetLink">
                <div class="rounded-md shadow-sm space-y-4">
                    <div>
                        <label for="telepon" class="block text-sm font-medium text-text-main dark:text-white mb-2">
                            Nomor WhatsApp
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="material-symbols-outlined text-gray-400 text-xl">phone</span>
                            </div>
                            <input wire:model.defer="telepon" id="telepon" name="telepon" type="tel" required 
                                class="appearance-none block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-700 placeholder-gray-400 text-text-main dark:text-white dark:bg-gray-800 rounded-xl focus:outline-none focus:ring-primary focus:border-primary sm:text-sm transition-all" 
                                placeholder="Contoh: 0812xxxxxx">
                        </div>
                        @error('telepon') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                @if ($error)
                    <div class="rounded-xl bg-red-50 dark:bg-red-900/30 p-4 border border-red-200 dark:border-red-800">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <span class="material-symbols-outlined text-red-500 text-2xl">error</span>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700 dark:text-red-400">
                                    {{ $error }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <div>
                    <button type="submit" wire:loading.attr="disabled"
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-xl text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading.remove>Kirim Link Reset Password</span>
                        <span wire:loading class="flex items-center gap-2">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Memproses...
                        </span>
                    </button>
                </div>
            </form>
            
            <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-800 text-center flex flex-col gap-2">
                <a href="{{ route('filament.sppg.auth.login') }}" class="text-sm font-bold text-primary hover:text-primary-dark">
                    Kembali ke Login
                </a>
                <a href="{{ route('claim.account') }}" class="text-xs text-text-secondary hover:text-text-main">
                    Belum aktivasi akun? Klik di sini
                </a>
            </div>
        @endif
    </div>
</div>
