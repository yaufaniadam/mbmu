@extends('layouts.public')

@section('content')
<!-- Header (already in layout, but template has specific sub-header style, we'll align with layout) -->

<!-- Main Content -->
<div class="px-4 md:px-10 lg:px-40 flex flex-1 justify-center py-8">
    <div class="layout-content-container flex flex-col max-w-[1200px] flex-1 gap-8">
        <!-- Page Heading -->
        <div class="flex flex-col gap-3">
            <h1 class="text-[#181511] dark:text-white text-5xl font-black leading-tight tracking-[-0.033em]">Berita</h1>
            <p class="text-[#686868] dark:text-[#b0a695] text-xl font-normal leading-normal">Kegiatan dan informasi terbaru Kornas Makan Bergizi Muhammadiyah dan SPPG</p>
        </div>

        @if($posts->count() > 0)
            @php
                // Logic to separate featured post (first item) from others
                // If on page 1, show first as featured. If page > 1, maybe just grid?
                // For simplicity, always show first of the current page as featured or grid.
                // Design has a specific featured block. Let's use the first item for it.
                $featured = $posts->first();
                $gridPosts = $posts->slice(1);
            @endphp

            <!-- Featured Post -->
            <div class="w-full">
                <div class="flex flex-col items-stretch justify-start rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow bg-white dark:bg-[#152030] border border-[#e6e1db] dark:border-[#272c38] group">
                    <div class="grid grid-cols-1 lg:grid-cols-2">
                        <a href="{{ route('blog.public.show', $featured->slug) }}" class="w-full h-64 lg:h-auto bg-center bg-no-repeat bg-cover group-hover:scale-105 transition-transform duration-700 block" 
                             style="background-image: url('{{ $featured->featured_image ? Storage::url($featured->featured_image) : 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=800&q=80' }}');">
                        </a>
                        <div class="flex flex-col justify-center p-8 lg:p-12 gap-6 bg-white dark:bg-[#141e2b] relative z-10">
                            <div class="flex items-center gap-3">
                                <span class="bg-primary/20 text-primary dark:text-primary px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide">Featured</span>
                                <span class="text-[#686868] dark:text-[#b0a695] text-sm">{{ $featured->published_at ? $featured->published_at->format('M d, Y') : '-' }}</span>
                            </div>
                            <h2 class="text-[#181511] dark:text-white text-3xl lg:text-4xl font-bold leading-tight">
                                <a href="{{ route('blog.public.show', $featured->slug) }}" class="hover:text-primary transition-colors">{{ $featured->title }}</a>
                            </h2>
                            <p class="text-[#525252] dark:text-[#ccc3b8] text-lg leading-relaxed line-clamp-3">
                                {{ $featured->excerpt }}
                            </p>
                            <div class="pt-2">
                                <a class="inline-flex items-center gap-2 text-primary font-bold hover:underline decoration-2 underline-offset-4" href="{{ route('blog.public.show', $featured->slug) }}">
                                    Baca Selengkapnya
                                    <span class="material-symbols-outlined text-[1.2em]">arrow_forward</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Main Content Grid + Sidebar -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 mt-8">
            <!-- Blog Articles Grid (Left) -->
            <div class="lg:col-span-8 flex flex-col gap-10">
                @if(isset($gridPosts) && $gridPosts->count() > 0)
                    <!-- Article Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-10">
                        @foreach($gridPosts as $post)
                        <article class="flex flex-col gap-4 group cursor-pointer">
                            <a href="{{ route('blog.public.show', $post->slug) }}" class="w-full aspect-[4/3] rounded-lg overflow-hidden bg-gray-200">
                                <div class="w-full h-full bg-cover bg-center transition-transform duration-500 group-hover:scale-105" 
                                     style="background-image: url('{{ $post->featured_image ? Storage::url($post->featured_image) : 'https://images.unsplash.com/photo-1547592180-85f173990554?w=600&q=80' }}');">
                                </div>
                            </a>
                            <div class="flex flex-col gap-2">
                                <div class="flex items-center gap-3 text-xs font-bold uppercase tracking-wider text-primary">
                                    <span>Article</span>
                                </div>
                                <h3 class="text-xl font-bold text-[#181511] dark:text-white leading-tight group-hover:text-primary transition-colors">
                                    <a href="{{ route('blog.public.show', $post->slug) }}">{{ $post->title }}</a>
                                </h3>
                                <p class="text-[#595045] dark:text-[#9e968c] line-clamp-2">
                                    {{ $post->excerpt }}
                                </p>
                                <div class="flex items-center gap-2 mt-2 text-xs text-[#686868] dark:text-[#80766a]">
                                    <div class="size-6 rounded-full bg-gray-300 bg-cover" 
                                         style="background-image: url('{{ $post->author && $post->author->photo_path ? Storage::url($post->author->photo_path) : 'https://ui-avatars.com/api/?name='.urlencode($post->author->name ?? 'Admin') }}');">
                                    </div>
                                    <span class="font-medium">{{ $post->author->name ?? 'Unknown Author' }}</span> • <span>{{ $post->published_at ? $post->published_at->format('M d, Y') : '-' }}</span>
                                </div>
                            </div>
                        </article>
                        @endforeach
                    </div>
                @elseif(!isset($featured) || $posts->count() == 0)
                    <div class="text-center py-20">
                        <p class="text-xl text-gray-500">No articles found.</p>
                    </div>
                @endif

                <!-- Pagination -->
                <div class="mt-8 pt-8 border-t border-[#e6e1db] dark:border-[#272c38]">
                    {{ $posts->links() }}
                </div>
            </div>

            <!-- Sidebar (Right)-->
            <aside class="lg:col-span-4 flex flex-col gap-8">
                <!-- Search Widget -->
                <div class="bg-white dark:bg-[#141e2b] p-6 rounded-xl border border-[#e6e1db] dark:border-[#272c38] shadow-sm">
                    <h4 class="text-lg font-bold mb-4 text-[#181511] dark:text-white">Search</h4>
                    <form action="{{ route('blog.public.index') }}" method="GET">
                        <label class="flex flex-col w-full">
                            <div class="flex w-full items-stretch rounded-lg h-10 border border-[#e6e1db] dark:border-[#272c38] overflow-hidden focus-within:ring-2 focus-within:ring-primary/50">
                                <div class="text-[#181511] dark:text-white flex bg-[#f8f7f5] dark:bg-[#1a2737] items-center justify-center pl-3 pr-2">
                                    <span class="material-symbols-outlined text-[20px]">search</span>
                                </div>
                                <input name="search" value="{{ request('search') }}" class="flex w-full min-w-0 flex-1 resize-none bg-[#f8f7f5] dark:bg-[#1a2737] text-[#181511] dark:text-white focus:outline-0 border-none h-full placeholder:text-[#686868] px-2 text-sm font-normal leading-normal" placeholder="Search articles..."/>
                            </div>
                        </label>
                    </form>
                </div>
                
                
                {{-- Categories Widget (Hidden for now) --}}
                {{-- <div class="bg-white dark:bg-[#1a150d] p-6 rounded-xl border border-[#e6e1db] dark:border-[#3a3025] shadow-sm">
                    <h4 class="text-lg font-bold mb-4 text-[#181511] dark:text-white">Categories</h4>
                    <div class="flex flex-col gap-2">
                        <a class="flex items-center justify-between p-2 rounded hover:bg-[#f8f7f5] dark:hover:bg-[#2a241d] group transition-colors" href="#">
                            <span class="text-[#595045] dark:text-[#ccc3b8] group-hover:text-primary font-medium">Health Tips</span>
                        </a>
                        <a class="flex items-center justify-between p-2 rounded hover:bg-[#f8f7f5] dark:hover:bg-[#2a241d] group transition-colors" href="#">
                            <span class="text-[#595045] dark:text-[#ccc3b8] group-hover:text-primary font-medium">Program News</span>
                        </a>
                        <a class="flex items-center justify-between p-2 rounded hover:bg-[#f8f7f5] dark:hover:bg-[#2a241d] group transition-colors" href="#">
                            <span class="text-[#595045] dark:text-[#ccc3b8] group-hover:text-primary font-medium">Volunteer Stories</span>
                        </a>
                    </div>
                </div> --}}

            </aside>
        </div>
    </div>
</div>
@endsection
