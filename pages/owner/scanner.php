<?php
/**
 * OWNER SCANNER PAGE
 * QR Code scanner for parking entry/exit/stay confirmation
 */

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../functions/auth.php';

// Start session
startSession();

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: ' . BASEURL . '/pages/login.php');
    exit;
}

// Get user
$user = getCurrentUser();

// Helper function to get first 2 words of name
function getShortName($fullName) {
    $words = explode(' ', trim($fullName));
    $shortName = implode(' ', array_slice($words, 0, 2));
    return $shortName;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>QR Scanner | SPARK</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="icon" type="image/png" href="<?= BASEURL ?>/assets/img/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASEURL ?>/assets/css/loading-overlay.css">
    <link rel="stylesheet" href="<?= BASEURL ?>/assets/css/dashboard-user.css">
    <link rel="stylesheet" href="<?= BASEURL ?>/assets/css/scanner.css">
</head>

<body>
    <!-- LOADING OVERLAY -->
    <div class="page-loader">
        <div class="loader-content">
            <div class="loader-logo">
                <img src="<?= BASEURL ?>/assets/img/logo.png" alt="SPARK">
                <span class="loader-logo-text">SPARK</span>
            </div>
            <div class="loader-spinner"></div>
            <div class="loader-text">Loading...</div>
        </div>
    </div>

    <!-- NAVBAR -->
    <nav class="dashboard-navbar">
        <a href="<?= BASEURL ?>/pages/dashboard.php" class="brand-wrapper">
            <img src="<?= BASEURL ?>/assets/img/logo.png" alt="Spark Logo">
            SPARK Scanner
        </a>

        <div class="user-actions">
            <div class="profile-chip">
                <div class="profile-avatar">
                    <?php if (!empty($user['profile_image'])): ?>
                        <img src="<?= BASEURL ?>/uploads/<?= htmlspecialchars($user['profile_image']) ?>" alt="Profile" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                    <?php else: ?>
                        <?= strtoupper(substr($user['nama_pengguna'] ?? 'U', 0, 1)) ?>
                    <?php endif; ?>
                </div>
                <span><?= htmlspecialchars(getShortName($user['nama_pengguna'] ?? 'User')) ?></span>
            </div>
        </div>
    </nav>

    <!-- MAIN CONTAINER -->
    <div class="scanner-container">
        <!-- SCANNER HEADER -->
        <div class="scanner-header">
            <h1><i class="fas fa-qrcode"></i> QR Code Scanner</h1>
            <p>Scan parking tickets for entry, exit, or stay confirmation</p>
        </div>

        <!-- SCAN TYPE SELECTOR -->
        <div class="scan-type-selector">
            <button class="scan-type-btn active" data-type="entry">
                <i class="fas fa-sign-in-alt"></i>
                <span>Entry</span>
            </button>
            <button class="scan-type-btn" data-type="exit">
                <i class="fas fa-sign-out-alt"></i>
                <span>Exit</span>
            </button>
            <button class="scan-type-btn" data-type="stay">
                <i class="fas fa-parking"></i>
                <span>Stay</span>
            </button>
        </div>

        <!-- SCANNER SECTION -->
        <div class="scanner-section">
            <div class="camera-container">
                <div id="qr-reader"></div>
                <div class="scanner-overlay">
                    <div class="scanner-frame"></div>
                </div>
            </div>
            
            <div class="scanner-status" id="scannerStatus">
                <i class="fas fa-camera"></i>
                <p>Ready to scan</p>
            </div>
        </div>

        <!-- BOOKING DETAILS (Hidden by default) -->
        <div class="booking-details-container" id="bookingDetails" style="display: none;">
            <div class="booking-details-header">
                <h3>Booking Details</h3>
                <button class="btn-close" id="closeDetails">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="booking-info" id="bookingInfo">
                <!-- Details will be populated by JavaScript -->
            </div>
            
            <div class="booking-actions">
                <button class="btn-cancel" id="cancelScan">Cancel</button>
                <button class="btn-confirm" id="confirmScan">Confirm Scan</button>
            </div>
        </div>

        <!-- RECENT SCANS -->
        <div class="recent-scans">
            <h3>Recent Scans</h3>
            <div class="scans-list" id="recentScans">
                <p class="empty-message">No recent scans</p>
            </div>
        </div>
    </div>

    <script>
        window.BASEURL = '<?= BASEURL ?>';
        window.USER_ID = <?= $user['id_pengguna'] ?? 0 ?>;
    </script>
    
    <!-- html5-qrcode Library -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    
    <!-- Scanner JavaScript -->
    <script src="<?= BASEURL ?>/assets/js/qr-scanner.js"></script>
    <script src="<?= BASEURL ?>/assets/js/page-loader.js"></script>
</body>
</html>
