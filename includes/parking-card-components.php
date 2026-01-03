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
        $image = $baseUrl . '/assets/img/park/' . $spot['foto_tempat'];
    }

    $badgeClass = '';
    if ($slots <= 2) $badgeClass = 'danger';
    elseif ($slots <= 5) $badgeClass = 'warning';
    ?>

    <div class="parking-card"
         data-id="<?= $id ?>"
         data-price="<?= $price ?>"
         data-rating="<?= $rating ?>"
         data-lat="<?= $lat ?>"
         data-lng="<?= $lng ?>">

        <div class="parking-card-image">
            <img src="<?= $image ?>" alt="<?= $name ?>">
            <?php if ($slots > 0): ?>
                <div class="spots-badge <?= $badgeClass ?>">
                    <i class="fas fa-check-circle"></i>
                    <?= $slots ?> spots left
                </div>
            <?php endif; ?>
        </div>

        <div class="parking-card-content">
            <h3 class="parking-card-title"><?= $name ?></h3>

            <div class="parking-card-meta">
                <div class="parking-card-rating">
                    ⭐ <?= $rating ?>
                    <?php if ($review > 0): ?>
                        <span class="count">(<?= $review ?>)</span>
                    <?php endif; ?>
                </div>
                <span class="meta-divider">•</span>
                <div class="parking-card-distance">
                    <i class="fas fa-walking"></i> 9 min (0.4 mi)
                </div>
            </div>

            <div class="parking-card-footer">
                <div class="parking-card-price">
                    <div class="price-amount">
                        Rp <?= number_format($price, 0, ',', '.') ?>
                    </div>
                    <div class="price-label">Subtotal</div>
                </div>

                <div class="parking-card-actions">
                    <button class="btn-card-details"
                            data-card-id="<?= $id ?>">
                        Details
                    </button>
                    <a href="<?= $baseUrl ?>/pages/booking.php?id=<?= $id ?>"
                       class="btn-card-book">
                        Book Now
                    </a>
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
