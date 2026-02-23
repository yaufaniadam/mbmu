<div class="flex items-center justify-center min-h-[80vh]">
    <div class="w-full max-w-sm bg-white rounded-lg material-shadow-2 overflow-hidden">
        <div class="bg-primary p-6 text-center">
            <div class="bg-white/20 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 backdrop-blur-sm">
                <span class="material-icons text-white text-3xl">local_shipping</span>
            </div>
            <h2 class="text-white text-xl font-medium">Login Pengantaran</h2>
            <p class="text-white/80 text-sm mt-1">Masuk untuk melihat tugas</p>
        </div>

        <div class="p-8">
            <form wire:submit.prevent="login">
                <!-- Login Input -->
                <div class="relative z-0 w-full mb-6 group">
                    <input type="text" wire:model="login" name="login" id="login" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-primary peer material-input" placeholder=" " required />
                    <label for="login" class="peer-focus:font-medium absolute text-sm text-gray-500 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-primary peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">No. HP atau Email</label>
                    @error('login') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Password Input -->
                <div class="relative z-0 w-full mb-6 group">
                    <input type="password" wire:model="password" name="password" id="password" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-primary peer material-input" placeholder=" " required />
                    <label for="password" class="peer-focus:font-medium absolute text-sm text-gray-500 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-primary peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Password</label>
                    <div class="flex justify-end mt-1">
                        <a href="{{ route('password.request') }}" class="text-xs text-primary hover:text-primary-dark">Lupa password?</a>
                    </div>
                    @error('password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center mb-6">
                    <input id="remember" wire:model="remember" type="checkbox" class="w-4 h-4 text-primary bg-gray-100 border-gray-300 rounded focus:ring-primary focus:ring-2">
                    <label for="remember" class="ml-2 text-sm font-medium text-gray-500">Ingat Saya</label>
                </div>

                <button type="submit" class="text-white bg-primary hover:bg-primary-dark focus:ring-4 focus:outline-none focus:ring-primary/30 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center material-btn w-full shadow-md hover:shadow-lg">
                    <span wire:loading.remove>MASUK</span>
                    <span wire:loading>LOADING...</span>
                </button>
            </form>
        </div>
    </div>
</div>
