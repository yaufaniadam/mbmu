@extends('layouts.public')

@section('content')
<section class="w-full relative group" x-data="{
    activeSlide: 0,
    slides: [0, 1],
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
        this.activeSlide = (this.activeSlide - 1 + this.slides.length) % this.slides.length;
        this.scrollTo(this.activeSlide);
    },
    next() {
        this.activeSlide = (this.activeSlide + 1) % this.slides.length;
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
        <div class="min-w-full snap-center relative h-full bg-gray-200">
            <div class="absolute inset-0 bg-cover bg-center" data-alt="Happy elementary school children eating healthy lunch in cafeteria" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuAqNcGi6nstHfmr4509VCCm_wzMhCO-0LvbTCuEazZgIaUjjRNI2Q7alBUWqYzqxhFPjdtW7gsZShvVNGWbLbYN7wVzYax1iho8B9eZlD2k1SjwNkeFnjJordfvpD10PHhP522hEuuOmqwSOj0niU0-h0DKEwSGOFJZ0cifZwAztXx-jWsuWAFUOBralUF8quKLgLJkC1stS6VA3mqGnuwyyeqOz87rsfFG0Rc7Qe8qKv8B6cwQUJW9IacCctxD_ddvtLaQ7XlOQYEU');">
            </div>
            <div class="absolute inset-0 bg-gradient-to-r from-black/60 to-black/10 flex items-center">
                <div class="px-4 md:px-40 w-full max-w-[1280px] mx-auto">
                    <div class="max-w-[720px] flex flex-col gap-6">
                        <span class="inline-block px-3 py-1 bg-secondary-green text-white text-xs font-bold uppercase tracking-wider rounded w-fit">Now Serving</span>
                        <h1 class="text-white text-4xl md:text-6xl font-black leading-tight tracking-tight">
                            Fueling Future Leaders,<br/><span class="text-primary">One Meal at a Time.</span>
                        </h1>
                        <p class="text-white/90 text-lg md:text-xl font-normal leading-relaxed max-w-[600px]">
                            We ensure every child has access to fresh, nutritious food every school day, empowering them to learn and grow.
                        </p>
                        <div class="flex gap-4 pt-4">
                            <button class="h-12 px-8 bg-primary hover:bg-primary-dark text-[#181511] text-base font-bold rounded-lg transition-colors shadow-lg">
                                Our Programs
                            </button>
                            <button class="h-12 px-8 bg-white/10 hover:bg-white/20 backdrop-blur-sm text-white border-2 border-white/30 text-base font-bold rounded-lg transition-colors">
                                Learn More
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="min-w-full snap-center relative h-full bg-gray-200">
            <div class="absolute inset-0 bg-cover bg-center" data-alt="Teacher smiling with diverse group of students holding fresh fruits" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuD8YgTWyvvAXOJJrbItQ7Y-JFHnBm77-1grTF0alGxBHorve83kopMYFHOLT1RH-9QZ5lcRQo6hALroH6AEmjLFtgKuYfqZIs09jntItn0tHryo3efVYtso0wcWc4F2ZwgcOR3vp3dkTiCUT2Ari0EgHQcRF1TaVYBpD8NqC_iefpJ4IpkQq9z-k5vJ1qO9eOSXGOsQsb7yJhXGLvosnn3Wa8l_y0pmaez5UO_NWMylgD-h9HFIsAJIqkm67S4U1DngaznvXy418sly');">
            </div>
            <div class="absolute inset-0 bg-gradient-to-t md:bg-gradient-to-r from-black/70 to-transparent flex items-center">
                <div class="px-4 md:px-40 w-full max-w-[1280px] mx-auto">
                    <div class="max-w-[720px] flex flex-col gap-6">
                        <span class="inline-block px-3 py-1 bg-secondary-yellow text-[#181511] text-xs font-bold uppercase tracking-wider rounded w-fit">Community Impact</span>
                        <h1 class="text-white text-4xl md:text-6xl font-black leading-tight tracking-tight">
                            Healthy Habits Start<br/>In The Classroom
                        </h1>
                        <p class="text-white/90 text-lg md:text-xl font-normal leading-relaxed max-w-[600px]">
                            Education and nutrition go hand in hand. See how we are integrating food literacy into daily learning.
                        </p>
                        <div class="flex gap-4 pt-4">
                            <button class="h-12 px-8 bg-primary hover:bg-primary-dark text-[#181511] text-base font-bold rounded-lg transition-colors shadow-lg">
                                Join the Cause
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="absolute bottom-6 left-0 right-0 flex justify-center gap-2">
        <template x-for="(slide, index) in slides" :key="index">
            <button @click="scrollTo(index)"
                class="w-3 h-3 rounded-full transition-colors shadow-sm focus:outline-none"
                :class="activeSlide === index ? 'bg-primary' : 'bg-white/50 hover:bg-white'">
            </button>
        </template>
    </div>
</section>

<section class="py-16 px-4 md:px-10 bg-surface-light dark:bg-surface-dark w-full">
    <div class="max-w-[960px] mx-auto flex flex-col gap-12">
        <div class="text-center flex flex-col gap-4 items-center">
            <h2 class="text-3xl md:text-4xl font-bold text-text-main dark:text-white leading-tight">Our Core Values</h2>
            <p class="text-text-secondary text-lg max-w-2xl">
                We are committed to rigorous standards and community support to provide the best for our children.
            </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="flex flex-col items-center text-center gap-4 rounded-xl border border-[#e6e1db] dark:border-gray-800 bg-white dark:bg-surface-dark p-8 shadow-sm hover:shadow-md transition-shadow">
                <div class="size-16 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 flex items-center justify-center mb-2">
                    <span class="material-symbols-outlined text-3xl">nutrition</span>
                </div>
                <h3 class="text-xl font-bold text-text-main dark:text-white">Balanced Nutrition</h3>
                <p class="text-text-secondary dark:text-gray-400">Menus crafted by pediatric nutritionists to ensure optimal growth and energy.</p>
            </div>
            <div class="flex flex-col items-center text-center gap-4 rounded-xl border border-[#e6e1db] dark:border-gray-800 bg-white dark:bg-surface-dark p-8 shadow-sm hover:shadow-md transition-shadow">
                <div class="size-16 rounded-full bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 flex items-center justify-center mb-2">
                    <span class="material-symbols-outlined text-3xl">agriculture</span>
                </div>
                <h3 class="text-xl font-bold text-text-main dark:text-white">Local Sourcing</h3>
                <p class="text-text-secondary dark:text-gray-400">Fresh ingredients sourced directly from farmers within the local community.</p>
            </div>
            <div class="flex flex-col items-center text-center gap-4 rounded-xl border border-[#e6e1db] dark:border-gray-800 bg-white dark:bg-surface-dark p-8 shadow-sm hover:shadow-md transition-shadow">
                <div class="size-16 rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400 flex items-center justify-center mb-2">
                    <span class="material-symbols-outlined text-3xl">local_shipping</span>
                </div>
                <h3 class="text-xl font-bold text-text-main dark:text-white">Daily Delivery</h3>
                <p class="text-text-secondary dark:text-gray-400">Hot, prepared meals arriving at schools before the first lunch bell rings.</p>
            </div>
        </div>
    </div>
</section>

<section class="py-16 px-4 md:px-10 bg-[#fbfbf9] dark:bg-background-dark w-full">
    <div class="max-w-[1280px] mx-auto flex flex-col gap-10">
        <div class="flex flex-col md:flex-row justify-between items-end gap-4 border-b border-[#e6e1db] dark:border-gray-800 pb-6">
            <div>
                <h2 class="text-[#181511] dark:text-white text-3xl font-bold leading-tight tracking-[-0.015em] mb-2">Latest News &amp; Nutrition Tips</h2>
                <p class="text-[#8a7960] text-base">Stay updated with our latest initiatives and health advice.</p>
            </div>
            <a class="text-primary font-bold hover:underline flex items-center gap-1" href="{{ url('/artikel') }}">
                View All Posts <span class="material-symbols-outlined text-sm">arrow_forward</span>
            </a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @forelse($posts as $post)
            <article class="flex flex-col rounded-lg overflow-hidden bg-white dark:bg-surface-dark shadow-[0_2px_8px_rgba(0,0,0,0.08)] hover:-translate-y-1 transition-transform duration-300">
                <div class="w-full aspect-video bg-cover bg-center" data-alt="{{ $post->title }}" style="background-image: url('{{ $post->featured_image ? Storage::url($post->featured_image) : 'https://placehold.co/600x400?text=No+Image' }}');">
                </div>
                <div class="p-5 flex flex-col flex-1 gap-3">
                    <span class="text-xs font-bold text-secondary-green uppercase tracking-wide">News</span>
                    <h3 class="text-lg font-bold text-text-main dark:text-white leading-snug">{{ $post->title }}</h3>
                    <p class="text-sm text-text-secondary line-clamp-3">{{ $post->excerpt }}</p>
                    <div class="mt-auto pt-2">
                        <a class="text-sm font-bold text-primary hover:text-primary-dark" href="{{ route('blog.public.show', $post->slug) }}">Read Article</a>
                    </div>
                </div>
            </article>
        @empty
            <div class="col-span-1 sm:col-span-2 lg:col-span-4 text-center py-10">
                <p class="text-text-secondary">No updates available at the moment.</p>
            </div>
        @endforelse
        </div>
    </div>
</section>

<section class="py-20 px-4 md:px-10 bg-orange-50 dark:bg-background-dark w-full border-t border-orange-100 dark:border-gray-800">
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
</section>


@endsection
