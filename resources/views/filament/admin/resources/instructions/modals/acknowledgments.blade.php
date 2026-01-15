<div class="space-y-4">
    @php
        $targetedUsers = $instruction->getTargetedUsers();
        $acknowledgments = $instruction->acknowledgments;
        $acknowledgedUserIds = $acknowledgments->pluck('user_id')->toArray();
    @endphp

    <div class="grid grid-cols-2 gap-4 mb-4">
        <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
            <div class="text-sm text-gray-600 dark:text-gray-400">Sudah Membaca</div>
            <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                {{ $acknowledgments->count() }} / {{ $targetedUsers->count() }}
            </div>
        </div>
        <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
            <div class="text-sm text-gray-600 dark:text-gray-400">Persentase</div>
            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                {{ $instruction->getAcknowledgmentRate() }}%
            </div>
        </div>
    </div>

    <div class="border dark:border-gray-700 rounded-lg divide-y dark:divide-gray-700 max-h-96 overflow-y-auto">
        @forelse($targetedUsers as $user)
            <div class="p-3 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-800">
                <div class="flex items-center gap-3">
                    @if(in_array($user->id, $acknowledgedUserIds))
                        <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                    @else
                        <div class="w-2 h-2 bg-gray-300 dark:bg-gray-600 rounded-full"></div>
                    @endif
                    <div>
                        <div class="font-medium text-gray-900 dark:text-gray-100">{{ $user->name }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</div>
                    </div>
                </div>
                <div class="text-right">
                    @php
                        $ack = $acknowledgments->firstWhere('user_id', $user->id);
                    @endphp
                    @if($ack)
                        <div class="text-xs text-green-600 dark:text-green-400 font-medium">✓ Sudah Dibaca</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $ack->acknowledged_at->format('d M Y H:i') }}
                        </div>
                    @else
                        <div class="text-xs text-gray-400 dark:text-gray-500">Belum Dibaca</div>
                    @endif
                </div>
            </div>
        @empty
            <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                Tidak ada penerima yang ditargetkan
            </div>
        @endforelse
    </div>
</div>
