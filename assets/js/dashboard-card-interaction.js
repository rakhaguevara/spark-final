/**
 * DASHBOARD-CARD-INTERACTION.JS
 * Handles parking card click interactions and map focus
 */

(function () {
    'use strict';

    let activeCardId = null;

    // Focus map on spot when card is clicked
    function focusMapOnSpot(lat, lng, spotId) {
        if (!lat || !lng || !window.parkingMap) return;

        // Remove previous active state
        document.querySelectorAll('.parking-card').forEach(card => {
            card.classList.remove('active', 'map-focused');
        });

        // Add active state to clicked card
        const clickedCard = document.querySelector(`.parking-card[data-id="${spotId}"]`);
        if (clickedCard) {
            clickedCard.classList.add('active', 'map-focused');
            activeCardId = spotId;

            // Remove animation class after animation completes
            setTimeout(() => {
                clickedCard.classList.remove('map-focused');
            }, 1000);
        }

        // Smooth map animation
        window.parkingMap.flyTo([lat, lng], 16, {
            duration: 0.8,
            easeLinearity: 0.25
        });

        // Highlight marker
        if (window.parkingMarkers && window.parkingMarkers[spotId]) {
            window.parkingMarkers[spotId].openPopup();
        }
    }

    // Expose to global scope
    window.focusMapOnSpot = focusMapOnSpot;

})();
