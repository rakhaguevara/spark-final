<!-- BOOKING SUMMARY (Right Column) -->
<div class="summary-card">
    <h2 class="summary-title">Booking Summary</h2>
    
    <!-- Parking Info -->
    <div class="summary-section">
        <h3 class="section-title">Parking Location</h3>
        <div class="parking-info">
            <div class="parking-name"><?= htmlspecialchars($parking['nama_tempat']) ?></div>
            <div class="parking-address">
                <i class="fas fa-map-marker-alt"></i>
                <?= htmlspecialchars($parking['alamat_tempat']) ?>
            </div>
        </div>
    </div>

    <!-- Reservation Details -->
    <div class="summary-section">
        <h3 class="section-title">Reservation Details</h3>
        <div class="detail-row">
            <span class="detail-label">Date</span>
            <span class="detail-value"><?= date('D, M d, Y', strtotime($bookingData['date'])) ?></span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Time</span>
            <span class="detail-value"><?= $bookingData['time_start'] ?></span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Duration</span>
            <span class="detail-value"><?= $bookingData['duration'] ?> hours</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Available Slots</span>
            <span class="detail-value"><?= $parking['slot_tersedia'] ?> slots</span>
        </div>
    </div>

    <!-- Price Breakdown -->
    <div class="summary-section">
        <h3 class="section-title">Price Breakdown</h3>
        <div class="detail-row">
            <span class="detail-label">Rate per hour</span>
            <span class="detail-value">Rp <?= number_format($parking['harga_per_jam'], 0, ',', '.') ?></span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Duration</span>
            <span class="detail-value"><?= $bookingData['duration'] ?> hours</span>
        </div>
        <div class="detail-row total-row">
            <span class="detail-label">Total Price</span>
            <span class="detail-value total-price">
                Rp <?= number_format($parking['harga_per_jam'] * $bookingData['duration'], 0, ',', '.') ?>
            </span>
        </div>
    </div>

    <!-- Info Box -->
    <div class="info-box">
        <i class="fas fa-info-circle"></i>
        <p>Your parking slot will be reserved after payment confirmation.</p>
    </div>
</div>
