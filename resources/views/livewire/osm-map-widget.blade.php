<x-filament-widgets::widget>
    {{-- We removed the <link> and <script> tags from here to prevent Livewire conflicts --}}

    <x-filament::section>
        <div class="mb-4">
            <h2 class="text-lg font-bold">Peta Persebaran SPPG</h2>
        </div>

        {{-- 
            1. wire:ignore: Tells Livewire "Hands off this DIV" 
            2. wire:key: Gives this element a permanent identity so Livewire doesn't replace it
        --}}
        <div wire:ignore wire:key="osm-map-container-{{ $this->getId() }}" x-data="{
            init() {
                    // STEP 1: FORCE LOAD CSS & JS
                    // This fixes the 'Sticky Marker' bug by ensuring styles always exist
                    this.loadResources().then(() => {
                        this.setupMap(this.$refs.map);
        
                        const initialData = @js($markers);
                        this.updateMarkers(initialData);
        
                        Livewire.on('update-map-markers', (event) => {
                            const markers = event.markers || event[0].markers;
                            this.updateMarkers(markers);
                        });
                    });
                },
        
                loadResources() {
                    return new Promise((resolve) => {
                        // 1. Inject CSS if missing
                        if (!document.getElementById('leaflet-css')) {
                            const link = document.createElement('link');
                            link.id = 'leaflet-css';
                            link.rel = 'stylesheet';
                            link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
                            document.head.appendChild(link);
                        }
        
                        // 2. Load JS if missing
                        if (typeof L === 'undefined') {
                            const script = document.createElement('script');
                            script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
                            script.onload = resolve;
                            document.head.appendChild(script);
                        } else {
                            resolve(); // JS already loaded
                        }
                    });
                },
        
                setupMap(el) {
                    // Prevent Double Initialization
                    if (el._leaflet_id && this._map) return;
        
                    // Create Map
                    this._map = L.map(el).setView([-2.5489, 118.0149], 5);
        
                    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; OpenStreetMap'
                    }).addTo(this._map);
        
                    this._layerGroup = L.layerGroup().addTo(this._map);
                },
        
                updateMarkers(data) {
                    if (!this._map || !this._layerGroup) return;
        
                    this._layerGroup.clearLayers();
                    let bounds = [];
        
                    data.forEach(item => {
                        const lat = parseFloat(item.lat);
                        const lng = parseFloat(item.lng);
                        if (isNaN(lat) || isNaN(lng)) return;
        
                        const marker = L.marker([lat, lng]);
        
                        marker.bindPopup(`
                            <div class='min-w-[150px]'>
                                <strong>${item.title}</strong><br>
                                <span>Alamat : <span><br/>
                                <span class='text-xs text-gray-500'>${item.info['alamat']}</span>
                                <br/>
                                <br/>
                                <span>Kepala SPPG : <span><br/>
                                <span class='text-xs text-gray-500'>${item.info['kepala_sppg']}</span>
                            </div>
                        `);
        
                        this._layerGroup.addLayer(marker);
                        bounds.push([lat, lng]);
                    });
        
                    // Auto-Zoom
                    if (bounds.length > 0) {
                        requestAnimationFrame(() => {
                            if (this._map) {
                                this._map.invalidateSize();
                                try {
                                    this._map.fitBounds(bounds, { padding: [50, 50], maxZoom: 15 });
                                } catch (e) {}
                            }
                        });
                    } else {
                        this._map.setView([-2.5489, 118.0149], 5);
                    }
                }
        }">
            <div x-ref="map" style="width: 100%; height: 400px; z-index: 1;" class="rounded-lg bg-gray-100"></div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
