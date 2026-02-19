<div>
    <!-- Tabs -->
    <div class="flex bg-white shadow rounded-lg mb-6 sticky top-16 z-10">
        <button 
            wire:click="switchTab('delivery')"
            class="flex-1 py-4 text-center font-medium transition-colors relative {{ $activeTab === 'delivery' ? 'text-primary' : 'text-gray-500 hover:text-gray-700' }}"
        >
            <span class="material-icons block mb-1">local_dining</span>
            Pengantaran
            @if($activeTab === 'delivery')
                <div class="absolute bottom-0 left-0 w-full h-1 bg-primary rounded-t"></div>
            @endif
        </button>
        <button 
            wire:click="switchTab('pickup')"
            class="flex-1 py-4 text-center font-medium transition-colors relative {{ $activeTab === 'pickup' ? 'text-primary' : 'text-gray-500 hover:text-gray-700' }}"
        >
            <span class="material-icons block mb-1">dirty_lens</span>
            Penjemputan
            @if($activeTab === 'pickup')
                <div class="absolute bottom-0 left-0 w-full h-1 bg-primary rounded-t"></div>
            @endif
        </button>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded shadow-sm flex items-center gap-3">
            <span class="material-icons">check_circle</span>
            <p>{{ session('message') }}</p>
        </div>
    @endif

    <!-- Task List -->
    <div class="space-y-4">
        @forelse($tasks as $task)
            <div class="bg-white rounded-lg material-shadow-1 overflow-hidden transition hover:shadow-md">
                <div class="p-5">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <h3 class="font-bold text-lg text-gray-800">{{ $task->school->name ?? 'Sekolah Tidak Diketahui' }}</h3>
                            <p class="text-sm text-gray-500 flex items-center gap-1">
                                <span class="material-icons text-sm">schedule</span>
                                {{ $task->productionSchedule->tanggal->format('d M Y') ?? '-' }}
                            </p>
                        </div>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $activeTab === 'delivery' ? 'bg-blue-100 text-blue-800' : 'bg-orange-100 text-orange-800' }}">
                            {{ $activeTab === 'delivery' ? 'Kirim Makanan' : 'Jemput Alat' }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-4 my-4 bg-gray-50 p-3 rounded-md">
                        <div>
                            <span class="block text-xs text-gray-400 uppercase">Porsi Besar</span>
                            <span class="font-medium text-gray-800">{{ $task->jumlah_porsi_besar }}</span>
                        </div>
                        <div>
                            <span class="block text-xs text-gray-400 uppercase">Porsi Kecil</span>
                            <span class="font-medium text-gray-800">{{ $task->jumlah_porsi_kecil }}</span>
                        </div>
                    </div>

                    @if($confirmingId === $task->id)
                        <div class="mt-4 bg-gray-50 p-4 rounded-lg border border-gray-200 animate-fade-in">
                            <h4 class="text-sm font-medium mb-3 text-gray-700">Konfirmasi {{ $activeTab === 'delivery' ? 'Pengantaran' : 'Penjemputan' }}</h4>
                            
                            <div class="mb-3">
                                <label class="block text-xs font-medium text-gray-500 mb-1">Bukti Foto (Opsional)</label>
                                <input type="file" wire:model="photo" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                                @error('photo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="block text-xs font-medium text-gray-500 mb-1">Catatan (Opsional)</label>
                                <textarea wire:model="notes" rows="2" class="w-full text-sm border-gray-300 rounded focus:ring-primary focus:border-primary" placeholder="Ada kendala atau catatan khusus?"></textarea>
                                @error('notes') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div class="flex gap-2">
                                <button 
                                    wire:click="{{ $activeTab === 'delivery' ? 'completeDelivery' : 'completePickup' }}({{ $task->id }})"
                                    class="flex-1 bg-green-600 text-white py-2 rounded shadow hover:bg-green-700 text-sm font-medium flex justify-center items-center gap-2"
                                >
                                    <span wire:loading.remove wire:target="{{ $activeTab === 'delivery' ? 'completeDelivery' : 'completePickup' }}">SELESAI</span>
                                    <span wire:loading wire:target="{{ $activeTab === 'delivery' ? 'completeDelivery' : 'completePickup' }}">LOADING...</span>
                                </button>
                                <button wire:click="cancelAction" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 text-sm font-medium">BATAL</button>
                            </div>
                        </div>
                    @else
                        <button 
                            wire:click="confirmAction({{ $task->id }})" 
                            class="w-full mt-2 py-3 bg-white border border-primary text-primary hover:bg-primary hover:text-white transition-colors rounded font-medium text-sm material-btn flex items-center justify-center gap-2"
                        >
                            <span class="material-icons text-base">{{ $activeTab === 'delivery' ? 'check_circle' : 'assignment_turned_in' }}</span>
                            {{ $activeTab === 'delivery' ? 'SELESAI ANTAR' : 'SELESAI JEMPUT' }}
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-10 bg-white rounded-lg shadow-sm">
                <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                    <span class="material-icons text-gray-400 text-3xl">inbox</span>
                </div>
                <h3 class="text-gray-500 font-medium">Tidak ada tugas saat ini</h3>
                <p class="text-sm text-gray-400 mt-1">Istirahatlah sejenak!</p>
            </div>
        @endforelse
    </div>
</div>
