<?php
/**
 * CREATE-BOOKING.PHP
 * Handles booking form submission
 * Creates booking record with 'pending' status
 * Generates booking_id and qr_token
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../functions/auth.php';

startSession();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASEURL . '/pages/dashboard.php');
    exit;
}

$pdo = getDBConnection();
$user = isLoggedIn() ? getCurrentUser() : null;

// Get form data
$id_tempat = $_POST['id_tempat'] ?? null;

// CRITICAL: Use session/DB data for contact info if logged in
// This prevents user from tampering with read-only fields via HTML manipulation
if ($user) {
    $email = $user['email'];
    $nama_lengkap = $user['nama_pengguna'];
    $nomor_telepon = $user['nomor_telepon'] ?? '';
    $id_pengguna = $user['id_pengguna'];
} else {
    // Fallback for guest (if enabled later) or redirect
    $_SESSION['error'] = 'Please login to continue.';
    header('Location: ' . BASEURL . '/pages/login.php');
    exit;
}

$jenis_kendaraan = trim($_POST['jenis_kendaraan'] ?? '');
$nomor_plat = trim($_POST['nomor_plat'] ?? '');
$tanggal_booking = $_POST['tanggal_booking'] ?? '';
$waktu_mulai = $_POST['waktu_mulai'] ?? '';
$durasi_jam = (int)($_POST['durasi_jam'] ?? 0);
$harga_per_jam = (float)($_POST['harga_per_jam'] ?? 0);

// Validate required fields
if (!$id_tempat || !$jenis_kendaraan || !$tanggal_booking || !$waktu_mulai || !$durasi_jam) {
    $_SESSION['error'] = 'Please fill in all required fields.';
    header('Location: ' . BASEURL . '/pages/booking.php?id=' . $id_tempat);
    exit;
}

// Ensure vehicle info is valid
if (empty($jenis_kendaraan)) {
     $_SESSION['error'] = 'Vehicle type is required.';
     header('Location: ' . BASEURL . '/pages/booking.php?id=' . $id_tempat);
     exit;
}

// Calculate end time and total price
$waktu_selesai = date('H:i', strtotime($waktu_mulai) + ($durasi_jam * 3600));
$total_harga = $harga_per_jam * $durasi_jam;

// Generate unique QR token
$qr_token = bin2hex(random_bytes(32)); // 64 character hex string

try {
    // Insert booking record
    $sql = "
        INSERT INTO booking_parkir (
            id_tempat, id_pengguna, email, nama_lengkap, nomor_telepon,
            jenis_kendaraan, nomor_plat, tanggal_booking, waktu_mulai, waktu_selesai,
            durasi_jam, harga_per_jam, total_harga, status_booking, qr_token
        ) VALUES (
            :id_tempat, :id_pengguna, :email, :nama_lengkap, :nomor_telepon,
            :jenis_kendaraan, :nomor_plat, :tanggal_booking, :waktu_mulai, :waktu_selesai,
            :durasi_jam, :harga_per_jam, :total_harga, 'pending', :qr_token
        )
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'id_tempat' => $id_tempat,
        'id_pengguna' => $id_pengguna,
        'email' => $email,
        'nama_lengkap' => $nama_lengkap,
        'nomor_telepon' => $nomor_telepon ?: null,
        'jenis_kendaraan' => $jenis_kendaraan,
        'nomor_plat' => $nomor_plat ?: null,
        'tanggal_booking' => $tanggal_booking,
        'waktu_mulai' => $waktu_mulai,
        'waktu_selesai' => $waktu_selesai,
        'durasi_jam' => $durasi_jam,
        'harga_per_jam' => $harga_per_jam,
        'total_harga' => $total_harga,
        'qr_token' => $qr_token
    ]);

    $booking_id = $pdo->lastInsertId();

    // Store booking data in session
    $_SESSION['booking_data'] = [
        'booking_id' => $booking_id,
        'qr_token' => $qr_token,
        'total_harga' => $total_harga,
        'status' => 'pending'
    ];

    $_SESSION['success'] = 'Booking created successfully! Waiting for payment.';

    // Redirect to payment waiting page (to be created later)
    // For now, redirect back to dashboard with success message
    header('Location: ' . BASEURL . '/pages/dashboard.php');
    exit;

} catch (PDOException $e) {
    error_log('Booking creation error: ' . $e->getMessage());
    $_SESSION['error'] = 'Failed to create booking. Please try again.';
    header('Location: ' . BASEURL . '/pages/booking.php?id=' . $id_tempat);
    exit;
}
