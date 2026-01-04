/**
 * DASHBOARD-POPUP.JS
 * Handles detail popup functionality
 */

(function () {
    'use strict';

    let currentImageIndex = 0;
    let currentSpotId = null;

    // Open detail popup
    function openDetailPopup(spotId) {
        if (!window.APP_CONFIG) return;

        const { parkingData, BASEURL } = window.APP_CONFIG;
        const spot = parkingData.find(s => s.id_tempat == spotId);
        if (!spot) return;

        currentSpotId = spotId;
        currentImageIndex = 0;

        // Set data
        const imagePath = spot.foto_tempat
            ? `${BASEURL}/assets/img/park/${spot.foto_tempat}`
            : `${BASEURL}/assets/img/content-1.png`;

        document.getElementById('detailImage').src = imagePath;
        document.getElementById('detailTitle').textContent = spot.nama_tempat;
        document.getElementById('detailAddress').textContent = spot.alamat_tempat;
        document.getElementById('detailSlots').textContent = spot.slot_tersedia + ' spots';
        document.getElementById('detailPrice').textContent = window.formatRupiah(spot.harga_per_jam);

        // Set facilities
        const facilitiesHTML = `
            <span class="facility-tag"><i class="fas fa-clock"></i> 24 Hours</span>
            <span class="facility-tag"><i class="fas fa-camera"></i> CCTV</span>
            <span class="facility-tag"><i class="fas fa-shield-alt"></i> Secure</span>
            <span class="facility-tag"><i class="fas fa-parking"></i> Covered</span>
        `;
        document.getElementById('detailFacilities').innerHTML = facilitiesHTML;

        // Set booking link
        document.getElementById('detailBookBtn').onclick = function () {
            window.location.href = `${BASEURL}/pages/booking.php?id=${spotId}`;
        };

        document.getElementById('detailPopup').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    // Close detail popup
    function closeDetailPopup() {
        document.getElementById('detailPopup').classList.remove('active');
        document.body.style.overflow = '';
    }

    // Image navigation placeholders
    function prevImage() {
        console.log('Previous image');
    }

    function nextImage() {
        console.log('Next image');
    }

    // Close popup when clicking outside
    document.addEventListener('DOMContentLoaded', function () {
        const detailPopup = document.getElementById('detailPopup');
        if (detailPopup) {
            detailPopup.addEventListener('click', function (e) {
                if (e.target === this) {
                    closeDetailPopup();
                }
            });
        }
    });

    // Expose to global scope
    window.openDetailPopup = openDetailPopup;
    window.closeDetailPopup = closeDetailPopup;
    window.prevImage = prevImage;
    window.nextImage = nextImage;

})();
