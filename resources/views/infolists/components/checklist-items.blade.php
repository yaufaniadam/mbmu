@php
    $items = $getState();
@endphp

@if (is_array($items) && !empty($items))
    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 p-4">
        <div class="space-y-3">
            @foreach ($items as $item)
                @php
                    $status = $item['status'] ?? ($item['checked'] === 'true' ? 'Sesuai' : 'Tidak Sesuai');
                    $color = match ($status) {
                        'Sesuai' => 'success',
                        'Tidak Sesuai' => 'danger',
                        'Perlu Perbaikan' => 'warning',
                        default => 'gray',
                    };
                @endphp
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; border-bottom: 1px solid rgba(156, 163, 175, 0.2); padding-bottom: 0.75rem; margin-bottom: 0.75rem;">
                    <span style="font-size: 0.875rem; font-weight: 500; color: inherit;">
                        {{ $item['item'] ?? ($item['item_name'] ?? 'Item') }}
                    </span>
                    <div style="flex-shrink: 0;">
                        <x-filament::badge :color="$color" size="sm">
                            {{ $status }}
                        </x-filament::badge>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@else
    <div class="p-4 text-center rounded-lg border border-dashed border-gray-300 dark:border-gray-600">
        <span class="text-sm text-gray-500 dark:text-gray-400italic">
            Belum ada data evaluasi.
        </span>
    </div>
@endif
