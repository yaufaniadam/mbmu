<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div 
        wire:ignore 
        x-data="{
            state: $wire.entangle('{{ $getStatePath() }}'),
            map: null,
            marker: null,
            
            init() {
                console.log('LocationPicker init started');
                
                // Watch for external state changes (e.g. from geocoding)
                // Watch for external state changes (e.g. from geocoding)
                this.$watch('state', value => {
                    if (value && value.lat && value.lng && this.map && this.marker) {
                        const current = this.marker.getLatLng();
                        
                        // Only update if position is significantly different to avoid loops
                        if (Math.abs(current.lat - value.lat) > 0.0001 || Math.abs(current.lng - value.lng) > 0.0001) {
                            const newLatLng = [value.lat, value.lng];
                            this.marker.setLatLng(newLatLng);
                            this.map.flyTo(newLatLng, value.zoom || this.map.getZoom());
                        }
                    }
                });

                // Wait for Leaflet to be available
                let checkLeaflet = setInterval(() => {
                    if (typeof L !== 'undefined') {
                        clearInterval(checkLeaflet);
                        console.log('Leaflet found, setting up map');
                        this.setupMap();
                    } else {
                        console.log('Waiting for Leaflet...');
                    }
                }, 100);
            },
            
            setupMap() {
                if (this.$refs.mapContainer._leaflet_id) return;
                
                console.log('Setting up map container');
                const defaultLat = {{ $getDefaultLatitude() ?? -7.797068 }};
                const defaultLng = {{ $getDefaultLongitude() ?? 110.370529 }};
                const zoom = {{ $getZoom() ?? 13 }};
                
                // Get initial coordinates from state or use defaults
                let initLat = parseFloat(this.state?.lat ?? defaultLat);
                let initLng = parseFloat(this.state?.lng ?? defaultLng);
                
                console.log('Map config:', { initLat, initLng, zoom });

                this.map = L.map(this.$refs.mapContainer).setView([initLat, initLng], zoom);
                
                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap'
                }).addTo(this.map);
                
                this.marker = L.marker([initLat, initLng], { draggable: true }).addTo(this.map);
                
                // Update state when marker is dragged
                this.marker.on('dragend', () => {
                    const pos = this.marker.getLatLng();
                    this.updateState(pos.lat, pos.lng);
                });
                
                // Update marker when map is clicked
                this.map.on('click', (e) => {
                    this.marker.setLatLng(e.latlng);
                    this.updateState(e.latlng.lat, e.latlng.lng);
                });
                
                // Force map resize to ensure tiles render
                setTimeout(() => {
                    this.map.invalidateSize();
                }, 500);
            },
            
            updateState(lat, lng) {
                this.state = { lat: lat, lng: lng };
                
                
                const statePath = '{{ $getStatePath() }}';
                const parentPath = statePath.substring(0, statePath.lastIndexOf('.'));
                
                const latField = '{{ $getLatitudeField() }}';
                const lngField = '{{ $getLongitudeField() }}';
                
                if (latField) {
                    $wire.set(parentPath + '.' + latField, lat);
                }
                if (lngField) {
                    $wire.set(parentPath + '.' + lngField, lng);
                }
            }
        }"
        class="w-full"
    >
        <div 
            x-ref="mapContainer" 
            style="height: {{ $getHeight() ?? '300px' }}; width: 100%; z-index: 1;" 
            class="rounded-lg border border-gray-300 shadow-sm"
        ></div>
    </div>
</x-dynamic-component>
