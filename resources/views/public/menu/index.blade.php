@extends('layouts.public')

@section('content')
<div class="bg-gray-50 dark:bg-background-dark py-12">
    <div class="max-w-[1280px] mx-auto px-4 md:px-10">
        <!-- Header -->
        <div class="mb-10 text-center">
            <h1 class="text-3xl md:text-4xl font-bold text-text-main dark:text-white mb-4">Menu Makanan Sehat</h1>
            <p class="text-lg text-text-secondary max-w-3xl mx-auto">
                Lihat daftar menu makanan bergizi yang disajikan oleh SPPG di seluruh Indonesia.
            </p>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-surface-dark p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800 mb-10">
            <form action="{{ route('menu.public.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                
                <!-- Province Filter -->
                <div>
                    <label for="province" class="block text-sm font-medium text-text-main dark:text-white mb-2">Provinsi</label>
                    <select name="province" id="province" onchange="this.form.submit()" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-text-main dark:text-white focus:border-primary focus:ring-primary shadow-sm text-sm">
                        <option value="">Semua Provinsi</option>
                        @foreach($provinces as $code => $name)
                            <option value="{{ $code }}" {{ request('province') == $code ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- City Filter -->
                <div>
                    <label for="city" class="block text-sm font-medium text-text-main dark:text-white mb-2">Kabupaten/Kota</label>
                    <select name="city" id="city" onchange="this.form.submit()" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-text-main dark:text-white focus:border-primary focus:ring-primary shadow-sm text-sm" {{ empty(request('province')) && empty(request('city')) ? 'disabled' : '' }}>
                        <option value="">Semua Kab/Kota</option>
                        @foreach($cities as $code => $name)
                            <option value="{{ $code }}" {{ request('city') == $code ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- SPPG Filter -->
                <div>
                    <label for="sppg" class="block text-sm font-medium text-text-main dark:text-white mb-2">SPPG</label>
                    <select name="sppg" id="sppg" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-text-main dark:text-white focus:border-primary focus:ring-primary shadow-sm text-sm">
                        <option value="">Semua SPPG</option>
                        @foreach($sppgs as $id => $name)
                            <option value="{{ $id }}" {{ request('sppg') == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Date Filter -->
                <div>
                    <label for="date" class="block text-sm font-medium text-text-main dark:text-white mb-2">Tanggal Upload</label>
                    <input type="date" name="date" id="date" value="{{ request('date') }}" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-text-main dark:text-white focus:border-primary focus:ring-primary shadow-sm text-sm">
                </div>

                <!-- Submit Button -->
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-primary hover:bg-primary-dark text-white font-bold py-2.5 px-4 rounded-lg transition-colors shadow-sm flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-xl">search</span>
                        Cari Menu
                    </button>
                </div>
            </form>
            @if(request()->anyFilled(['province', 'city', 'sppg', 'date']))
                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <a href="{{ route('menu.public.index') }}" class="text-primary hover:text-primary-dark text-sm font-medium flex items-center gap-1">
                        <span class="material-symbols-outlined text-lg">close</span> Reset Filter
                    </a>
                </div>
            @endif
        </div>

        <!-- Menu Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($menus as $menu)
                <div class="bg-white dark:bg-surface-dark rounded-xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden group border border-gray-100 dark:border-gray-800 flex flex-col h-full">
                    <div class="relative overflow-hidden aspect-[4/3]">
                        @if($menu->image)
                            <img src="{{ Storage::url($menu->image) }}" alt="{{ $menu->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-full h-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-400">
                                <span class="material-symbols-outlined text-5xl">restaurant_menu</span>
                            </div>
                        @endif
                        <div class="absolute top-3 right-3 bg-white/90 dark:bg-black/70 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold shadow-sm text-text-main dark:text-white">
                             {{ $menu->created_at->format('d M Y') }}
                        </div>
                    </div>
                    
                    <div class="p-5 flex flex-col flex-1 gap-3">
                        <div class="flex items-start justify-between gap-2">
                             <h3 class="font-bold text-lg text-text-main dark:text-white line-clamp-2 leading-tight group-hover:text-primary transition-colors">
                                {{ $menu->name ?? 'Menu Tanpa Nama' }}
                            </h3>
                        </div>

                       <div class="flex items-center gap-2 text-xs font-medium text-text-secondary dark:text-gray-400 bg-gray-50 dark:bg-gray-800 p-2 rounded-lg">
                            <span class="material-symbols-outlined text-base">storefront</span>
                            <span class="truncate">{{ $menu->sppg->nama_sppg }}</span>
                        </div>

                        <div class="flex items-center gap-2 text-xs text-text-secondary dark:text-gray-400">
                            <span class="material-symbols-outlined text-base">location_on</span>
                            <span class="truncate">
                                {{ \Illuminate\Support\Str::title($menu->sppg->city->name ?? '-') }}, {{ \Illuminate\Support\Str::title($menu->sppg->province->name ?? '-') }}
                            </span>
                        </div>

                        @if($menu->description)
                            <p class="text-sm text-text-secondary dark:text-gray-400 line-clamp-3 mt-1">
                                {{ $menu->description }}
                            </p>
                        @endif
                        
                         <!-- Optional: View Details Button -->
                         <!-- 
                         <div class="mt-auto pt-4">
                            <a href="#" class="block w-full text-center py-2 rounded-lg border border-primary text-primary hover:bg-primary hover:text-white transition-colors text-sm font-bold">
                                Detail Menu
                            </a>
                         </div> 
                         -->
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center">
                    <div class="mb-4 inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-400">
                        <span class="material-symbols-outlined text-3xl">search_off</span>
                    </div>
                    <h3 class="text-xl font-bold text-text-main dark:text-white mb-2">Tidak ada menu ditemukan</h3>
                    <p class="text-text-secondary">Coba sesuaikan filter pencarian Anda.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-10">
            {{ $menus->links() }}
        </div>
    </div>
</div>
@endsection
