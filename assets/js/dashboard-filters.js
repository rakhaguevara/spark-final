/**
 * DASHBOARD-FILTERS.JS
 * Handles filter functionality for parking cards and map markers
 */

(function () {
    'use strict';

    // Filter state
    let activeFilters = {
        vehicleType: null, // null = all, or specific type like 'Motor', 'Mobil'
        facilityType: null // 'self-park', 'garage', or null
    };

    document.addEventListener('DOMContentLoaded', function () {
        initializeFilters();
    });

    function initializeFilters() {
        // Set active state based on URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        const vehicleType = urlParams.get('vehicle_type');

        if (vehicleType) {
            const filterVehicleType = document.getElementById('filterVehicleType');
            if (filterVehicleType) {
                updateFilterButtonState(filterVehicleType, true);
            }
        } else {
            const filterAll = document.getElementById('filterAll');
            if (filterAll) {
                updateFilterButtonState(filterAll, true);
            }
        }

        // Filter All button - clear all filters
        const filterAll = document.getElementById('filterAll');
        if (filterAll) {
            filterAll.addEventListener('click', () => {
                // Remove all query parameters and reload
                window.location.href = window.location.pathname;
            });
        }

        // Vehicle Type filter
        const filterVehicleType = document.getElementById('filterVehicleType');
        if (filterVehicleType) {
            filterVehicleType.addEventListener('click', () => {
                showVehicleTypeMenu(filterVehicleType);
            });
        }

        // Facility filters
        const filterSelfPark = document.getElementById('filterSelfPark');
        if (filterSelfPark) {
            filterSelfPark.addEventListener('click', () => {
                toggleFacilityFilter('self-park', filterSelfPark);
            });
        }

        const filterGarage = document.getElementById('filterGarage');
        if (filterGarage) {
            filterGarage.addEventListener('click', () => {
                toggleFacilityFilter('garage', filterGarage);
            });
        }
    }

    function showVehicleTypeMenu(button) {
        // Create dropdown menu
        const existingMenu = document.querySelector('.vehicle-type-menu');
        if (existingMenu) {
            existingMenu.remove();
            return;
        }

        const menu = document.createElement('div');
        menu.className = 'vehicle-type-menu';
        menu.innerHTML = `
            <div class="filter-menu-item" data-type="">All Vehicles</div>
            <div class="filter-menu-item" data-type="Motor">Motor</div>
            <div class="filter-menu-item" data-type="Mobil">Mobil</div>
        `;

        // Append to body for proper positioning
        document.body.appendChild(menu);

        // Position menu below button
        const rect = button.getBoundingClientRect();
        menu.style.position = 'fixed';
        menu.style.top = (rect.bottom + 5) + 'px';
        menu.style.left = rect.left + 'px';
        menu.style.zIndex = '10000';

        // Add click handlers - reload page with query param
        menu.querySelectorAll('.filter-menu-item').forEach(item => {
            item.addEventListener('click', (e) => {
                const type = e.target.dataset.type;

                // Build URL with filter parameter
                const url = new URL(window.location.href);
                if (type) {
                    url.searchParams.set('vehicle_type', type);
                } else {
                    url.searchParams.delete('vehicle_type');
                }

                // Reload page with filter
                window.location.href = url.toString();
            });
        });

        // Close menu when clicking outside
        setTimeout(() => {
            document.addEventListener('click', function closeMenu(e) {
                if (!menu.contains(e.target) && e.target !== button) {
                    menu.remove();
                    document.removeEventListener('click', closeMenu);
                }
            });
        }, 0);
    }

    function toggleFacilityFilter(type, button) {
        if (activeFilters.facilityType === type) {
            activeFilters.facilityType = null;
            updateFilterButtonState(button, false);
        } else {
            // Deactivate other facility filters
            document.querySelectorAll('#filterSelfPark, #filterGarage').forEach(btn => {
                updateFilterButtonState(btn, false);
            });

            activeFilters.facilityType = type;
            updateFilterButtonState(button, true);
        }

        applyFilters();
    }

    function resetFilters() {
        activeFilters = {
            vehicleType: null,
            facilityType: null
        };

        // Reset all button states
        document.querySelectorAll('.filter-btn').forEach(btn => {
            updateFilterButtonState(btn, false);
        });

        // Activate "All" button
        const filterAll = document.getElementById('filterAll');
        if (filterAll) {
            updateFilterButtonState(filterAll, true);
        }
    }

    function updateFilterButtonState(button, isActive) {
        if (isActive) {
            button.classList.add('active');
        } else {
            button.classList.remove('active');
        }
    }

    function applyFilters() {
        const cards = document.querySelectorAll('.parking-card');

        cards.forEach(card => {
            const shouldShow = cardMatchesFilters(card);

            if (shouldShow) {
                card.style.display = '';
                showMarker(card.dataset.id);
            } else {
                card.style.display = 'none';
                hideMarker(card.dataset.id);
            }
        });
    }

    function cardMatchesFilters(card) {
        // Vehicle Type filter
        if (activeFilters.vehicleType) {
            const vehicleTypesJson = card.dataset.vehicleTypes;
            if (!vehicleTypesJson) return false;

            try {
                const vehicleTypes = JSON.parse(vehicleTypesJson);
                if (!vehicleTypes.includes(activeFilters.vehicleType)) {
                    return false;
                }
            } catch (e) {
                console.error('Error parsing vehicle types:', e);
                return false;
            }
        }

        // Facility Type filter (placeholder - needs backend data)
        if (activeFilters.facilityType) {
            // TODO: Add facility type data attribute to cards
            // For now, show all cards
        }

        return true;
    }

    function showMarker(id) {
        if (window.parkingMarkers && window.parkingMarkers[id]) {
            window.parkingMarkers[id].addTo(window.parkingMap);
        }
    }

    function hideMarker(id) {
        if (window.parkingMarkers && window.parkingMarkers[id]) {
            window.parkingMap.removeLayer(window.parkingMarkers[id]);
        }
    }

    // Expose reset function globally
    window.resetParkingFilters = resetFilters;

})();
