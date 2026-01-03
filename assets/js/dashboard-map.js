/**
 * DASHBOARD-MAP.JS
 * Handles map initialization, markers, and map-related interactions
 */

(function () {
    'use strict';

    // Wait for DOM to be ready
    document.addEventListener('DOMContentLoaded', function () {

        if (!window.APP_CONFIG) {
            console.error('APP_CONFIG not found');
            return;
        }

        const { parkingData, BASEURL } = window.APP_CONFIG;

        // Initialize Map
        const map = L.map('map', {
            zoomControl: true,
            scrollWheelZoom: true
        }).setView([-7.7956, 110.3695], 13);

        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            attribution: '© OpenStreetMap © CARTO',
            maxZoom: 19
        }).addTo(map);

        const markers = {};

        // Utility function
        function formatRupiah(amount) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
        }

        // Render markers with SPARK branding
        parkingData.forEach(spot => {
            if (spot.latitude && spot.longitude) {
                const priceMarker = L.divIcon({
                    className: 'spark-price-marker',
                    html: `<div class="marker-pill">${formatRupiah(spot.harga_per_jam)}</div>`,
                    iconSize: [90, 34],
                    iconAnchor: [45, 17]
                });

                const marker = L.marker([spot.latitude, spot.longitude], { icon: priceMarker })
                    .addTo(map);

                marker.on('click', function () {
                    if (window.openBookingModal) {
                        window.openBookingModal(spot.id_tempat);
                    }
                });

                markers[spot.id_tempat] = marker;
            }
        });

        // Fit bounds
        if (parkingData.length > 0) {
            const bounds = L.latLngBounds(
                parkingData
                    .filter(s => s.latitude && s.longitude)
                    .map(s => [s.latitude, s.longitude])
            );
            if (bounds.isValid()) {
                map.fitBounds(bounds, { padding: [50, 50] });
            }
        }

        // Expose to global scope
        window.parkingMap = map;
        window.parkingMarkers = markers;
        window.formatRupiah = formatRupiah;
    });

})();