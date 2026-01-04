<?php
/**
 * BOOKING.PHP
 * Main booking/payment page
 * User confirms parking reservation and enters payment details
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../functions/auth.php';

startSession();

$pdo = getDBConnection();

// Get parking ID from URL
$id_tempat = $_GET['id'] ?? null;

if (!$id_tempat) {
    header('Location: ' . BASEURL . '/pages/dashboard.php');
    exit;
}

// Fetch parking details
$sql = "
    SELECT 
        tp.id_tempat,
        tp.nama_tempat,
        tp.alamat_tempat,
        tp.harga_per_jam,
        tp.jam_buka,
        tp.jam_tutup,
        tp.foto_tempat,
        COUNT(CASE WHEN sp.status_slot = 'available' THEN 1 END) as slot_tersedia
    FROM tempat_parkir tp
    LEFT JOIN slot_parkir sp ON tp.id_tempat = sp.id_tempat
    WHERE tp.id_tempat = :id_tempat
    GROUP BY tp.id_tempat
";

$stmt = $pdo->prepare($sql);
$stmt->execute(['id_tempat' => $id_tempat]);
$parking = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$parking) {
    header('Location: ' . BASEURL . '/pages/dashboard.php');
    exit;
}

// Get available vehicle types
$sql = "
    SELECT DISTINCT jk.nama_jenis
    FROM slot_parkir sp
    INNER JOIN jenis_kendaraan jk ON sp.id_jenis = jk.id_jenis
    WHERE sp.id_tempat = :id_tempat 
    AND sp.status_slot = 'available'
    ORDER BY jk.nama_jenis ASC
";

$stmt = $pdo->prepare($sql);
$stmt->execute(['id_tempat' => $id_tempat]);
$vehicleTypes = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Get user data if logged in
$user = isLoggedIn() ? getCurrentUser() : null;

// Default booking data (can be customized later)
$bookingData = [
    'date' => date('Y-m-d'),
    'time_start' => '09:00',
    'duration' => 2, // hours
];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking - <?= htmlspecialchars($parking['nama_tempat']) ?> | SPARK</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Styles -->
    <link rel="stylesheet" href="<?= BASEURL ?>/assets/css/booking-page.css">
</head>
<body>
    <!-- Checkout Navbar -->
    <nav class="checkout-navbar">
        <div class="checkout-navbar-content">
            <div class="navbar-logo">
                <img src="<?= BASEURL ?>/assets/img/logo.png" alt="SPARK" class="logo-image">
            </div>
            <div class="navbar-secure">
                <i class="fas fa-lock"></i>
                <span>Secure Checkout</span>
            </div>
        </div>
    </nav>

    <div class="booking-container">
        <!-- Header -->
        <header class="booking-header">
            <a href="<?= BASEURL ?>/pages/dashboard.php" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                Back to Dashboard
            </a>
            <h1>Complete Your Booking</h1>
        </header>

        <!-- Main Content -->
        <div class="booking-content">
            <!-- Left Column - Form -->
            <div class="booking-form-section">
                <?php include __DIR__ . '/../includes/booking-form.php'; ?>
            </div>

            <!-- Right Column - Summary -->
            <div class="booking-summary-section">
                <?php include __DIR__ . '/../includes/booking-summary.php'; ?>
            </div>
        </div>
        
        <!-- Minimal Checkout Footer -->
        <footer class="checkout-footer">
            <div class="footer-links">
                <a href="#">Terms of Service</a>
                <span class="divider">•</span>
                <a href="#">Privacy Policy</a>
                <span class="divider">•</span>
                <a href="#">Help Center</a>
            </div>
            <div class="footer-copyright">
                &copy; <?= date('Y') ?> SPARK Parking. All rights reserved.
            </div>
        </footer>
    </div>

    <script src="<?= BASEURL ?>/assets/js/booking-page.js"></script>
</body>
</html>
