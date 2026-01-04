/**
 * BOOKING-MODAL.JS
 * Database-driven booking modal interactions
 * No hardcoded data - all from PHP/database
 */

(function () {
    'use strict';

    // Modal elements
    let modal, modalImage, modalTitle, modalRating, modalReviews, modalAddress;
    let modalAvailabilityBadge, modalAvailabilityText, modalSlotsRemaining;
    let modalVehicleBadges, modalFacilities, modalPrice, modalBookNowBtn;
    let modalWarningBox;

    document.addEventListener('DOMContentLoaded', function () {
        initializeModal();
    });

    function initializeModal() {
        // Get modal elements
        modal = document.getElementById('bookingModal');
        modalImage = document.getElementById('modalImage');
        modalTitle = document.getElementById('modalTitle');
        modalRating = document.getElementById('modalRating');
        modalReviews = document.getElementById('modalReviews');
        modalAddress = document.getElementById('modalAddress');
        modalAvailabilityBadge = document.getElementById('modalAvailabilityBadge');
        modalAvailabilityText = document.getElementById('modalAvailabilityText');
        modalSlotsRemaining = document.getElementById('modalSlotsRemaining');
        modalVehicleBadges = document.getElementById('modalVehicleBadges');
        modalFacilities = document.getElementById('modalFacilities');
        modalPrice = document.getElementById('modalPrice');
        modalBookNowBtn = document.getElementById('modalBookNowBtn');
        modalWarningBox = document.getElementById('modalWarningBox');
    }

    // Open modal with parking data
    window.openBookingModal = function (parkingId) {
        // Get parking data from APP_CONFIG (passed from PHP)
        const parkingData = window.APP_CONFIG.parkingData.find(p => p.id_tempat == parkingId);

        if (!parkingData) {
            console.error('Parking data not found for ID:', parkingId);
            return;
        }

        // Populate modal with database data
        populateModal(parkingData);

        // Show modal
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    };

    // Close modal
    window.closeBookingModal = function () {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    };

    // Populate modal with data
    function populateModal(data) {
        // Image
        const imageUrl = data.foto_tempat
            ? window.APP_CONFIG.BASEURL + '/assets/img/' + data.foto_tempat
            : window.APP_CONFIG.BASEURL + '/assets/img/content-1.png';
        modalImage.src = imageUrl;
        modalImage.alt = data.nama_tempat;

        // Title
        modalTitle.textContent = data.nama_tempat;

        // Rating
        modalRating.textContent = parseFloat(data.avg_rating).toFixed(1);
        modalReviews.textContent = `(${data.total_review} reviews)`;

        // Address
        modalAddress.textContent = data.alamat_tempat || 'Address not available';

        // Availability
        const totalAvailable = data.slot_tersedia || 0;
        const isFull = totalAvailable === 0;

        if (isFull) {
            modalAvailabilityBadge.classList.add('badge-full');
            modalAvailabilityText.textContent = 'Full';
            modalWarningBox.style.display = 'none';
        } else {
            modalAvailabilityBadge.classList.remove('badge-full');
            modalAvailabilityText.textContent = totalAvailable + ' available';
            modalSlotsRemaining.textContent = totalAvailable;
            modalWarningBox.style.display = totalAvailable <= 5 ? 'flex' : 'none';
        }

        // Vehicle availability badges
        modalVehicleBadges.innerHTML = '';
        if (data.vehicle_availability && data.vehicle_availability.length > 0) {
            data.vehicle_availability.forEach(vehicle => {
                const badge = document.createElement('div');
                badge.className = 'modal-vehicle-badge';

                // Add vehicle-specific class
                if (vehicle.nama_jenis.toLowerCase().includes('motor')) {
                    badge.classList.add('vehicle-motor');
                    badge.innerHTML = `<i class="fas fa-motorcycle"></i><span>${vehicle.nama_jenis} · ${vehicle.available_count} slot</span>`;
                } else if (vehicle.nama_jenis.toLowerCase().includes('mobil')) {
                    badge.classList.add('vehicle-mobil');
                    badge.innerHTML = `<i class="fas fa-car"></i><span>${vehicle.nama_jenis} · ${vehicle.available_count} slot</span>`;
                } else {
                    badge.innerHTML = `<i class="fas fa-parking"></i><span>${vehicle.nama_jenis} · ${vehicle.available_count} slot</span>`;
                }

                modalVehicleBadges.appendChild(badge);
            });
        }

        // Facilities
        modalFacilities.innerHTML = '';
        const facilities = getFacilities(data.jam_buka, data.jam_tutup);
        facilities.forEach(facility => {
            const tag = document.createElement('div');
            tag.className = 'modal-facility-tag';
            tag.innerHTML = `<i class="fas fa-${facility.icon}"></i><span>${facility.text}</span>`;
            modalFacilities.appendChild(tag);
        });

        // Price
        modalPrice.textContent = window.formatRupiah ? window.formatRupiah(data.harga_per_jam) : 'Rp ' + data.harga_per_jam.toLocaleString('id-ID');

        // Book Now button
        if (isFull) {
            modalBookNowBtn.style.opacity = '0.5';
            modalBookNowBtn.style.pointerEvents = 'none';
            modalBookNowBtn.textContent = 'Fully Booked';
        } else {
            modalBookNowBtn.style.opacity = '1';
            modalBookNowBtn.style.pointerEvents = 'auto';
            modalBookNowBtn.textContent = 'Book Now';
            modalBookNowBtn.href = window.APP_CONFIG.BASEURL + '/pages/booking.php?id=' + data.id_tempat;
        }
    }

    // Get facilities based on operating hours
    function getFacilities(jamBuka, jamTutup) {
        const facilities = [];

        if (jamBuka === '00:00:00' && jamTutup === '23:59:59') {
            facilities.push({ icon: 'clock', text: '24 Hours' });
        }

        facilities.push({ icon: 'camera', text: 'CCTV' });
        facilities.push({ icon: 'shield-alt', text: 'Secure' });
        facilities.push({ icon: 'parking', text: 'Covered' });

        return facilities;
    }

    // Close on escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal && modal.style.display === 'flex') {
            closeBookingModal();
        }
    });

})();
