document.addEventListener("DOMContentLoaded", function () {

const map = L.map('dashboard-map').setView([-7.797068, 110.370529], 14);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
attribution: '© OpenStreetMap'
}).addTo(map);

const parkingLocations = [
{ lat: -7.7975, lng: 110.3702, price: '$5' },
{ lat: -7.7958, lng: 110.3685, price: '$5' }
];

parkingLocations.forEach(p => {
L.marker([p.lat, p.lng])
.addTo(map)
.bindPopup(`<b>Parking Spot</b><br>Price: ${p.price}`);
});
});