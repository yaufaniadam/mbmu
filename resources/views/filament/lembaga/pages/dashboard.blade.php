<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Stats Overview Widget --}}
        <x-filament-widgets::widgets
            :widgets="$this->getWidgets()"
            :columns="$this->getColumns()"
        />

        {{-- Welcome Section --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                Selamat Datang, {{ auth()->user()->name }}
            </h2>
            <p class="text-gray-600 dark:text-gray-400">
                @if($this->getData()['lembaga'])
                    Anda login sebagai Pimpinan <strong>{{ $this->getData()['lembaga']->nama_lembaga }}</strong>
                @else
                    Akun Anda belum terhubung dengan Lembaga Pengusul.
                @endif
            </p>
        </div>

        {{-- Recent Activity --}}
        @if($this->getData()['recent_complaints']->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                Pengaduan Terbaru
            </h3>
            <div class="space-y-3">
                @foreach($this->getData()['recent_complaints'] as $complaint)
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                {{ $complaint->subject }}
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                @if($complaint->status === 'Open') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                @elseif($complaint->status === 'Responded') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                @else bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                @endif">
                                {{ $complaint->status }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            {{ Str::limit($complaint->content, 100) }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                            {{ $complaint->created_at->diffForHumans() }}
                        </p>
                    </div>
                    <a href="{{ route('filament.lembaga.resources.complaints.view', $complaint) }}" 
                       class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300">
                        <x-filament::icon icon="heroicon-o-arrow-right" class="w-5 h-5" />
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</x-filament-panels::page>
