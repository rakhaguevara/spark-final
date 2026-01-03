let map;
let markers = [];

document.addEventListener('DOMContentLoaded', () => {
    initializeMap();
    initializeFilters();
    initializeParkingCards();
});

/* ================= MAP ================= */
function initializeMap() {
    const defaultLat = -7.797068;
    const defaultLng = 110.370529;

    map = L.map('map').setView([defaultLat, defaultLng], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(map);

    if (typeof parkingData !== 'undefined' && parkingData.length > 0) {
        parkingData.forEach((spot, index) => {
            addMarker(spot, index === 0);
        });

        if (parkingData[0].latitude && parkingData[0].longitude) {
            map.setView(
                [parkingData[0].latitude, parkingData[0].longitude],
                14
            );
        }
    }
}

function addMarker(spot, isActive = false) {
    if (!spot.latitude || !spot.longitude) return;

    const availability =
        spot.total_slot > 0
            ? spot.slot_tersedia / spot.total_slot
            : 0;

    let markerColor = '#10b981';
    if (availability < 0.3) markerColor = '#ef4444';
    else if (availability < 0.6) markerColor = '#f59e0b';

    const icon = L.divIcon({
        className: 'custom-marker',
        html: `
            <div style="
                background:${markerColor};
                width:32px;height:32px;
                border-radius:50%;
                border:3px solid white;
                box-shadow:0 2px 8px rgba(0,0,0,.3);
                display:flex;
                align-items:center;
                justify-content:center;
                color:white;
                font-weight:bold;
            ">P</div>
        `,
        iconSize: [32, 32],
        iconAnchor: [16, 16]
    });

    const marker = L.marker(
        [spot.latitude, spot.longitude],
        { icon }
    ).addTo(map);

    marker.bindPopup(`
        <div style="padding:.5rem">
            <h4 style="margin:0 0 .25rem 0">${spot.nama_tempat_parkir}</h4>
            <p style="margin:0;font-size:.85rem;color:#64748b">
                ${spot.alamat}
            </p>
            <div style="margin-top:.5rem;display:flex;justify-content:space-between">
                <strong style="color:#6366f1">
                    Rp ${Number(spot.tarif_per_jam).toLocaleString('id-ID')}
                </strong>
                <button onclick="bookParking(${spot.id_tempat_parkir})"
                    style="padding:.35rem .75rem;background:#6366f1;color:white;border:none;border-radius:6px">
                    Book
                </button>
            </div>
        </div>
    `);

    markers.push({
        id: spot.id_tempat_parkir,
        marker
    });

    if (isActive) marker.openPopup();
}

/* ================= FILTER & SORT ================= */
function initializeFilters() {
    const badges = document.querySelectorAll('.filter-badges .badge');

    badges.forEach(badge => {
        badge.addEventListener('click', () => {
            badges.forEach(b => b.classList.remove('active'));
            badge.classList.add('active');
            sortParkingList(badge.dataset.sort);
        });
    });

    const sortSelect = document.getElementById('sortSelect');
    if (sortSelect) {
        sortSelect.addEventListener('change', () => {
            sortParkingList(sortSelect.value);
        });
    }
}

function sortParkingList(type) {
    const list = document.getElementById('parkingList');
    const items = [...list.querySelectorAll('.parking-item')];

    items.sort((a, b) => {
        const priceA = parseFloat(a.dataset.price);
        const priceB = parseFloat(b.dataset.price);

        if (type === 'price' || type === 'price-asc') return priceA - priceB;
        if (type === 'price-desc') return priceB - priceA;
        return 0;
    });

    items.forEach(item => list.appendChild(item));
}

/* ================= CARD CLICK ================= */
function initializeParkingCards() {
    const cards = document.querySelectorAll('.parking-item');

    cards.forEach(card => {
        card.addEventListener('click', () => {
            cards.forEach(c => c.classList.remove('active'));
            card.classList.add('active');

            const id = Number(card.dataset.id);
            const lat = Number(card.dataset.lat);
            const lng = Number(card.dataset.lng);

            if (lat && lng) {
                map.setView([lat, lng], 16);
                const m = markers.find(m => m.id === id);
                if (m) m.marker.openPopup();
            }
        });
    });
}

/* ================= BOOKING ================= */
function bookParking(id) {
    window.location.href = `${BASEURL}/pages/booking.php?id=${id}`;
}

/* ================= SEARCH ================= */
function searchParking(query) {
    const q = query.toLowerCase();
    document.querySelectorAll('.parking-item').forEach(item => {
        const title = item.querySelector('h4').textContent.toLowerCase();
        const address = item.querySelector('.parking-address').textContent.toLowerCase();
        item.style.display =
            title.includes(q) || address.includes(q) ? 'flex' : 'none';
    });
}

/* ================= USER LOCATION ================= */
function getUserLocation() {
    if (!navigator.geolocation) return;

    navigator.geolocation.getCurrentPosition(pos => {
        const { latitude, longitude } = pos.coords;

        L.marker([latitude, longitude], {
            icon: L.divIcon({
                className: 'user-marker',
                html: `<div style="background:#3b82f6;width:20px;height:20px;border-radius:50%;border:3px solid white"></div>`,
                iconSize: [20, 20]
            })
        }).addTo(map).bindPopup('Your Location');

        map.setView([latitude, longitude], 14);
    });
}
