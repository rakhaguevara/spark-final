<?php
/**
 * PARKING CARD COMPONENT
 */

function renderParkingCard($spot, $baseUrl = '') {
    $id       = $spot['id_tempat'];
    $name     = htmlspecialchars($spot['nama_tempat']);
    $price    = $spot['harga_per_jam'];
    $rating   = number_format($spot['avg_rating'] ?? 4.5, 1);
    $review   = $spot['total_review'] ?? 0;
    $slots    = $spot['slot_tersedia'] ?? 0;
    $lat      = $spot['latitude'] ?? '';
    $lng      = $spot['longitude'] ?? '';

    $image = $baseUrl . '/assets/img/content-1.png';
    if (!empty($spot['foto_tempat'])) {
        $image = $baseUrl . '/assets/img/' . $spot['foto_tempat'];
    }

    // Availability state logic (data-driven - GREEN/RED only)
    $isFull = ($slots == 0);
    $badgeClass = '';
    $badgeText = '';
    
    if ($isFull) {
        // RED - No slots available
        $badgeClass = 'badge-full';
        $badgeText = 'Full';
    } else {
        // GREEN - Slots available
        $badgeClass = 'badge-available';
        $badgeText = $slots . ' available';
    }
    ?>

    <?php
    // Prepare vehicle types array for filtering
    $vehicleTypes = array_map(function($v) {
        return $v['nama_jenis'];
    }, $spot['vehicle_availability']);
    $vehicleTypesJson = htmlspecialchars(json_encode($vehicleTypes), ENT_QUOTES, 'UTF-8');
    ?>

    <div class="parking-card"
         data-id="<?= $id ?>"
         data-price="<?= $price ?>"
         data-rating="<?= $rating ?>"
         data-lat="<?= $lat ?>"
         data-lng="<?= $lng ?>"
         data-vehicle-types="<?= $vehicleTypesJson ?>"
         onclick="focusMapOnSpot(<?= $lat ?>, <?= $lng ?>, <?= $id ?>)">

        <div class="card-image-wrapper">
            <img src="<?= $image ?>" alt="<?= $name ?>" class="card-image">
            <div class="availability-badge <?= $badgeClass ?>">
                <i class="fas fa-parking"></i>
                <span><?= $badgeText ?></span>
            </div>
        </div>

        <div class="card-content-wrapper">
            <div class="card-header">
                <h3 class="card-title"><?= $name ?></h3>
                <div class="card-address">
                    <i class="fas fa-map-marker-alt"></i>
                    <span><?= htmlspecialchars($spot['alamat_tempat'] ?? 'Alamat tidak tersedia') ?></span>
                </div>
                
                <?php if (!empty($spot['vehicle_availability'])): ?>
                    <div class="vehicle-badges">
                        <?php foreach ($spot['vehicle_availability'] as $vehicle): ?>
                            <?php 
                                $vehicleClass = '';
                                $vehicleIcon = 'fa-parking';
                                
                                if (stripos($vehicle['nama_jenis'], 'motor') !== false) {
                                    $vehicleClass = 'vehicle-motor';
                                    $vehicleIcon = 'fa-motorcycle';
                                } elseif (stripos($vehicle['nama_jenis'], 'mobil') !== false) {
                                    $vehicleClass = 'vehicle-mobil';
                                    $vehicleIcon = 'fa-car';
                                }
                            ?>
                            <div class="vehicle-badge <?= $vehicleClass ?>">
                                <i class="fas <?= $vehicleIcon ?>"></i>
                                <span><?= htmlspecialchars($vehicle['nama_jenis']) ?> · <?= $vehicle['available_count'] ?> slot</span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <div class="card-meta-row">
                    <?php if ($rating > 0): ?>
                        <div class="rating-badge">
                            <i class="fas fa-star"></i>
                            <span class="rating-value"><?= $rating ?></span>
                            <?php if ($review > 0): ?>
                                <span class="review-count">(<?= $review ?>)</span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <div class="distance-info">
                        <i class="fas fa-walking"></i>
                        <span>9 min</span>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <div class="price-section">
                    <div class="price-value">Rp <?= number_format($price, 0, ',', '.') ?></div>
                    <div class="price-unit">per hour</div>
                </div>

                <div class="card-actions">
                    <button class="btn-card-details"
                            data-card-id="<?= $id ?>"
                            onclick="event.stopPropagation(); openBookingModal(<?= $id ?>)">
                        Details
                    </button>
                    <?php if ($isFull): ?>
                        <button class="btn-card-book btn-disabled"
                                disabled
                                aria-disabled="true"
                                onclick="event.stopPropagation()">
                            Fully Booked
                        </button>
                    <?php else: ?>
                        <a href="<?= $baseUrl ?>/pages/booking.php?id=<?= $id ?>"
                           class="btn-card-book"
                           onclick="event.stopPropagation()">
                            Book Now
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php
}

function renderParkingCards($spots, $baseUrl = '') {
    if (empty($spots)) {
        echo '<div class="parking-cards-empty">No parking available</div>';
        return;
    }

    foreach ($spots as $spot) {
        renderParkingCard($spot, $baseUrl);
    }
}
