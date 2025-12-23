<x-filament-panels::page>
    <div 
        x-data="{
            showModal: false,
            selectedDate: '',
            holidayName: '',
            calendar: null,
            
            openModal(date) {
                this.selectedDate = date;
                this.holidayName = '';
                this.showModal = true;
                this.$nextTick(() => this.$refs.holidayInput?.focus());
            },
            
            closeModal() {
                this.showModal = false;
            },
            
            saveHoliday() {
                if (this.holidayName.trim()) {
                    $wire.addHoliday(this.selectedDate, this.holidayName);
                    this.closeModal();
                }
            },
            
            initCalendar() {
                if (typeof FullCalendar === 'undefined') {
                    setTimeout(() => this.initCalendar(), 100);
                    return;
                }
                
                const calendarEl = this.$refs.calendarEl;
                if (!calendarEl) return;
                
                const holidays = @js($this->getHolidays());
                const self = this;
                
                this.calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    locale: 'id',
                    selectable: true,
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth'
                    },
                    events: holidays,
                    dateClick: function(info) {
                        self.openModal(info.dateStr);
                    },
                    eventClick: function(info) {
                        if (confirm('Hapus hari libur \"' + info.event.title + '\"?')) {
                            $wire.deleteHoliday(parseInt(info.event.id));
                        }
                    },
                    height: 'auto',
                    buttonText: {
                        today: 'Hari Ini',
                        month: 'Bulan'
                    }
                });
                
                this.calendar.render();
            }
        }"
        x-init="initCalendar()"
        @holiday-saved.window="location.reload()"
    >
        {{-- Calendar Container --}}
        <div class="space-y-4">
            <div 
                x-ref="calendarEl"
                class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4"
            ></div>
            
            {{-- Legend --}}
            <div class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 bg-red-600 rounded"></span>
                    <span>Hari Libur Nasional</span>
                </div>
                <span>•</span>
                <span>Klik tanggal untuk menambah hari libur</span>
                <span>•</span>
                <span>Klik event untuk menghapus</span>
            </div>
        </div>

        {{-- Add Holiday Modal --}}
        <template x-teleport="body">
            <div 
                x-show="showModal" 
                x-cloak
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50"
                @keydown.escape.window="closeModal()"
            >
                <div 
                    x-show="showModal"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 w-full max-w-md mx-4"
                    @click.away="closeModal()"
                >
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        Tambah Hari Libur
                    </h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Tanggal
                            </label>
                            <input 
                                type="date" 
                                x-model="selectedDate"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm"
                            />
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Nama Hari Libur
                            </label>
                            <input 
                                type="text" 
                                x-ref="holidayInput"
                                x-model="holidayName"
                                placeholder="Contoh: Hari Kemerdekaan RI"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm"
                                @keydown.enter.prevent="saveHoliday()"
                            />
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end gap-3">
                        <button 
                            type="button"
                            @click="closeModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600"
                        >
                            Batal
                        </button>
                        <button 
                            type="button"
                            @click="saveHoliday()"
                            class="px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700"
                        >
                            Simpan
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>
</x-filament-panels::page>

@assets
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
@endassets
