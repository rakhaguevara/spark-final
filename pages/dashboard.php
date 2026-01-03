<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../functions/auth.php';

startSession();

/* ================= PROTEKSI LOGIN ================= */
if (!isLoggedIn()) {
    header('Location: ' . BASEURL . '/pages/login.php');
    exit;
}

$user = getCurrentUser();

/* ================= AMBIL DATA PARKIR ================= */
// Ambil data tempat parkir dari database
$pdo = getDBConnection();
$stmt = $pdo->query("
    SELECT 
        tp.id_tempat,
        tp.nama_tempat,
        tp.alamat_tempat,
        tp.latitude,
        tp.longitude,
        tp.harga_per_jam,
        COUNT(sp.id_slot) as total_slot,
        SUM(CASE WHEN sp.status_slot = 'available' THEN 1 ELSE 0 END) as slot_tersedia
    FROM tempat_parkir tp
    LEFT JOIN slot_parkir sp ON tp.id_tempat = sp.id_tempat
    GROUP BY tp.id_tempat
    ORDER BY tp.nama_tempat ASC
");
$parkingSpots = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard | SPARK</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- FAVICON -->
    <link rel="icon" type="image/png" href="<?= BASEURL ?>/assets/img/logo.png">

    <!-- GOOGLE FONT -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- LEAFLET -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

    <!-- BOOTSTRAP -->
    <link rel="stylesheet" href="<?= BASEURL ?>/assets/css/bootstrap.min.css">

    <!-- DASHBOARD CSS -->
    <link rel="stylesheet" href="<?= BASEURL ?>/assets/css/dashboardassets/css/dashboard.css.css">
</head>

<body>

<!-- NAVBAR -->
<?php require_once __DIR__ . '/../includes/dashboard-navbar.php'; ?>

<div class="dashboard-wrapper">

    <!-- SIDEBAR -->
    <?php require_once __DIR__ . '/../includes/dashboard-sidebar.php'; ?>

    <main class="dashboard-main">

        <!-- HEADER -->
        <div class="dashboard-header">
            <div>
                <h1>Find Your Parking Spot</h1>
                <p>Temukan dan reservasi tempat parkir terdekat dengan mudah</p>
            </div>

            <div class="dashboard-user-info">
                <div class="user-avatar">
                    <?= strtoupper(substr($user['nama_pengguna'], 0, 1)) ?>
                </div>
                <div>
                    <strong><?= htmlspecialchars($user['nama_pengguna']) ?></strong>
                    <span><?= htmlspecialchars($user['nama_role']) ?></span>
                </div>
            </div>
        </div>

        <!-- CONTENT -->
        <div class="dashboard-content">

            <!-- PARKING LIST -->
            <section class="parking-list">
                <div class="list-header">
                    <div class="filter-badges">
                        <button class="badge active" data-sort="distance">Shortest Walk</button>
                        <button class="badge" data-sort="price">Best Price</button>
                        <button class="badge" data-sort="rating">Top Rated</button>
                    </div>

                    <div class="sort-dropdown">
                        <label>Sort by:</label>
                        <select id="sortSelect">
                            <option value="relevance">Relevance</option>
                            <option value="price-asc">Price: Low to High</option>
                            <option value="price-desc">Price: High to Low</option>
                            <option value="availability">Availability</option>
                        </select>
                    </div>
                </div>

                <div id="parkingList" class="parking-items">
                    <?php if (!empty($parkingSpots)): ?>
                        <?php foreach ($parkingSpots as $index => $spot): ?>
                            <div class="parking-item <?= $index === 0 ? 'active' : '' ?>"
                                 data-id="<?= $spot['id_tempat_parkir'] ?>"
                                 data-lat="<?= $spot['latitude'] ?>"
                                 data-lng="<?= $spot['longitude'] ?>"
                                 data-price="<?= $spot['tarif_per_jam'] ?>">

                                <img src="<?= BASEURL ?>/assets/img/<?= $spot['foto_tempat_parkir'] ?? 'parking.jpg' ?>"
                                     alt="<?= htmlspecialchars($spot['nama_tempat_parkir']) ?>">

                                <div class="parking-info">
                                    <h4><?= htmlspecialchars($spot['nama_tempat_parkir']) ?></h4>

                                    <div class="parking-address">
                                        📍 <?= htmlspecialchars($spot['alamat']) ?>
                                    </div>

                                    <div class="parking-meta">
                                        <span class="rating">⭐ 4.8 (<?= rand(10, 50) ?> reviews)</span>
                                        <span class="availability">
                                            <?= $spot['slot_tersedia'] ?>/<?= $spot['total_slot'] ?> tersedia
                                        </span>
                                    </div>

                                    <div class="parking-footer">
                                        <div class="price">
                                            Rp <?= number_format($spot['tarif_per_jam'], 0, ',', '.') ?>
                                            <span>/jam</span>
                                        </div>

                                        <button class="btn-book"
                                                onclick="bookParking(<?= $spot['id_tempat_parkir'] ?>)">
                                            Book Now
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <p>Belum ada tempat parkir tersedia saat ini.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <!-- MAP -->
            <section class="parking-map">
                <div id="map"></div>

                <div class="map-legend">
                    <div class="legend-item">
                        <span class="legend-marker available"></span>
                        <span>Available</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-marker busy"></span>
                        <span>Almost Full</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-marker full"></span>
                        <span>Full</span>
                    </div>
                </div>
            </section>

        </div>
    </main>
</div>

<!-- JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="<?= BASEURL ?>/assets/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASEURL ?>/assets/js/dashboard.js"></script>

<script>
    const parkingData = <?= json_encode($parkingSpots) ?>;
    const BASEURL = '<?= BASEURL ?>';
</script>

</body>
</html>
