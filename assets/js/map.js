document.addEventListener('DOMContentLoaded', () => {

    const mapEl = document.getElementById('map');
    if (!mapEl || typeof parkingData === 'undefined') return;

    /* ================= INIT MAP ================= */
    const map = L.map('map', {
        zoomControl: true,
        scrollWheelZoom: true
    }).setView([-7.7965, 110.3690], 14);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // FIX resize pada layout flex
    setTimeout(() => map.invalidateSize(), 200);

    /* ================= PRICE MARKER ================= */
    const priceIcon = (price) => L.divIcon({
        className: 'price-marker',
        html: `
            <div class="price-bubble">
                Rp ${new Intl.NumberFormat('id-ID').format(price)}
            </div>
        `,
        iconSize: [80, 34],
        iconAnchor: [40, 17]
    });

    const markers = {};
    const bounds = [];

    /* ================= RENDER MARKER FROM DATA ================= */
    parkingData.forEach(spot => {
        if (!spot.latitude || !spot.longitude) return;

        const marker = L.marker(
            [spot.latitude, spot.longitude],
            { icon: priceIcon(spot.harga_per_jam) }
        ).addTo(map);

        marker.on('click', () => {
            openDetailPopup(spot.id_tempat);
            setActiveCard(spot.id_tempat);
        });

        markers[spot.id_tempat] = marker;
        bounds.push([spot.latitude, spot.longitude]);
    });

    /* ================= FIT MAP ================= */
    if (bounds.length > 0) {
        map.fitBounds(bounds, { padding: [50, 50] });
    }

});
