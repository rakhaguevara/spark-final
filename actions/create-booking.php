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

// Get form data
$id_tempat = $_POST['id_tempat'] ?? null;
$email = trim($_POST['email'] ?? '');
$nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
$nomor_telepon = trim($_POST['nomor_telepon'] ?? '');
$jenis_kendaraan = trim($_POST['jenis_kendaraan'] ?? '');
$nomor_plat = trim($_POST['nomor_plat'] ?? '');
$tanggal_booking = $_POST['tanggal_booking'] ?? '';
$waktu_mulai = $_POST['waktu_mulai'] ?? '';
$durasi_jam = (int)($_POST['durasi_jam'] ?? 0);
$harga_per_jam = (float)($_POST['harga_per_jam'] ?? 0);

// Validate required fields
if (!$id_tempat || !$email || !$nama_lengkap || !$jenis_kendaraan || !$tanggal_booking || !$waktu_mulai || !$durasi_jam) {
    $_SESSION['error'] = 'Please fill in all required fields.';
    header('Location: ' . BASEURL . '/pages/booking.php?id=' . $id_tempat);
    exit;
}

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = 'Please enter a valid email address.';
    header('Location: ' . BASEURL . '/pages/booking.php?id=' . $id_tempat);
    exit;
}

// Calculate end time and total price
$waktu_selesai = date('H:i', strtotime($waktu_mulai) + ($durasi_jam * 3600));
$total_harga = $harga_per_jam * $durasi_jam;

// Generate unique QR token
$qr_token = bin2hex(random_bytes(32)); // 64 character hex string

// Get user ID if logged in
$id_pengguna = isLoggedIn() ? getCurrentUser()['id_pengguna'] : null;

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
