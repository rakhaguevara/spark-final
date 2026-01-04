/**
 * MOBILE INTERACTION CONTROLLER
 * Handles scroll snapping sync with map markers
 */

document.addEventListener('DOMContentLoaded', () => {
    // Only run on mobile/small screens where the bottom sheet exists
    if (window.innerWidth > 768) return;

    const container = document.querySelector('.parking-container');
    const cards = document.querySelectorAll('.parking-card');

    if (!container || !cards.length) return;

    console.log('Mobile Interaction Loaded: ' + cards.length + ' cards found');

    let isScrolling;

    container.addEventListener('scroll', () => {
        // Debounce to prevent excessive map movements
        window.clearTimeout(isScrolling);

        isScrolling = setTimeout(() => {
            const containerRect = container.getBoundingClientRect();
            const containerCenter = containerRect.left + containerRect.width / 2;

            let closestCard = null;
            let minDiff = Infinity;

            cards.forEach(card => {
                const rect = card.getBoundingClientRect();
                const cardCenter = rect.left + rect.width / 2;
                const diff = Math.abs(containerCenter - cardCenter);

                // Threshold: Card must be relatively close to center
                if (diff < minDiff) {
                    minDiff = diff;
                    closestCard = card;
                }
            });

            if (closestCard && minDiff < 100) { // Only if reasonably centered
                const lat = parseFloat(closestCard.dataset.lat);
                const lng = parseFloat(closestCard.dataset.lng);
                const id = closestCard.dataset.id;

                console.log('Focused Card ID:', id);

                if (window.parkingMap && lat && lng) {
                    // Smooth pan to location
                    window.parkingMap.flyTo([lat, lng], 16, {
                        animate: true,
                        duration: 0.8
                    });

                    // Highlight marker if available
                    if (window.parkingMarkers && window.parkingMarkers[id]) {
                        window.parkingMarkers[id].openPopup();

                        // Optional: Add a visual class to marker
                        // Object.values(window.parkingMarkers).forEach(m => m._icon.classList.remove('active-marker'));
                        // window.parkingMarkers[id]._icon.classList.add('active-marker');
                    }
                }
            }
        }, 100);
    }, { passive: true });
});
