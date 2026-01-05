<?php

require_once __DIR__ . '/../functions/format.php';
require_once __DIR__ . '/../config/database.php'; // kalau DB terpisah


// Get database connection
$pdo = getDBConnection();

// Query untuk mengambil data tempat parkir dengan informasi lengkap (LIMIT 10 untuk performa)
$query = "
    SELECT 
        tp.*,
        dp.nama_pengguna as pemilik_nama,
        COALESCE(AVG(ut.rating), 0) as avg_rating,
        COUNT(DISTINCT ut.id_ulasan) as total_reviews,
        COUNT(DISTINCT sp.id_slot) as total_slots,
        SUM(CASE WHEN sp.status_slot = 'available' THEN 1 ELSE 0 END) as available_slots
    FROM tempat_parkir tp
    LEFT JOIN data_pengguna dp ON tp.id_pemilik = dp.id_pengguna
    LEFT JOIN ulasan_tempat ut ON tp.id_tempat = ut.id_tempat
    LEFT JOIN slot_parkir sp ON tp.id_tempat = sp.id_tempat
    GROUP BY tp.id_tempat
    ORDER BY tp.created_at DESC
    LIMIT 10
";

$stmt = $pdo->query($query);
$tempat_parkir = $stmt->fetchAll();

// Fungsi untuk mendapatkan icon fasilitas berdasarkan data tempat parkir
function getFacilityIcons($tempat) {
    $facilities = [];
    
    // Cek keamanan plat
    if ($tempat['is_plat_required']) {
        $facilities[] = '🔐 Secure';
    }
    
    // Cek jam operasional 24 jam
    if ($tempat['jam_buka'] == '00:00:00' && $tempat['jam_tutup'] == '23:59:59') {
        $facilities[] = '⏰ 24 Jam';
    }
    
    // CCTV selalu ada
    $facilities[] = '📷 CCTV';
    
    // Jumlah slot tersedia
    if ($tempat['total_slots'] > 0) {
        $facilities[] = '🅿️ ' . ($tempat['available_slots'] ?? 0) . '/' . $tempat['total_slots'] . ' Slot';
    }
    
    return $facilities;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Parking - SPARK</title>
    <link rel="stylesheet" href="<?= BASEURL ?>/assets/css/bookpark.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>
<body>

<section class="bookpark-page">
  <div class="bookpark-container">

    <!-- HEADER -->
    <div class="bookpark-header">
      <span class="bookpark-label">SAVE YOUR PARK NOW!</span>
      <h1>Exploring Parking Space Near You</h1>
      <p>
        Tidak perlu berkeliling mencari parkir. SPARK memandu Anda menemukan ruang parkir
        terdekat dan memastikan tempat tersedia sebelum Anda tiba.
      </p>
    </div>

    <!-- CONTENT -->
    <div class="bookpark-content">

      <!-- LEFT LIST -->
      <div class="bookpark-list">

        <?php if (empty($tempat_parkir)): ?>
          <div class="bookpark-empty">
            <p style="font-size: 24px;">😔</p>
            <p style="font-weight: 600; color: #333; margin-top: 12px;">Belum ada tempat parkir tersedia</p>
            <p style="font-size: 13px; margin-top: 8px;">Silakan coba lagi nanti atau hubungi admin.</p>
          </div>
        <?php else: ?>

          <?php foreach ($tempat_parkir as $parkir): ?>
            <!-- CARD -->
            <div class="bookpark-card" 
                 data-lat="<?= htmlspecialchars($parkir['latitude'] ?? '') ?>" 
                 data-lng="<?= htmlspecialchars($parkir['longitude'] ?? '') ?>" 
                 data-id="<?= $parkir['id_tempat'] ?>">

                 <div class="bookpark-card-image">
    <?php
        // Tentukan path gambar (TANPA file_exists)
        $imagePath = BASEURL . '/assets/img/' . ($parkir['foto_tempat'] ?: 'default.jpg');
        $available = $parkir['available_slots'] ?? 0;
    ?>

    <img 
        src="<?= $imagePath ?>"
        loading="lazy"
        decoding="async"
        alt="<?= htmlspecialchars($parkir['nama_tempat']) ?>"
        onerror="this.src='<?= BASEURL ?>/assets/img/default.jpg'"
    >

    <?php if ($available > 0): ?>
        <span class="bookpark-badge-available">
            <?= $available ?> Available
        </span>
    <?php else: ?>
        <span class="bookpark-badge-full">Full</span>
    <?php endif; ?>
</div>


              <div class="bookpark-card-info">
                <h3><?= htmlspecialchars($parkir['nama_tempat']) ?></h3>
                <div class="bookpark-address">
                  <?= htmlspecialchars($parkir['alamat_tempat']) ?>
                </div>

                <!-- Fasilitas -->
                <ul class="bookpark-facilities">
                  <?php foreach (getFacilityIcons($parkir) as $facility): ?>
                    <li><?= $facility ?></li>
                  <?php endforeach; ?>
                </ul>

                <!-- Rating -->
                <div class="bookpark-rating">
                  ⭐ <?= number_format($parkir['avg_rating'], 1) ?> 
                  <span>(<?= $parkir['total_reviews'] ?> ulasan)</span>
                </div>

                <!-- Harga -->
                <div class="bookpark-price">
                  <?= formatRupiah($parkir['harga_per_jam']) ?> / jam
                </div>

                <!-- Jam Operasional -->
                <div class="bookpark-time">
                  🕒 <?= formatTime($parkir['jam_buka']) ?> - <?= formatTime($parkir['jam_tutup']) ?>
                </div>

                <!-- Action Buttons -->
                <div class="bookpark-actions">
                  <?php if ($available > 0): ?>
                    <a href="<?= BASEURL ?>/booking.php?id=<?= $parkir['id_tempat'] ?>" 
                       class="bookpark-btn-primary">
                      Book Now
                    </a>
                  <?php else: ?>
                    <button class="bookpark-btn-disabled" disabled>
                      Fully Booked
                    </button>
                  <?php endif; ?>
                  
                  <a href="<?= BASEURL ?>/detail.php?id=<?= $parkir['id_tempat'] ?>" 
                     class="bookpark-btn-outline">
                    See Details
                  </a>
                </div>
              </div>

            </div>
          <?php endforeach; ?>

        <?php endif; ?>

      </div>

      <!-- RIGHT MAP -->
      <div class="bookpark-map-card">
        <div id="bookpark-map"></div>
      </div>

    </div>
  </div>
</section>

<script>
// Prevent any JavaScript errors from breaking the page
try {
    // Initialize map centered on Yogyakarta
    const map = L.map('bookpark-map').setView([-7.7956, 110.3695], 13);

    // Add OpenStreetMap tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap',
        maxZoom: 19
    }).addTo(map);

    // Custom icon untuk marker parkir
    const parkingIcon = L.divIcon({
        className: 'custom-parking-icon',
        html: '<div style="background: #ffe100; border-radius: 50%; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; font-size: 20px; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3); cursor: pointer;">🅿️</div>',
        iconSize: [36, 36],
        iconAnchor: [18, 18]
    });

    // Array untuk menyimpan marker
    const markers = [];

    // Tambahkan marker dari data PHP
    <?php foreach ($tempat_parkir as $parkir): ?>
      <?php if (!empty($parkir['latitude']) && !empty($parkir['longitude'])): ?>
        (function() {
            const lat = parseFloat('<?= $parkir['latitude'] ?>');
            const lng = parseFloat('<?= $parkir['longitude'] ?>');
            
            if (!isNaN(lat) && !isNaN(lng)) {
                const marker = L.marker([lat, lng], { icon: parkingIcon }).addTo(map);
                
                // Popup content
                const popupContent = `
                    <div style="min-width: 220px; font-family: Arial, sans-serif;">
                        <h4 style="margin: 0 0 8px 0; font-size: 15px; font-weight: 700; color: #1e1e1e;">
                            <?= htmlspecialchars(str_replace("'", "\\'", $parkir['nama_tempat'])) ?>
                        </h4>
                        <p style="margin: 4px 0; font-size: 12px; color: #666;">
                            📍 <?= htmlspecialchars(str_replace("'", "\\'", substr($parkir['alamat_tempat'], 0, 50))) ?><?= strlen($parkir['alamat_tempat']) > 50 ? '...' : '' ?>
                        </p>
                        <p style="margin: 8px 0; font-weight: 700; color: #ffe100; font-size: 14px;">
                            <?= formatRupiah($parkir['harga_per_jam']) ?>/jam
                        </p>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin: 8px 0;">
                            <span style="font-size: 12px;">
                                ⭐ <?= number_format($parkir['avg_rating'], 1) ?> (<?= $parkir['total_reviews'] ?>)
                            </span>
                            <span style="font-size: 12px; color: <?= ($parkir['available_slots'] ?? 0) > 0 ? '#4caf50' : '#f44336' ?>; font-weight: 600;">
                                <?= ($parkir['available_slots'] ?? 0) > 0 ? ($parkir['available_slots'] ?? 0) . ' Tersedia' : 'Penuh' ?>
                            </span>
                        </div>
                        <?php if (($parkir['available_slots'] ?? 0) > 0): ?>
                        <a href="<?= BASEURL ?>/booking.php?id=<?= $parkir['id_tempat'] ?>" 
                           style="display: block; margin-top: 10px; padding: 8px 16px; background: #ffe100; color: #1e1e1e; text-decoration: none; border-radius: 8px; font-size: 13px; font-weight: 600; text-align: center;">
                            Book Now →
                        </a>
                        <?php endif; ?>
                    </div>
                `;
                
                marker.bindPopup(popupContent);
                
                markers.push({
                    marker: marker,
                    id: <?= $parkir['id_tempat'] ?>
                });
            }
        })();
      <?php endif; ?>
    <?php endforeach; ?>

    // Event listener untuk highlight marker saat card di-hover
    document.querySelectorAll('.bookpark-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
    const id = parseInt(this.dataset.id);
    const markerObj = markers.find(m => m.id === id);
    if (markerObj) {
        markerObj.marker.openPopup();
    }
});
        
        // Click event untuk zoom ke marker
        card.addEventListener('click', function(e) {
            // Jangan zoom jika yang diklik adalah button atau link
            if (e.target.tagName === 'A' || e.target.tagName === 'BUTTON') {
                return;
            }
            
            const id = parseInt(this.dataset.id);
            const markerObj = markers.find(m => m.id === id);
            if (markerObj) {
                map.setView(markerObj.marker.getLatLng(), 16, {
                    animate: true,
                    duration: 0.8
                });
                markerObj.marker.openPopup();
            }
        });
    });

    // Fit map bounds untuk menampilkan semua marker
    if (markers.length > 0) {
        const group = L.featureGroup(markers.map(m => m.marker));
        map.fitBounds(group.getBounds().pad(0.1));
    }

    // Jika hanya ada 1 marker, set zoom lebih dekat
    if (markers.length === 1) {
        map.setZoom(15);
    }
    
} catch (error) {
    console.error('Map initialization error:', error);
}
</script>

</body>
</html>