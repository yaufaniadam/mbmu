@extends('layouts.public')

@section('content')
<!-- Header (Nav) is in layout -->

<div class="flex-grow w-full max-w-[1280px] mx-auto px-6 py-8">
    <!-- Breadcrumbs -->
    <nav class="flex items-center gap-2 mb-6 text-sm">
        <a class="text-[#686868] dark:text-gray-400 hover:text-primary dark:hover:text-primary transition-colors" href="{{ url('/') }}">Home</a>
        <span class="text-[#686868] dark:text-gray-400">/</span>
        <a class="text-[#686868] dark:text-gray-400 hover:text-primary dark:hover:text-primary transition-colors" href="{{ route('blog.public.index') }}">Blog</a>
        <span class="text-[#686868] dark:text-gray-400">/</span>
        <span class="font-medium text-[#181511] dark:text-gray-200 truncate max-w-[200px]">{{ $post->title }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
        <!-- Main Content Area -->
        <article class="lg:col-span-8 flex flex-col gap-8">
            <!-- Header Section -->
            <header class="flex flex-col gap-6">
                <div class="flex flex-col gap-3">
                    <span class="inline-block px-3 py-1 rounded-full bg-primary/10 text-primary text-xs font-bold w-fit uppercase tracking-wider">Article</span>
                    <h1 class="text-[#181511] dark:text-white text-3xl md:text-5xl font-black leading-tight tracking-[-0.02em]">
                        {{ $post->title }}
                    </h1>
                    @if($post->excerpt)
                    <p class="text-lg md:text-xl text-[#686868] dark:text-gray-400 leading-relaxed max-w-3xl">
                        {{ $post->excerpt }}
                    </p>
                    @endif
                </div>
                <!-- Author Meta -->
                <div class="flex items-center gap-4 py-4 border-y border-[#f5f3f0] dark:border-[#3a3530]">
                    <div class="bg-center bg-no-repeat bg-cover rounded-full h-12 w-12 ring-2 ring-primary/20" 
                         style="background-image: url('{{ $post->author && $post->author->photo_path ? Storage::url($post->author->photo_path) : 'https://ui-avatars.com/api/?name='.urlencode($post->author->name ?? 'Admin') }}');">
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[#181511] dark:text-white text-sm font-bold">{{ $post->author->name ?? 'Admin' }}</span>
                        <div class="flex items-center gap-2 text-xs text-[#686868] dark:text-gray-400">
                            <span>Author</span>
                            <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                            <span>{{ $post->published_at->format('M d, Y') }}</span>
                            <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                            <span>{{ ceil(str_word_count(strip_tags($post->content)) / 200) }} min read</span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Hero Image -->
            @if($post->featured_image)
            <div class="w-full h-auto aspect-video rounded-xl overflow-hidden shadow-sm">
                <div class="w-full h-full bg-center bg-no-repeat bg-cover transform hover:scale-105 transition-transform duration-700" 
                     style="background-image: url('{{ Storage::url($post->featured_image) }}');">
                </div>
            </div>
            @endif

            <!-- Article Body + Sticky Share -->
            <div class="relative flex gap-8">
                <!-- Sticky Share Sidebar (Desktop) -->
                <aside class="hidden xl:flex flex-col gap-4 sticky top-24 h-fit w-12 items-center">
                    <span class="text-xs font-bold text-gray-400 uppercase rotate-180" style="writing-mode: vertical-rl;">Share</span>
                    <a class="w-10 h-10 rounded-full bg-white dark:bg-[#2c2620] border border-gray-100 dark:border-gray-700 flex items-center justify-center text-gray-500 hover:text-[#1877F2] hover:border-[#1877F2] transition-colors shadow-sm" href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" target="_blank" title="Bagikan ke Facebook">
                        <svg class="w-4 h-4" fill="currentColor" viewbox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.791-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"></path></svg>
                    </a>
                    <a class="w-10 h-10 rounded-full bg-white dark:bg-[#2c2620] border border-gray-100 dark:border-gray-700 flex items-center justify-center text-gray-500 hover:text-[#1DA1F2] hover:border-[#1DA1F2] transition-colors shadow-sm" href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($post->title) }}" target="_blank" title="Bagikan ke Twitter">
                        <svg class="w-4 h-4" fill="currentColor" viewbox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"></path></svg>
                    </a>
                    <a class="w-10 h-10 rounded-full bg-white dark:bg-[#2c2620] border border-gray-100 dark:border-gray-700 flex items-center justify-center text-gray-500 hover:text-[#25D366] hover:border-[#25D366] transition-colors shadow-sm" href="https://api.whatsapp.com/send?text={{ urlencode($post->title . ' - ' . request()->url()) }}" target="_blank" title="Bagikan ke WhatsApp">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    </a>
                </aside>
                
                <!-- Text Content -->
                <div class="flex flex-col gap-6 text-[#181511] dark:text-gray-200 text-lg leading-8 flex-1 prose prose-lg prose-orange dark:prose-invert max-w-none">
                    {!! $post->content !!}
                </div>
            </div>
        </article>

        <!-- Sidebar -->
        <aside class="lg:col-span-4 space-y-8">

            <!-- Latest/Related Posts -->
            <div class="bg-white dark:bg-[#1a1612] border border-[#f5f3f0] dark:border-[#3a3530] rounded-xl p-6">
                <h3 class="text-lg font-bold mb-4 text-[#181511] dark:text-white">Related Posts</h3>
                <div class="flex flex-col gap-4">
                    @forelse($relatedPosts as $related)
                    <a class="flex gap-4 group" href="{{ route('blog.public.show', $related->slug) }}">
                        <div class="flex-shrink-0 w-16 h-16 bg-gray-200 rounded-lg bg-cover bg-center" 
                             style="background-image: url('{{ $related->featured_image ? Storage::url($related->featured_image) : 'https://lh3.googleusercontent.com/aida-public/AB6AXuCXnhm_2cGZjpiAxZddG8JNXZCELjgy8tS7d9l5Zo2T4WsVXuxaY8CizH1lG1F-Ec914WKodU-dCjkOTz8k6Bb_31xVJsrZxqJr7DExxUAHQ5FKGua5CNVamW4VnNvbjE0QEv7fp6ehzcbm7lUS_-2xcntWkcF34j8n1Q91E7eynMbv3ULL6LXEeE7NT0VSPvOUTcXt_vl0PDgVFTKnKcZqxCmEgKr2fIBR9LobCMv7KvwxZtaXaW_qBjN7gxjyeoLJt3rvN0HgYqB-' }}');">
                        </div>
                        <div>
                            <h4 class="font-bold text-sm text-[#181511] dark:text-white group-hover:text-primary transition-colors line-clamp-2">{{ $related->title }}</h4>
                            <p class="text-xs text-gray-500 mt-1">{{ $related->published_at->format('M d') }}</p>
                        </div>
                    </a>
                    @empty
                    <p class="text-sm text-gray-500">No related posts found.</p>
                    @endforelse
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection
