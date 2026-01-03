<?php
/**
 * TEST MODE: Generate Ticket Without Payment
 * For testing purposes only - bypasses payment gateway
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../functions/auth.php';

// Start session
startSession();

// Check if user is logged in
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user = getCurrentUser();

// Validate request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get and validate input
$id_parkir = $_POST['id_parkir'] ?? '';
$id_jenis_kendaraan = $_POST['id_jenis_kendaraan'] ?? '';
$nomor_plat = trim($_POST['nomor_plat'] ?? '');
$waktu_mulai = $_POST['waktu_mulai'] ?? '';
$waktu_selesai = $_POST['waktu_selesai'] ?? '';
$durasi = $_POST['durasi'] ?? '';
$total_harga = $_POST['total_harga'] ?? '';

// Validation
if (empty($id_parkir) || empty($id_jenis_kendaraan) || empty($nomor_plat) || 
    empty($waktu_mulai) || empty($waktu_selesai) || empty($durasi) || empty($total_harga)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

try {
    $pdo = getDBConnection();
    $pdo->beginTransaction();
    
    // Generate booking ID
    $booking_id = 'BKG-' . strtoupper(substr(uniqid(), -10));
    
    // Insert booking
    $stmt = $pdo->prepare("
        INSERT INTO booking_parkir (
            booking_id, id_pengguna, id_parkir, id_jenis_kendaraan,
            nomor_plat, waktu_mulai, waktu_selesai, durasi,
            total_harga, status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'confirmed')
    ");
    
    $stmt->execute([
        $booking_id,
        $user['id_pengguna'],
        $id_parkir,
        $id_jenis_kendaraan,
        $nomor_plat,
        $waktu_mulai,
        $waktu_selesai,
        $durasi,
        $total_harga
    ]);
    
    // Generate ticket ID
    $ticket_id = 'TKT-' . strtoupper(substr(uniqid(), -10));
    
    // Hash vehicle plate
    $secret_salt = defined('SECRET_SALT') ? SECRET_SALT : 'default-salt-change-me';
    $vehicle_plate_hash = hash('sha256', $nomor_plat . $secret_salt);
    $vehicle_plate_hint = substr($nomor_plat, -4);
    
    // Insert ticket
    $stmt = $pdo->prepare("
        INSERT INTO tickets (
            ticket_id, booking_id, id_pengguna,
            vehicle_plate_hash, vehicle_plate_hint, ticket_status
        ) VALUES (?, ?, ?, ?, ?, 'active')
    ");
    
    $stmt->execute([
        $ticket_id,
        $booking_id,
        $user['id_pengguna'],
        $vehicle_plate_hash,
        $vehicle_plate_hint
    ]);
    
    // Generate initial QR token
    $qr_token = bin2hex(random_bytes(32));
    $expires_at = date('Y-m-d H:i:s', time() + 10);
    
    $stmt = $pdo->prepare("
        INSERT INTO qr_sessions (ticket_id, qr_token, expires_at)
        VALUES (?, ?, ?)
    ");
    $stmt->execute([$ticket_id, $qr_token, $expires_at]);
    
    // Commit transaction
    $pdo->commit();
    
    // Return success
    echo json_encode([
        'success' => true,
        'booking_id' => $booking_id,
        'ticket_id' => $ticket_id,
        'qr_token' => $qr_token,
        'message' => 'Ticket generated successfully (TEST MODE)'
    ]);
    
} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Test ticket generation error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
