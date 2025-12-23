<div class="flex items-center justify-center gap-2 p-2">
    @php
        $status = $record->dailyAttendances->firstWhere('attendance_date', $this->selected_date)?->status;
        $statuses = [
            'Hadir' => ['icon' => '✓', 'color' => 'success', 'bg' => 'bg-green-500', 'hover' => 'hover:bg-green-600'],
            'Izin' => ['icon' => '📝', 'color' => 'warning', 'bg' => 'bg-yellow-500', 'hover' => 'hover:bg-yellow-600'],
            'Sakit' => ['icon' => '🏥', 'color' => 'danger', 'bg' => 'bg-orange-500', 'hover' => 'hover:bg-orange-600'],
            'Alpha' => ['icon' => '✗', 'color' => 'danger', 'bg' => 'bg-red-500', 'hover' => 'hover:bg-red-600'],
        ];
    @endphp

    <div class="flex items-center justify-center gap-2 p-2 rounded-lg border dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
        @foreach($statuses as $key => $config)
            <button
                type="button"
                wire:click="updateStatus({{ $record->id }}, '{{ $key }}')"
                wire:loading.attr="disabled"
                @if($status === $key) disabled @endif
                class="flex items-center gap-2 px-3 py-1.5 rounded-md transition-all text-sm font-medium
                    {{ $status === $key
                        ? $config['bg'] . ' text-white shadow-sm cursor-default opacity-100'
                        : 'bg-white text-gray-700 hover:bg-gray-100 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600'
                    }}"
            >
                <span>{{ $config['icon'] }}</span>
                <span>{{ $key }}</span>
            </button>
        @endforeach
    </div>
</div>
