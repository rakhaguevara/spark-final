<?php
// pages/dashboard.php

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../functions/auth.php';

startSession();

if (!isLoggedIn()) {
    header('Location: ' . BASEURL . '/pages/login.php');
    exit;
}

$user = getCurrentUser();
$pdo = getDBConnection();

$sql = "
    SELECT 
        tp.id_tempat,
        tp.nama_tempat,
        tp.alamat_tempat,
        tp.latitude,
        tp.longitude,
        tp.harga_per_jam,
        tp.jam_buka,
        tp.jam_tutup,
        tp.foto_tempat,
        COUNT(sp.id_slot) as total_slot,
        SUM(CASE WHEN sp.status_slot = 'available' THEN 1 ELSE 0 END) as slot_tersedia,
        COALESCE(AVG(ut.rating), 4.5) as avg_rating,
        COUNT(DISTINCT ut.id_ulasan) as total_review
    FROM tempat_parkir tp
    LEFT JOIN slot_parkir sp ON tp.id_tempat = sp.id_tempat
    LEFT JOIN ulasan_tempat ut ON tp.id_tempat = ut.id_tempat
    GROUP BY tp.id_tempat
    ORDER BY tp.nama_tempat ASC
";

$stmt = $pdo->query($sql);
$parkingSpots = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fungsi helper untuk format harga
function formatPrice($price) {
    return 'Rp ' . number_format($price, 0, ',', '.');
}

