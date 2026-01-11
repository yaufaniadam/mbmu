@extends('layouts.public')

@section('content')
<article class="py-16 bg-white min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-12">
            <!-- Main Content -->
            <div class="w-full lg:w-2/3">
                <!-- Header -->
                <header class="mb-8">
                    <div class="mb-4">
                        <a href="{{ url('/') }}" class="text-sm font-medium text-blue-600 hover:underline">
                            &larr; Kembali ke Beranda
                        </a>
                    </div>
                    
                    <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 leading-tight mb-4">
                        {{ $post->title }}
                    </h1>
                    
                    <div class="flex items-center text-gray-500 text-sm">
                        <span>{{ $post->published_at->format('d M Y') }}</span>
                        <span class="mx-2">&bull;</span>
                        <span>Oleh {{ $post->author->name ?? 'Admin' }}</span>
                    </div>
                </header>

                <!-- Featured Image -->
                @if($post->featured_image)
                    <figure class="mb-10">
                        <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-auto rounded-xl shadow-lg object-cover max-h-[500px]">
                    </figure>
                @else
                    <div class="mb-10 w-full h-80 bg-gray-100 rounded-xl flex items-center justify-center">
                        <svg class="w-20 h-20 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path></svg>
                    </div>
                @endif

                <!-- Content -->
                <div class="prose prose-lg prose-blue max-w-none">
                    {!! $post->content !!}
                </div>
            </div>

            <!-- Sidebar -->
            <aside class="w-full lg:w-1/3 space-y-8 mt-12 lg:mt-0">
                <div class="sticky top-24">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Artikel Lainnya</h3>
                    <div class="space-y-6">
                        @forelse($relatedPosts as $related)
                        <a href="{{ route('blog.public.show', $related->slug) }}" class="group flex items-center gap-4">
                            <div class="flex-shrink-0">
                                @if($related->featured_image)
                                    <img class="h-16 w-16 rounded-full object-cover shadow-sm group-hover:opacity-80 transition" src="{{ Storage::url($related->featured_image) }}" alt="{{ $related->title }}">
                                @else
                                    <div class="h-16 w-16 rounded-full bg-gray-200 flex items-center justify-center group-hover:bg-gray-300 transition">
                                        <svg class="h-8 w-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path></svg>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 group-hover:text-blue-600 line-clamp-2 transition mb-1">
                                    {{ $related->title }}
                                </h4>
                                <p class="text-xs text-gray-500">
                                    {{ $related->published_at->diffForHumans() }}
                                </p>
                            </div>
                        </a>
                        @empty
                            <p class="text-gray-500 text-sm">Belum ada artikel lain.</p>
                        @endforelse
                    </div>
                </div>
            </aside>
        </div>
    </div>
</article>
@endsection
