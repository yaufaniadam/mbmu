@extends('layouts.public')

@section('content')
<section class="w-full relative group" x-data="{
    activeSlide: 0,
    slides: {{ $sliders->count() }},
    interval: null,
    init() {
        this.startInterval();
    },
    startInterval() {
        this.interval = setInterval(() => {
            this.next();
        }, 5000);
    },
    stopInterval() {
        clearInterval(this.interval);
    },
    prev() {
        this.activeSlide = (this.activeSlide - 1 + this.slides) % this.slides;
        this.scrollTo(this.activeSlide);
    },
    next() {
        this.activeSlide = (this.activeSlide + 1) % this.slides;
        this.scrollTo(this.activeSlide);
    },
    scrollTo(index) {
        this.activeSlide = index;
        this.$refs.slider.scrollTo({
            left: this.$refs.slider.offsetWidth * index,
            behavior: 'smooth'
        });
    }
}" @mouseenter="stopInterval" @mouseleave="startInterval">
    <!-- Left Arrow -->
    <button @click="prev()" class="absolute z-10 left-4 top-1/2 -translate-y-1/2 bg-black/20 hover:bg-black/40 text-white p-3 rounded-full backdrop-blur-sm transition-all hidden md:flex items-center justify-center group-hover:opacity-100 opacity-0 duration-300">
        <span class="material-symbols-outlined text-3xl">chevron_left</span>
    </button>
    
    <!-- Right Arrow -->
    <button @click="next()" class="absolute z-10 right-4 top-1/2 -translate-y-1/2 bg-black/20 hover:bg-black/40 text-white p-3 rounded-full backdrop-blur-sm transition-all hidden md:flex items-center justify-center group-hover:opacity-100 opacity-0 duration-300">
        <span class="material-symbols-outlined text-3xl">chevron_right</span>
    </button>
    <div x-ref="slider" class="flex overflow-x-auto snap-x snap-mandatory no-scrollbar h-[500px] md:h-[600px] w-full scroll-smooth">
        @forelse($sliders as $slider)
        <div class="min-w-full snap-center relative h-full bg-gray-200">
            <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ Storage::disk('public')->url($slider->image_path) }}');">
            </div>
            <div class="absolute inset-0 bg-gradient-to-r from-black/70 to-black/30 flex items-center">
                <div class="px-4 md:px-40 w-full max-w-[1280px] mx-auto">
                    <div class="max-w-[720px] flex flex-col gap-6">
                        <h1 class="text-white text-4xl md:text-6xl font-black leading-tight tracking-tight">
                            {!! nl2br(e($slider->title)) !!}
                        </h1>
                        @if($slider->description)
                        <p class="text-white/90 text-lg md:text-xl font-normal leading-relaxed max-w-[600px]">
                            {{ $slider->description }}
                        </p>
                        @endif
                        
                    </div>
                </div>
            </div>
        </div>
        @empty
        <!-- Fallback if no sliders in database -->
        <div class="min-w-full snap-center relative h-full bg-gray-200">
            <div class="absolute inset-0 bg-gradient-to-r from-primary/20 to-secondary-green/20 flex items-center justify-center">
                <div class="text-center text-text-main dark:text-white">
                    <p class="text-xl">Belum ada slider</p>
                </div>
            </div>
        </div>
        @endforelse
    </div>
    <div class="absolute bottom-6 left-0 right-0 flex justify-center gap-2">
        @foreach($sliders as $index => $slider)
            <button @click="scrollTo({{ $index }})"
                class="w-3 h-3 rounded-full transition-colors shadow-sm focus:outline-none"
                :class="activeSlide === {{ $index }} ? 'bg-primary' : 'bg-white/50 hover:bg-white'">
            </button>
        @endforeach
    </div>
</section>

<section class="py-16 px-4 md:px-10 bg-surface-light dark:bg-surface-dark w-full">
    <div class="max-w-[960px] mx-auto flex flex-col gap-12">
        <div class="text-center flex flex-col gap-4 items-center">
            <h2 class="text-3xl md:text-4xl font-bold text-text-main dark:text-white leading-tight">Nilai Kami</h2>
            <p class="text-text-secondary text-lg max-w-2xl">
                Kami berkomitmen pada standar yang ketat dan dukungan komunitas untuk memberikan yang terbaik untuk anak-anak kami.
            </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @forelse($features as $feature)
            <div class="flex flex-col items-center text-center gap-4 rounded-xl border border-[#e6e1db] dark:border-gray-800 bg-white dark:bg-surface-dark p-8 shadow-sm hover:shadow-md transition-shadow">
                @if($feature->icon)
                <div class="size-16 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 flex items-center justify-center mb-2">
                    <span class="material-symbols-outlined text-3xl">{{ $feature->icon }}</span>
                </div>
                @endif
                <h3 class="text-xl font-bold text-text-main dark:text-white">{{ $feature->title }}</h3>
                @if($feature->description)
                <p class="text-text-secondary dark:text-gray-400">{{ $feature->description }}</p>
                @endif
            </div>
            @empty
            <!-- Fallback if no features -->
            <div class="col-span-full text-center py-12">
                <p class="text-gray-500 dark:text-gray-400">Belum ada fitur yang ditampilkan.</p>
            </div>
            @endforelse
        </div>
    </div>
</section>


@endsection
