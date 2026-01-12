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
                        @if($slider->link_url)
                        <div class="flex gap-4 pt-4">
                            <a href="{{ $slider->link_url }}" class="h-12 px-8 text-white text-base font-bold rounded-lg transition-all shadow-lg inline-flex items-center justify-center hover:shadow-xl hover:scale-105" style="background: linear-gradient(45deg, #1C3B7C 0%, #1C3B7C 60%, #1E8657 100%);">
                                Selengkapnya
                            </a>
                        </div>
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

<section class="py-16 px-4 md:px-10 bg-[#fbfbf9] dark:bg-background-dark w-full">
    <div class="max-w-[1280px] mx-auto flex flex-col gap-10">
        <div class="flex flex-col md:flex-row justify-between items-end gap-4 border-b border-[#e6e1db] dark:border-gray-800 pb-6">
            <div>
                <h2 class="text-[#181511] dark:text-white text-3xl font-bold leading-tight tracking-[-0.015em] mb-2">Berita & Update</h2>
                <p class="text-[#686868] text-base">Berita dan Update Kornas Makan Bergizi Muhammadiyah</p>
            </div>
            <a class="text-primary font-bold hover:underline flex items-center gap-1" href="{{ url('/artikel') }}">
                Lihat Semua <span class="material-symbols-outlined text-sm">arrow_forward</span>
            </a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @forelse($posts as $post)
            <article class="flex flex-col rounded-lg overflow-hidden bg-white dark:bg-surface-dark shadow-[0_2px_8px_rgba(0,0,0,0.08)] hover:-translate-y-1 transition-transform duration-300">
                <a href="{{ route('blog.public.show', $post->slug) }}" class="w-full aspect-video bg-cover bg-center block" data-alt="{{ $post->title }}" style="background-image: url('{{ $post->featured_image ? Storage::url($post->featured_image) : 'https://images.unsplash.com/photo-1547592180-85f173990554?w=600&q=80' }}');">
                </a>
                <div class="p-5 flex flex-col flex-1 gap-3">
                    <!-- <span class="text-xs font-bold text-secondary-green uppercase tracking-wide">News</span> -->
                    <h3 class="text-lg font-bold text-text-main dark:text-white leading-snug">
                        <a href="{{ route('blog.public.show', $post->slug) }}" class="hover:text-primary transition-colors">{{ $post->title }}</a>
                    </h3>
                    <p class="text-sm text-text-secondary line-clamp-3">{{ $post->excerpt }}</p>
                    <div class="mt-auto pt-2">
                        <a class="text-sm font-bold text-primary hover:text-primary-dark" href="{{ route('blog.public.show', $post->slug) }}">Baca Selengkapnya</a>
                    </div>
                </div>
            </article>
        @empty
            <div class="col-span-1 sm:col-span-2 lg:col-span-4 text-center py-10">
                <p class="text-text-secondary">Belum ada berita yang ditampilkan.</p>
            </div>
        @endforelse
        </div>
    </div>
</section>

