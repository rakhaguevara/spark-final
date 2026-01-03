/**
 * DASHBOARD-SEARCH-SORT.JS
 * Handles search and sort functionality for parking cards
 */

(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {

        const searchInput = document.getElementById('searchInput');
        const sortSelect = document.getElementById('sortSelect');
        const parkingContainer = document.getElementById('parkingList');
        const parkingCards = document.querySelectorAll('.parking-card');

        // Search functionality
        if (searchInput) {
            searchInput.addEventListener('input', function (e) {
                const searchTerm = e.target.value.toLowerCase();

                parkingCards.forEach(card => {
                    const title = card.querySelector('.card-title');
                    if (title) {
                        const titleText = title.textContent.toLowerCase();

                        if (titleText.includes(searchTerm)) {
                            card.style.display = 'flex';
                        } else {
                            card.style.display = 'none';
                        }
                    }
                });
            });
        }

        // Sort functionality
        if (sortSelect && parkingContainer) {
            sortSelect.addEventListener('change', function () {
                const sortValue = this.value;
                const cardsArray = Array.from(parkingCards);

                cardsArray.sort((a, b) => {
                    switch (sortValue) {
                        case 'price-low':
                            return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
                        case 'price-high':
                            return parseFloat(b.dataset.price) - parseFloat(a.dataset.price);
                        case 'rating':
                            return parseFloat(b.dataset.rating) - parseFloat(a.dataset.rating);
                        default:
                            return 0;
                    }
                });

                cardsArray.forEach(card => parkingContainer.appendChild(card));
            });
        }
    });

})();
