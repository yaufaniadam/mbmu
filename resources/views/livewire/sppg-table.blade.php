<!--
File ini berisi komponen tabel dan modal yang ditulis ulang dengan Tailwind + Alpine.js.
Versi ini menggunakan entangle secara aman agar tidak error "unexpected token".
-->
<div 
    x-data="{ showModal: false, showDeleteModal: false }"
    x-init="
        showModal = $wire.entangle('showModal');
        showDeleteModal = $wire.entangle('showDeleteModal');
    "
>
    
    <!-- 1. Header: Tombol Search dan Tambah Data -->
    <div class="flex justify-between items-center mb-6">
        <!-- Kolom Pencarian -->
        <div class="w-1/3">
            <input wire:model.debounce.300ms="search" 
                   type="text" 
                   class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" 
                   placeholder="Cari berdasarkan nama SPPG...">
        </div>
        
        <!-- Tombol Tambah Data -->
        <button wire:click="create()" 
                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Tambah SPPG
        </button>
    </div>

    <!-- 2. Tabel Data -->
    <div class="flex flex-col">
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <div class="flex items-center cursor-pointer" wire:click="sortBy('nama_sppg')">
                                        Nama SPPG
                                        @if ($sortField === 'nama_sppg')
                                            @if ($sortDirection === 'asc')
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" /></svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                            @endif
                                        @endif
                                    </div>
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Kepala SPPG
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Lembaga Pengusul
                                </th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">Aksi</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($sppgs as $sppg)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $sppg->nama_sppg }}</div>
                                        <div class="text-sm text-gray-500">{{ $sppg->kode_sppg }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $sppg->kepalaSppg->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $sppg->lembagaPengusul->nama_lembaga ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                        <button wire:click="edit({{ $sppg->id }})" class="text-blue-600 hover:text-blue-900">Edit</button>
                                        <button wire:click="delete({{ $sppg->id }})" class="text-red-600 hover:text-red-900">Hapus</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                        Tidak ada data untuk ditampilkan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Pagination -->
    <div class="mt-6">
        {{ $sppgs->links() }}
    </div>

    <!-- 4. Modal Tambah/Edit -->
    <div x-show="showModal" 
         x-transition.opacity
         class="fixed z-10 inset-0 overflow-y-auto" 
         style="display: none;"
         aria-labelledby="modal-title" role="dialog" aria-modal="true">
         
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showModal = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form wire:submit.prevent="save">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            {{ $sppgId ? 'Edit SPPG' : 'Tambah SPPG Baru' }}
                        </h3>
                        <div class="mt-4 space-y-4">
                            <!-- Nama -->
                            <div>
                                <label for="nama_sppg" class="block text-sm font-medium text-gray-700">Nama SPPG</label>
                                <input wire:model.defer="sppg.nama_sppg" type="text" id="nama_sppg" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                @error('sppg.nama_sppg') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <!-- Kode -->
                            <div>
                                <label for="kode_sppg" class="block text-sm font-medium text-gray-700">Kode SPPG</label>
                                <input wire:model.defer="sppg.kode_sppg" type="text" id="kode_sppg" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                @error('sppg.kode_sppg') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <!-- Alamat -->
                            <div>
                                <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat</label>
                                <textarea wire:model.defer="sppg.alamat" id="alamat" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                                @error('sppg.alamat') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <!-- Kepala -->
                            <div>
                                <label for="kepala_sppg_id" class="block text-sm font-medium text-gray-700">Kepala SPPG</label>
                                <select wire:model.defer="sppg.kepala_sppg_id" id="kepala_sppg_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    <option value="">Pilih Kepala SPPG</option>
                                    @foreach($allUsers as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                                @error('sppg.kepala_sppg_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <!-- Lembaga -->
                            <div>
                                <label for="lembaga_pengusul_id" class="block text-sm font-medium text-gray-700">Lembaga Pengusul</label>
                                <select wire:model.defer="sppg.lembaga_pengusul_id" id="lembaga_pengusul_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    <option value="">Pilih Lembaga Pengusul</option>
                                    @foreach($allLembaga as $lembaga)
                                        <option value="{{ $lembaga->id }}">{{ $lembaga->nama_lembaga }}</option>
                                    @endforeach
                                </select>
                                @error('sppg.lembaga_pengusul_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full sm:w-auto inline-flex justify-center rounded-md bg-blue-600 text-white px-4 py-2 text-sm font-medium hover:bg-blue-700">
                            <span wire:loading.remove wire:target="save">Simpan</span>
                            <span wire:loading wire:target="save">Menyimpan...</span>
                        </button>
                        <button type="button" @click="showModal = false" class="mt-3 sm:mt-0 sm:ml-3 w-full sm:w-auto inline-flex justify-center rounded-md border border-gray-300 bg-white text-gray-700 px-4 py-2 text-sm font-medium hover:bg-gray-50">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 5. Modal Hapus -->
    <div x-show="showDeleteModal"
         x-transition.opacity
         class="fixed z-10 inset-0 overflow-y-auto"
         style="display: none;">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showDeleteModal = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5.93 19h12.14a1.5 1.5 0 001.299-2.228L13.732 4a1.5 1.5 0 00-2.598 0L4.63 16.772A1.5 1.5 0 005.93 19z" />
                            </svg>
                        </div>
                        <div class="mt-3 sm:mt-0 sm:ml-4 text-left">
                            <h3 class="text-lg font-medium text-gray-900">Hapus Data SPPG</h3>
                            <p class="mt-2 text-sm text-gray-500">Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.</p>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button wire:click="confirmDelete()" type="button" class="w-full sm:w-auto inline-flex justify-center rounded-md bg-red-600 text-white px-4 py-2 text-sm font-medium hover:bg-red-700">
                        <span wire:loading.remove wire:target="confirmDelete">Hapus</span>
                        <span wire:loading wire:target="confirmDelete">Menghapus...</span>
                    </button>
                    <button type="button" @click="showDeleteModal = false" class="mt-3 sm:mt-0 sm:ml-3 w-full sm:w-auto inline-flex justify-center rounded-md border border-gray-300 bg-white text-gray-700 px-4 py-2 text-sm font-medium hover:bg-gray-50">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
