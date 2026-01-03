<!-- BOOKING MODAL -->
<div id="bookingModal" class="booking-modal" style="display: none;">
    <div class="booking-modal-overlay" onclick="closeBookingModal()"></div>
    
    <div class="booking-modal-content">
        <!-- Close Button -->
        <button class="modal-close-btn" onclick="closeBookingModal()">
            <i class="fas fa-times"></i>
        </button>
        
        <!-- Image Section -->
        <div class="modal-image-section">
            <img id="modalImage" src="" alt="" class="modal-image">
            <div class="modal-availability-badge" id="modalAvailabilityBadge">
                <i class="fas fa-parking"></i>
                <span id="modalAvailabilityText">5 available</span>
            </div>
        </div>
        
        <!-- Content Section -->
        <div class="modal-content-section">
            <!-- Header -->
            <div class="modal-header">
                <h2 id="modalTitle" class="modal-title">Parking Name</h2>
                <div class="modal-rating">
                    <i class="fas fa-star"></i>
                    <span id="modalRating">4.5</span>
                    <span class="modal-reviews" id="modalReviews">(120 reviews)</span>
                </div>
            </div>
            
            <!-- Location -->
            <div class="modal-location">
                <i class="fas fa-map-marker-alt"></i>
                <span id="modalAddress">Address here</span>
            </div>
            
            <!-- Warning Box -->
            <div class="modal-warning-box" id="modalWarningBox">
                <i class="fas fa-exclamation-circle"></i>
                <div>
                    <strong>Book now!</strong>
                    <p>Only <span id="modalSlotsRemaining">5</span> spots remaining for your selected time.</p>
                </div>
            </div>
            
            <!-- Vehicle Availability -->
            <div class="modal-vehicle-section" id="modalVehicleSection">
                <h3 class="modal-section-title">Available For</h3>
                <div class="modal-vehicle-badges" id="modalVehicleBadges">
                    <!-- Vehicle badges will be inserted here -->
                </div>
            </div>
            
            <!-- Facilities -->
            <div class="modal-facilities-section">
                <h3 class="modal-section-title">Facilities</h3>
                <div class="modal-facilities" id="modalFacilities">
                    <!-- Facilities will be inserted here -->
                </div>
            </div>
            
            <!-- Pricing -->
            <div class="modal-pricing-section">
                <div class="modal-price-row">
                    <span class="modal-price-label">Hourly Rate</span>
                    <span class="modal-price-value" id="modalPrice">Rp 5.000</span>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="modal-actions">
                <button class="modal-btn-secondary" onclick="closeBookingModal()">
                    Close
                </button>
                <a href="#" id="modalBookNowBtn" class="modal-btn-primary">
                    Book Now
                </a>
            </div>
        </div>
    </div>
</div>