// Fungsi untuk mendapatkan fasilitas
function getParkingFacilities($jam_buka, $jam_tutup) {
    $facilities = [];
    
    // Cek 24 jam
    if ($jam_buka == '00:00:00' && $jam_tutup == '23:59:59') {
        $facilities[] = ['icon' => 'clock', 'text' => '24 Hours'];
    }
    
    // Fasilitas standar
    $facilities[] = ['icon' => 'camera', 'text' => 'CCTV'];
    $facilities[] = ['icon' => 'shield-alt', 'text' => 'Secure'];
    $facilities[] = ['icon' => 'parking', 'text' => 'Covered'];
    
    return $facilities;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Find Parking | SPARK</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="icon" type="image/png" href="<?= BASEURL ?>/assets/img/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <link rel="stylesheet" href="<?= BASEURL ?>/assets/css/dashboard-user.css">
</head>

<body>

    <!-- NAVBAR -->
    <nav class="dashboard-navbar">
        <a href="<?= BASEURL ?>" class="brand-wrapper">
            <img src="<?= BASEURL ?>/assets/img/logo.png" alt="Spark Logo">
            SPARK
        </a>

        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Where are you going?">
        </div>

        <div class="user-actions">
            <button class="icon-btn" title="Notifications"><i class="fas fa-bell"></i></button>
            <button class="icon-btn" title="Settings"><i class="fas fa-cog"></i></button>
            <div class="profile-chip">
                <div class="profile-avatar">
                    <?= strtoupper(substr($user['nama_pengguna'] ?? 'U', 0, 1)) ?>
                </div>
                <span><?= htmlspecialchars($user['nama_pengguna'] ?? 'User') ?></span>
            </div>
        </div>
    </nav>

    <!-- MAIN CONTAINER -->
    <div class="dashboard-container">

        <!-- SIDEBAR -->
        <aside class="dashboard-sidebar">
            <ul class="sidebar-menu">
                <li><a href="#" class="active"><i class="fas fa-th-large"></i> Dashboard</a></li>
                <li><a href="#"><i class="fas fa-map-marked-alt"></i> Find Parking</a></li>
                <li><a href="#"><i class="fas fa-history"></i> My Booking</a></li>
                <li><a href="#"><i class="fas fa-wallet"></i> Wallet</a></li>
                <li><a href="#"><i class="fas fa-user"></i> Profile</a></li>
            </ul>

            <div class="sidebar-logout">
                <a href="<?= BASEURL ?>/logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="dashboard-main">

            <!-- FILTERS BAR -->
            <div class="filters-bar">
                <button class="filter-btn active">
                    <i class="fas fa-filter"></i>
                    Filters
                </button>
                <button class="filter-btn">
                    <i class="fas fa-car"></i>
                    Vehicle Type
                </button>
                <button class="filter-btn">
                    Self Park
                </button>
                <button class="filter-btn">
                    Garage - Covered
                </button>
                
                <div class="sort-dropdown">
                    <select id="sortSelect">
                        <option value="relevance">Sort by Relevance</option>
                        <option value="price-low">Price: Low to High</option>
                        <option value="price-high">Price: High to Low</option>
                        <option value="distance">Distance</option>
                        <option value="rating">Rating</option>
                    </select>
                </div>
            </div>

            <!-- CONTENT GRID -->
            <div class="content-grid">

                <!-- LEFT SIDE - PARKING LIST -->
                <div class="parking-container" id="parkingList">
                    <?php if (!empty($parkingSpots)): ?>
                        <?php foreach ($parkingSpots as $spot): ?>
                            <?php 
                            $facilities = getParkingFacilities($spot['jam_buka'], $spot['jam_tutup']);
                            $slotsAvailable = $spot['slot_tersedia'] ?? 0;
                            ?>
                            
                            <div class="parking-card" 
                                 data-id="<?= $spot['id_tempat'] ?>"
                                 data-lat="<?= htmlspecialchars($spot['latitude'] ?? '') ?>" 
                                 data-lng="<?= htmlspecialchars($spot['longitude'] ?? '') ?>"
                                 data-price="<?= $spot['harga_per_jam'] ?>"
                                 data-rating="<?= number_format($spot['avg_rating'], 1) ?>">
                                
                                <!-- Card Image -->
                                <div class="parking-card-image">
                                    <?php
                                    $imagePath = BASEURL . '/assets/img/content-1.png';
                                    if (!empty($spot['foto_tempat'])) {
                                        $checkPath = $_SERVER['DOCUMENT_ROOT'] . str_replace(BASEURL, '', BASEURL) . '/assets/img/park/' . $spot['foto_tempat'];
                                        if (file_exists($checkPath)) {
                                            $imagePath = BASEURL . '/assets/img/park/' . htmlspecialchars($spot['foto_tempat']);
                                        }
                                    }
                                    ?>
                                    <img src="<?= $imagePath ?>" 
                                         alt="<?= htmlspecialchars($spot['nama_tempat']) ?>"
                                         onerror="this.src='<?= BASEURL ?>/assets/img/content-1.png'">
                                    
                                    <?php if ($slotsAvailable > 0): ?>
                                    <div class="parking-badge">
                                        <i class="fas fa-check-circle"></i>
                                        <?= $slotsAvailable ?> spots left
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Card Content -->
                                <div class="parking-card-content">
                                    <div class="parking-card-header">
                                        <h3><?= htmlspecialchars($spot['nama_tempat']) ?></h3>
                                        
                                        <div class="parking-location">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <span><?= htmlspecialchars($spot['alamat_tempat']) ?></span>
                                        </div>
                                    </div>

                                    <!-- Facilities -->
                                    <div class="parking-facilities">
                                        <?php foreach ($facilities as $facility): ?>
                                            <span class="facility-tag">
                                                <i class="fas fa-<?= $facility['icon'] ?>"></i>
                                                <?= $facility['text'] ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>

                                    <!-- Meta Info -->
                                    <div class="parking-meta">
                                        <div class="parking-rating">
                                            <span>⭐</span>
                                            <span><?= number_format($spot['avg_rating'], 1) ?></span>
                                            <?php if ($spot['total_review'] > 0): ?>
                                                <span style="color: #999;">(<?= $spot['total_review'] ?>)</span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <span style="color: #ddd;">•</span>
                                        
                                        <div class="parking-distance">
                                            <i class="fas fa-walking"></i>
                                            <span>9 min (0.4 mi)</span>
                                        </div>
                                    </div>

                                    <!-- Description -->
                                    <div class="parking-description">
                                        Tempat parkir yang aman dan nyaman dengan fasilitas lengkap. Tersedia <?= $spot['total_slot'] ?> slot parkir.
                                    </div>

                                    <!-- Footer -->
                                    <div class="parking-footer">
                                        <div class="parking-price">
                                            <span class="price-amount"><?= formatPrice($spot['harga_per_jam']) ?></span>
                                            <span class="price-subtitle">per jam</span>
                                        </div>
                                        
                                        <div class="parking-actions">
                                            <button class="btn-details" 
                                                    onclick="openDetailPopup(<?= $spot['id_tempat'] ?>)">
                                                Details
                                            </button>
                                            <a href="<?= BASEURL ?>/pages/booking.php?id=<?= $spot['id_tempat'] ?>" 
                                               class="btn-book">
                                                Book Now
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-car-side"></i>
                            <p>Belum ada data tempat parkir.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- RIGHT SIDE - MAP -->
                <div class="map-container">
                    <div id="map"></div>
                </div>

            </div>
        </main>
    </div>

    <!-- DETAIL POPUP MODAL -->
    <div class="detail-popup" id="detailPopup">
        <div class="detail-popup-content">
            <button class="detail-close" onclick="closeDetailPopup()">×</button>
            
            <!-- Image Slider -->
            <div class="detail-image-slider">
                <button class="slider-nav prev" onclick="prevImage()">‹</button>
                <button class="slider-nav next" onclick="nextImage()">›</button>
                <img id="detailImage" src="" alt="">
                <div class="image-counter" id="imageCounter">1/5</div>
            </div>

            <!-- Detail Info -->
            <div class="detail-info">
                <h2 id="detailTitle">Parking Name</h2>
                <div class="detail-meta">
                    <i class="fas fa-walking"></i>
                    <span id="detailDistance">9 minute walk (0.4 mi)</span>
                </div>

                <!-- Suggestion Box -->
                <div class="detail-suggestion">
                    <p><strong>We suggest you book now.</strong></p>
                    <p>We only have <strong id="detailSlots">5 spots</strong> remaining here during the times you selected.</p>
                </div>

                <!-- Address -->
                <div class="parking-location" style="margin-bottom: 20px;">
                    <i class="fas fa-map-marker-alt"></i>
                    <span id="detailAddress">Address here</span>
                </div>

                <!-- Facilities -->
                <div class="detail-facilities">
                    <h3>Facilities</h3>
                    <div class="parking-facilities" id="detailFacilities">
                        <!-- Facilities will be inserted here -->
                    </div>
                </div>

                <!-- Booking Info -->
                <div class="detail-booking">
                    <h3>Parking Reservation</h3>
                    <div class="booking-time" id="detailTime">Today 4:00 PM - 7:00 PM</div>
                    <div class="booking-duration">
                        <span><i class="far fa-clock"></i> 3 hours</span>
                        <span><i class="fas fa-sign-in-alt"></i> No In & Out</span>
                    </div>

                    <div class="detail-price">
                        <span class="detail-price-label">Parking Reservation</span>
                        <div>
                            <div class="detail-price-amount" id="detailPrice">Rp 15.000</div>
                            <div class="detail-price-subtitle">Subtotal</div>
                        </div>
                    </div>

                    <button class="detail-book-btn" id="detailBookBtn">
                        Book Now
                    </button>
                </div>

                <!-- Features -->
                <div class="detail-features">
                    <div class="feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Free Cancellation</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Guaranteed parking</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        const parkingData = <?= json_encode($parkingSpots) ?>;
        const BASEURL = '<?= BASEURL ?>';

        // Initialize Map
        let map = L.map('map', {
            zoomControl: true,
            scrollWheelZoom: true
        }).setView([-7.7956, 110.3695], 13);

        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            attribution: '© OpenStreetMap © CARTO',
            maxZoom: 19
        }).addTo(map);

        const markers = {};

        // Render markers
        parkingData.forEach(spot => {
            if (spot.latitude && spot.longitude) {
                const priceMarker = L.divIcon({
                    className: 'custom-marker',
                    html: `<div style="
                        background: #0066FF;
                        color: white;
                        padding: 6px 12px;
                        border-radius: 20px;
                        font-weight: 700;
                        font-size: 13px;
                        box-shadow: 0 2px 8px rgba(0,0,0,0.3);
                        white-space: nowrap;
                        border: 2px solid white;
                        cursor: pointer;
                    ">${formatRupiah(spot.harga_per_jam)}</div>`,
                    iconSize: [80, 30],
                    iconAnchor: [40, 15]
                });

                const marker = L.marker([spot.latitude, spot.longitude], {icon: priceMarker})
                    .addTo(map);
                
                marker.on('click', function() {
                    openDetailPopup(spot.id_tempat);
                });
                
                markers[spot.id_tempat] = marker;
            }
        });

        function formatRupiah(amount) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
        }

        // Detail Popup Functions
        let currentImageIndex = 0;
        let currentSpotId = null;

        function openDetailPopup(spotId) {
            const spot = parkingData.find(s => s.id_tempat == spotId);
            if (!spot) return;

            currentSpotId = spotId;
            currentImageIndex = 0;

            // Set data
            const imagePath = spot.foto_tempat 
                ? `${BASEURL}/assets/img/park/${spot.foto_tempat}`
                : `${BASEURL}/assets/img/content-1.png`;

            document.getElementById('detailImage').src = imagePath;
            document.getElementById('detailTitle').textContent = spot.nama_tempat;
            document.getElementById('detailAddress').textContent = spot.alamat_tempat;
            document.getElementById('detailSlots').textContent = spot.slot_tersedia + ' spots';
            document.getElementById('detailPrice').textContent = formatRupiah(spot.harga_per_jam);
            
            // Set facilities
            const facilitiesHTML = `
                <span class="facility-tag"><i class="fas fa-clock"></i> 24 Hours</span>
                <span class="facility-tag"><i class="fas fa-camera"></i> CCTV</span>
                <span class="facility-tag"><i class="fas fa-shield-alt"></i> Secure</span>
                <span class="facility-tag"><i class="fas fa-parking"></i> Covered</span>
            `;
            document.getElementById('detailFacilities').innerHTML = facilitiesHTML;

            // Set booking link
            document.getElementById('detailBookBtn').onclick = function() {
                window.location.href = `${BASEURL}/pages/booking.php?id=${spotId}`;
            };

            document.getElementById('detailPopup').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeDetailPopup() {
            document.getElementById('detailPopup').classList.remove('active');
            document.body.style.overflow = '';
        }

        function prevImage() {
            // Implement image navigation
            console.log('Previous image');
        }

        function nextImage() {
            // Implement image navigation
            console.log('Next image');
        }

        // Close popup when clicking outside
        document.getElementById('detailPopup').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDetailPopup();
            }
        });

        // Search functionality
        const searchInput = document.getElementById('searchInput');
        const parkingCards = document.querySelectorAll('.parking-card');

        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            
            parkingCards.forEach(card => {
                const title = card.querySelector('h3').textContent.toLowerCase();
                const location = card.querySelector('.parking-location span').textContent.toLowerCase();
                
                if (title.includes(searchTerm) || location.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });

        // Sort functionality
        const sortSelect = document.getElementById('sortSelect');
        const parkingContainer = document.getElementById('parkingList');

        sortSelect.addEventListener('change', function() {
            const sortValue = this.value;
            const cardsArray = Array.from(parkingCards);
            
            cardsArray.sort((a, b) => {
                switch(sortValue) {
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

        // Fit bounds
        if (parkingData.length > 0) {
            const bounds = L.latLngBounds(
                parkingData
                    .filter(s => s.latitude && s.longitude)
                    .map(s => [s.latitude, s.longitude])
            );
            if (bounds.isValid()) {
                map.fitBounds(bounds, { padding: [50, 50] });
            }
        }
    </script>
</body>
</html>