document.addEventListener('DOMContentLoaded', () => {

    const mapEl = document.getElementById('map');
    if (!mapEl) return;

    const map = L.map('map', {
        zoomControl: true
    }).setView([-7.7965, 110.3690], 14); // Yogyakarta

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // FIX ukuran saat layout flex/grid
    setTimeout(() => {
        map.invalidateSize();
    }, 200);

    // ===== PRICE MARKER =====
    const priceIcon = (price) => L.divIcon({
        className: 'price-marker',
        html: `<div class="price-bubble">$${price}</div>`,
        iconSize: [40, 40],
        iconAnchor: [20, 20]
    });

    L.marker([-7.7965, 110.3690], { icon: priceIcon(5) }).addTo(map);
    L.marker([-7.7982, 110.3721], { icon: priceIcon(7) }).addTo(map);
});