{{-- Testimony Section (Hidden for now) --}}
{{-- <section class="py-20 px-4 md:px-10 bg-orange-50 dark:bg-background-dark w-full border-t border-orange-100 dark:border-gray-800">
    <div class="max-w-[1280px] mx-auto flex flex-col gap-12">
        <div class="text-center max-w-3xl mx-auto flex flex-col gap-4">
            <span class="text-primary font-bold uppercase tracking-wider text-sm">Community Voices</span>
            <h2 class="text-3xl md:text-4xl font-bold text-text-main dark:text-white leading-tight">Stories from Our School Family</h2>
            <p class="text-text-secondary text-lg">
                Hear directly from the parents and teachers witnessing the daily transformation in our children's lives.
            </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white dark:bg-surface-dark p-8 rounded-2xl shadow-sm hover:shadow-md transition-shadow border border-orange-100 dark:border-gray-800 flex flex-col gap-6">
                <div class="text-primary flex gap-1">
                    <span class="material-symbols-outlined text-xl text-yellow-400 fill-current">star</span>
                    <span class="material-symbols-outlined text-xl text-yellow-400 fill-current">star</span>
                    <span class="material-symbols-outlined text-xl text-yellow-400 fill-current">star</span>
                    <span class="material-symbols-outlined text-xl text-yellow-400 fill-current">star</span>
                    <span class="material-symbols-outlined text-xl text-yellow-400 fill-current">star</span>
                </div>
                <blockquote class="text-text-main dark:text-white text-lg font-medium leading-relaxed flex-1 italic">
                    "Since the free meal program started, my son is much more active and focuses better in class. It's a huge relief knowing he gets a healthy, balanced lunch every single day."
                </blockquote>
                <div class="flex items-center gap-4 mt-auto">
                    <div class="h-12 w-12 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xl border-2 border-white shadow-sm">
                        SA
                    </div>
                    <div>
                        <div class="font-bold text-text-main dark:text-white">Siti Aminah</div>
                        <div class="text-sm text-text-secondary font-medium">Orang Tua Siswa</div>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-surface-dark p-8 rounded-2xl shadow-sm hover:shadow-md transition-shadow border border-orange-100 dark:border-gray-800 flex flex-col gap-6">
                <div class="text-primary flex gap-1">
                    <span class="material-symbols-outlined text-xl text-yellow-400 fill-current">star</span>
                    <span class="material-symbols-outlined text-xl text-yellow-400 fill-current">star</span>
                    <span class="material-symbols-outlined text-xl text-yellow-400 fill-current">star</span>
                    <span class="material-symbols-outlined text-xl text-yellow-400 fill-current">star</span>
                    <span class="material-symbols-outlined text-xl text-yellow-400 fill-current">star</span>
                </div>
                <blockquote class="text-text-main dark:text-white text-lg font-medium leading-relaxed flex-1 italic">
                    "Attendance has improved significantly. Students are no longer lethargic in the afternoon sessions. The difference in their energy levels and participation is night and day."
                </blockquote>
                <div class="flex items-center gap-4 mt-auto">
                    <div class="h-12 w-12 rounded-full bg-green-100 text-green-600 flex items-center justify-center font-bold text-xl border-2 border-white shadow-sm">
                        BS
                    </div>
                    <div>
                        <div class="font-bold text-text-main dark:text-white">Budi Santoso</div>
                        <div class="text-sm text-text-secondary font-medium">Guru Wali Kelas</div>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-surface-dark p-8 rounded-2xl shadow-sm hover:shadow-md transition-shadow border border-orange-100 dark:border-gray-800 flex flex-col gap-6">
                <div class="text-primary flex gap-1">
                    <span class="material-symbols-outlined text-xl text-yellow-400 fill-current">star</span>
                    <span class="material-symbols-outlined text-xl text-yellow-400 fill-current">star</span>
                    <span class="material-symbols-outlined text-xl text-yellow-400 fill-current">star</span>
                    <span class="material-symbols-outlined text-xl text-yellow-400 fill-current">star</span>
                    <span class="material-symbols-outlined text-xl text-yellow-400 fill-current">star</span>
                </div>
                <blockquote class="text-text-main dark:text-white text-lg font-medium leading-relaxed flex-1 italic">
                    "This initiative is a blessing. The menu is varied and nutritious. My daughter has started eating vegetables she used to refuse at home! Thank you for caring for our kids."
                </blockquote>
                <div class="flex items-center gap-4 mt-auto">
                    <div class="h-12 w-12 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center font-bold text-xl border-2 border-white shadow-sm">
                        RW
                    </div>
                    <div>
                        <div class="font-bold text-text-main dark:text-white">Rina Wijaya</div>
                        <div class="text-sm text-text-secondary font-medium">Orang Tua Siswa</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section> --}}


@endsection
